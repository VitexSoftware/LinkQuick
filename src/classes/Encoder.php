<?php

namespace LQ;

use Ease\Page;
use Ease\Brick;

/**
 * Třída pro kodování url
 *
 * @author     Vitex <vitex@hippy.cz>
 * @copyright  Vitex@hippy.cz (G) 2009,2011
 * @package    LinkQuick
 * @subpackage Engine
 */
class Encoder extends Brick
{
    /**
     * Klicovy sloupec
     * @var string
     */
    public $myKeyColumn = 'id';

    /**
     * Sql tabulka objektu
     * @var string
     */
    public $myTable = 'entry';

    /**
     * ID aktualniho zaznamu
     * @var int
     */
    public $recordID = NULL;

    /**
     * Zakodovana adresa
     * @var string
     */
    public $encodedURL = NULL;

    /**
     * Adresa Odkazu
     * @var string
     */
    public $plainURL = NULL;

    /**
     * Zkracovač adres
     *
     * @param string $originalURL url ke zkrácení
     */
    function __construct($originalURL = NULL)
    {
        parent::__construct();
        if (!is_null($originalURL)) {
            $this->saveUrl($originalURL);
        }
    }

    /**
     * Vraci kod pro URL
     *
     * @param string $Url
     */
    function getCodeByUrl($Url)
    {
        $Result = $this->getColumnsFromMySQL(['id', 'code'],
            ['url' => $this->MyDbLink->EaseAddSlashes($Url)]);
        if (isset($Result[0]['code'])) {
            return $Result[0]['code'];
        }
    }

    /**
     * Nastavuje URL
     *
     * @param string $Url
     */
    function setURL($Url)
    {
        $this->plainURL = $Url;
    }

    /**
     * Vraci URL podle kodu
     *
     * @param string $code
     *
     * @return string url
     */
    public function getURLByCode($code = NULL, $domain = null)
    {
        if (!$code) {
            $code     = $this->getDataValue('code');
            $InObject = true;
        } else {
            $InObject = false;
        }
        if (is_null($domain)) {
            $domain = dirname(self::getDomain()).'/';
        }
        $url = $this->getColumnsFromMySQL(['id', 'url', 'ExpireDate', 'UNIX_TIMESTAMP(ExpireDate) AS Expire'],
            ['code' => $code, 'deleted' => 0, 'domain' => $this->MyDbLink->addSlashes($domain)]);
        if (isset($url[0]['url'])) {
            if ($InObject) {
                $this->setDataValue('url', $url[0]['url']);
                $this->setDataValue('id', $url[0]['id']);
                if ((int) $url[0]['Expire'] < time()) {
                    $this->setDataValue('Expired', true);
                }
            }
            return $url[0]['url'];
        } else {
            return NULL;
        }
    }

    /**
     * Set code
     *
     * @param string $code
     */
    function setCode($code)
    {
        $this->setDataValue('code', $code);
    }

    /**
     * Vraci vysledne URL
     * @return string
     */
    public function getShortCutURL()
    {
        return $this->getCode();
    }

    /**
     * Ulozi URL do databaze
     *
     * @param string $originalURL
     *
     * @return string
     */
    public function saveUrl($originalURL, $domain)
    {
        if ($domain[strlen($domain) - 1] != '/') {
            $domain .= '/';
        }

        $originalURL = $this->dbLink->EaseAddSlashes($originalURL);
        $this->setDataValue('code', self::getNextCode($domain));
        $this->setDataValue('domain', $domain);
        $this->setDataValue('url', $originalURL);
        $this->setDataValue('title', self::urlToTitle($originalURL));
        $this->setDataValue('created', 'NOW()');
        $this->setDataValue('ExpireDate', $this->getDataValue('ExpiryDate'));
        $this->setDataValue('owner', (int) $this->EaseShared->User->getUserID());

        $this->insertToMySQL();

        if ($this->dbLink->getNumRows()) {
            $this->encodedURL = $this->getDataValue('code');
            $this->addToLog('saveUrl: '.$originalURL.' -> http://'.$domain.'/'.$this->encodedURL.' ',
                'success');
            return 'http://'.$domain.'/'.$this->encodedURL;
        } else {
            $this->addToLog('saveUrl: '.$originalURL.' -> http://'.$domain.'/'.$this->encodedURL.' ',
                'error');
            return null;
        }
    }

    /**
     * Get remote Webpage title
     *
     * @param string $URL
     * @return string
     */
    static function urlToTitle($URL)
    {
        $title    = NULL;
        $ch       = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $pageHtml = curl_exec($ch);
        if (curl_errno($ch)) {
            $title = curl_error($ch);
        }
        curl_close($ch);
        if (strlen($pageHtml)) {
            libxml_use_internal_errors(true);
            $document = new DOMDocument;
            $document->loadHTML($pageHtml);
            $ts       = $document->getElementsByTagName("title");
            if ($ts->length > 0) {
                $title = $ts->item(0)->textContent;
            }
        }
        return $title;
    }

    /**
     * returns address base
     *
     * @return string
     */
    static function getDomain()
    {
        return str_replace(basename($_SERVER['SCRIPT_FILENAME']), '',
            str_replace('http://', '', Page::phpSelf()));
    }

    /**
     * Encode String
     *
     * @param string $stringToEncode plaintext
     * @return string encoded
     */
    static public function encode($stringToEncode)
    {
        return base_convert($stringToEncode, 10, 36);
    }

    /**
     * Returns Current url
     * @return string
     */
    public function getCode()
    {
        return $this->encodedURL;
    }

    /**
     * Vrací aktuální URL
     *
     * @return type
     */
    public function getURL()
    {
        return $this->plainURL;
    }

    /**
     * Usage counter incerase
     *
     * @param int $recordID
     */
    public function updateCounter($recordID = NULL)
    {
        if (!$recordID) {
            $recordID = $this->getMyKey();
        }
        $this->MyDbLink->ExeQuery('UPDATE entry SET used = (used+1) WHERE id= '.$recordID);
    }

    /**
     * Vrací pole všech domén k dispozici
     *
     * @return array
     */
    public static function getDomainList()
    {
        return \Ease\Shared::db()->queryTo2DArray('SELECT domain FROM entry GROUP BY domain ORDER BY domain');
    }

    /**
     * Return next unused code
     *
     * @param  string $domain
     * @return string
     */
    public static function getNextCode($domain)
    {
        $Counter = self::getCodeCount($domain);
        return self::encode($Counter);
    }

    /**
     * Give you number of codes for given domain
     *
     * @param string $domain
     * @return int codes for domain in database
     */
    public static function getCodeCount($domain)
    {
        return (int) \Ease\Shared::db()->queryToValue('SELECT count(*) FROM entry WHERE domain=\''.$domain.'\'');
    }
}