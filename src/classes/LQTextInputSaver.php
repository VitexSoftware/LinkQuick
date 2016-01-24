<?php

/**
 * Ukládání hodnot z políčka ajaxem
 * @copyright Vitex Software © 2011
 * @author Vitex <vitex@hippy.cz>
 * @package LinkQuick
 * @subpackage WEBUI
 */require_once 'Ease/\Ease\HtmlForm.php';

/**
 * Ukláda data z imputu přímo do databáze
 */
class LQTextInputSaver extends EaseLabeledTextInput {

    /**
     * Pracujeme s tabulkou mains
     * @var string 
     */
    public $MyTable = 'users';
    
    /**
     * Sloupeček pro poslední modifikaci
     * @var type 
     */
    public $MyLastModifiedColumn = 'DatSave';


    /**
     * Input pro editaci položek uživatele
     * @param string $Name
     * @param mixed $Value
     * @param string $Label
     * @param int $UserID
     * @param array $Properties 
     */
    function __construct($Name, $Value = NULL, $Label = NULL, $Properties = NULL) {
        parent::__construct($Name, $Value, $Label, $Properties);
    }

    /**
     * Přidá odesílací javascript
     */
    function Finalize() {
        parent::Finalize();
        $this->EnclosedElement->setTagProperties(array('OnChange' => '$.post(\'DataSaver.php\', { SaverClass: \'' . get_class($this) . '\', Field: \'' . $this->EnclosedElement->getTagProperty('name') . '\', Value: this.value } )'));
//        $this->EnclosedElement->setTagProperties(array('OnChange' => '$.ajax( { type: \"POST\", url: \"DataSaver.php\", data: \"SaverClass=' . get_class($this) . '&amp;Field=' . $this->EnclosedElement->getTagProperty('name') . '&amp;Value=\" + this.value , async: false, success : function() { alert (this); }, statusCode: { 404: function() { alert(\'page not found\');} } }); '));
    }

    /**
     * Uloží data, pokud se to nepovede, pokusí se vytvořit chybějící sloupečky 
     * a vrátí vysledek dalšího uložení
     * @param array $Data
     * @param boolean $SearchForID
     * @return int 
     */
    function SaveToMySQL($Data = NULL, $SearchForID = false) {
        if (is_null($Data)) {
            $Data = $this->getData();
        }
        $SaveResult = parent::SaveToMySQL($Data, $SearchForID);
        if (is_null($SaveResult)) {
            if ($this->CreateMissingColumns($Data) > 0) {
                $SaveResult = parent::SaveToMySQL($Data, $SearchForID);
            }
        }
        return $SaveResult;
    }

    /**
     * Vytvoří v databázi sloupeček pro uložení hodnoty widgetu
     * @param array $Data
     * @return int 
     */
    function CreateMissingColumns($Data = NULL) {
        if (is_null($Data)) {
            $this->getData();
        }
        unset($Data[$this->getMyKeyColumn()]);
        $KeyName = current(array_keys($Data));
        return EaseDbMySqli::CreateMissingColumns($this, array($KeyName => str_repeat(' ', 1000)));
    }

    /**
     * Přiřadí objektu uživatele a nastaví DB
     * @param EaseUser $User
     * @param object|mixed $TargetObject
     * @return boolen 
     */
    function SetUpUser(&$User, &$TargetObject = NULL) {
        $this->setMyKey($User->getUserID());
        return parent::SetUpUser($User, $TargetObject);
    }
    
}

?>
