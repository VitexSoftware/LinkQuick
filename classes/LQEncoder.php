<?php

require_once 'Ease/EaseBase.php';

/**
 * Třída pro kodování url
 * 
 * @author     Vitex <vitex@hippy.cz>
 * @copyright  Vitex@hippy.cz (G) 2009,2011
 * @package    LinkQuick
 * @subpackage Engine
 */
class LQEncoder extends EaseBrick
{

    /**
     * Klicovy sloupec
     * @var string
     */
    public $MyKeyColumn = 'id';

    /**
     * Sql tabulka objektu
     * @var string
     */
    public $MyTable = 'entries';

    /**
     * ID aktualniho zaznamu
     * @var int
     */
    public $RecordID = NULL;

    /**
     * Zakodovana adresa
     * @var string
     */
    public $EncodedURL = NULL;

    /**
     * Adresa Odkazu
     * @var string
     */
    public $PlainURL = NULL;

    /**
     * Zkracovač adres
     * 
     * @param string $OriginalURL url ke zkrácení
     */
    function __construct($OriginalURL = NULL)
    {
        parent::__construct();
        if (!is_null($OriginalURL)) {
            $this->SaveUrl($OriginalURL);
        }
    }

    /**
     * Vraci kod pro URL
     * 
     * @param string $Url
     */
    function getCodeByUrl($Url)
    {
        $Result = $this->GetColumnsFromMySQL(array('id', 'code'), array('url' => $this->MyDbLink->EaseAddSlashes($Url)));
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
        $this->PlainURL = $Url;
    }

    /**
     * Vraci URL podle kodu
     * 
     * @param string $Code
     * 
     * @return string url
     */
    public function getURLByCode($Code = NULL, $Domain = null)
    {
        if (!$Code) {
            $Code = $this->GetDataValue('code');
            $InObject = true;
        } else {
            $InObject = false;
        }
        if (is_null($Domain)) {
            $Domain = dirname(self::getDomain()) . '/';
        }
        $Url = $this->GetColumnsFromMySQL(array('id', 'url', 'ExpireDate', 'UNIX_TIMESTAMP(ExpireDate) AS Expire'), array('code' => $Code, 'deleted' => 0, 'domain' => $this->MyDbLink->addSlashes($Domain)));
        if (isset($Url[0]['url'])) {
            if ($InObject) {
                $this->SetDataValue('url', $Url[0]['url']);
                $this->SetDataValue('id', $Url[0]['id']);
                if ((int) $Url[0]['Expire'] < time()) {
                    $this->SetDataValue('Expired', true);
                }
            }
            return $Url[0]['url'];
        } else {
            return NULL;
        }
    }

    /**
     * Nastaví kod
     * 
     * @param type $Code 
     */
    function setCode($Code)
    {
        $this->SetDataValue('code', $Code);
    }

    /**
     * Vraci vysledne URL
     * @return string
     */
    public function getShortCutURL()
    {
        return $this->GetCode();
    }

    /**
     * Ulozi URL do databaze
     * 
     * @param string $OriginalURL
     * 
     * @return string
     */
    public function saveUrl($OriginalURL, $Domain)
    {
        if($Domain[strlen($Domain)-1] != '/' ){
            $Domain .= '/';
        }
        
        $OriginalURL = $this->MyDbLink->EaseAddSlashes($OriginalURL);
        $this->setDataValue('code',self::getNextCode($Domain));
        $this->setDataValue('domain',$Domain);
        $this->setDataValue('url', $OriginalURL);
        $this->setDataValue('title', self::urlToTitle($OriginalURL));
        $this->setDataValue('created','NOW()');
        $this->setDataValue('ExpireDate',$this->GetDataValue('ExpiryDate'));
        $this->setDataValue('owner',(int) $this->EaseShared->User->GetUserID());
        
        $this->insertToMySQL();
        
        if ($this->MyDbLink->getNumRows()) {
            $this->EncodedURL = $this->getDataValue('code');
            $this->addToLog('saveUrl: '.$OriginalURL.' -> http://'.$Domain.'/'.$this->EncodedURL.' ','success');
            return 'http://'.$Domain.'/'.$this->EncodedURL;
        } else {
            $this->addToLog('saveUrl: '.$OriginalURL.' -> http://'.$Domain.'/'.$this->EncodedURL.' ','error');
            return null;
        }
    }

    /**
     * Vrací titulek stránky 
     * 
     * @param type $URL
     * @return type 
     */
    static function urlToTitle($URL)
    {
        $title = NULL;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $pageHtml = curl_exec($ch);
        if(curl_errno($ch)){
            $title = curl_error($ch);
        }
        curl_close($ch);
        if (strlen($pageHtml)) {
            libxml_use_internal_errors(true);
            $document = new DOMDocument;
            $document->loadHTML($pageHtml);
            $ts = $document->getElementsByTagName("title");
            if ($ts->length > 0) {
                $title = $ts->item(0)->textContent;
            }
        }
        return $title;
    }

    /**
     * Vrací základ adresy
     * 
     * @return string
     */
    static function getDomain()
    {
        return str_replace(basename($_SERVER['SCRIPT_FILENAME']), '', str_replace('http://', '', EasePage::phpSelf()));
    }

    /**
     * Zakoduje ID
     */
    static public function encode($StringToEncode)
    {
        return base_convert($StringToEncode, 10, 36);
    }

    /**
     * Vrací aktuální kod
     * @return type 
     */
    public function getCode()
    {
        return $this->EncodedURL;
    }

    /**
     * Vrací aktuální URL
     * 
     * @return type 
     */
    public function getURL()
    {
        return $this->PlainURL;
    }

    /**
     * Zvedne počítadlo použití
     * 
     * @param type $RecordID 
     */
    public function updateCounter($RecordID = NULL)
    {
        if (!$RecordID) {
            $RecordID = $this->GetMyKey();
        }
        $this->MyDbLink->ExeQuery('UPDATE entries SET used = (used+1) WHERE id= ' . $RecordID);
    }

    /**
     * Vrací pole všech domén k dispozici 
     * 
     * @return array 
     */
    public static function getDomainList()
    {
        return EaseShared::myDbLink()->queryTo2DArray('SELECT domain FROM entries GROUP BY domain ORDER BY domain');
    }

    /**
     * Vrací následující volný kod
     * 
     * @param  string $Domain
     * @return type 
     */
    public static function getNextCode($Domain)
    {
        $Counter = self::getCodeCount($Domain);
        return self::encode($Counter);
    }

    public static function getCodeCount($Domain)
    {
        return (int) EaseShared::myDbLink()->queryToValue('SELECT count(*) FROM entries WHERE domain=\'' . $Domain . '\'');
    }

}

?>