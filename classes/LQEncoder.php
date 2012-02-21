<?php

require_once 'Ease/EaseBase.php';

/**
 * Třída pro kodování url
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2011
 * @package LinkQuick
 * @subpackage Engine
 */
class LQEncoder extends EaseBrick {

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
     * @param string $OriginalURL url ke zkrácení
     */
    function __construct($OriginalURL = NULL) {
        parent::__construct();
        if (!is_null($OriginalURL)) {
            $this->SaveUrl($OriginalURL);
        }
    }

    /**
     * Vraci kod pro URL
     * @param string $Url
     */
    function GetCodeByUrl($Url) {
        $Result = $this->GetColumnsFromMySQL(array('id', 'code'), array('url' => $this->MyDbLink->EaseAddSlashes($Url)));
        if (isset($Result[0]['code'])) {
            return $Result[0]['code'];
        }
    }

    /**
     * Nastavuje URL
     * @param string $Url 
     */
    function SetURL($Url) {
        $this->PlainURL = $Url;
    }

    /**
     * Vraci URL podle kodu
     * @param string $Code
     * @return string url
     */
    function GetURLByCode($Code = NULL) {
        if (!$Code) {
            $Code = $this->GetDataValue('code');
            $InObject = TRUE;
        } else {
            $InObject = FALSE;
        }
        $Url = $this->GetColumnsFromMySQL(array('id', 'url','ExpireDate','UNIX_TIMESTAMP(ExpireDate) AS Expire'), array('code' => $Code));
        if (isset($Url[0]['url'])) {
            if ($InObject) {
                $this->SetDataValue('url', $Url[0]['url']);
                $this->SetDataValue('id', $Url[0]['id']);
                if((int)$Url[0]['Expire'] < time()){
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
     * @param type $Code 
     */
    function SetCode($Code) {
        $this->SetDataValue('code', $Code);
    }

    /**
     * Vraci vysledne URL
     * @return string
     */
    public function GetShortCutURL() {
        return 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/' . $this->GetCode();
    }

    /**
     * Ulozi URL do databaze
     * @param string $OriginalURL
     * @return string
     */
    public function SaveUrl($OriginalURL) {
        $OriginalURL = $this->MyDbLink->EaseAddSlashes($OriginalURL);
        $this->MyDbLink->ExeQuery('INSERT INTO entries SET url=\'' . $OriginalURL . '\', created=NOW(), ExpireDate = \''.$this->GetDataValue('ExpiryDate').'\', owner='.(int)  $this->EaseShared->User->GetUserID());
        $this->RecordID = $this->MyDbLink->GetInsertID();
        $EncodedURL = self::Encode($this->RecordID);
        $this->MyDbLink->ExeQuery('UPDATE entries SET code=\'' . $EncodedURL . '\' WHERE id=\'' . $this->RecordID . '\'');
        $this->EncodedURL = $EncodedURL;
        return $EncodedURL;
    }

    /**
     * Zakoduje ID
     */
    static public function Encode($StringToEncode) {
        return base_convert($StringToEncode, 10, 36);
    }

    /**
     * Vrací aktuální kod
     * @return type 
     */
    public function GetCode() {
        return $this->EncodedURL;
    }

    /**
     * Vrací aktuální URL
     * @return type 
     */
    public function GetURL() {
        return $this->PlainURL;
    }

    /**
     * Zvedne počítadlo použití
     * @param type $RecordID 
     */
    public function UpdateCounter($RecordID = NULL) {
        if (!$RecordID) {
            $RecordID = $this->GetMyKey();
        }
        $this->MyDbLink->ExeQuery('UPDATE entries SET used = (used+1) WHERE id= ' . $RecordID);
    }

}

?>