<?php

/**
 * Dialog volby data a času pro editaci linku
 * @copyright Vitex Software © 2012
 * @author Vítězslav Dvořák <vitex@vitexsoftware.cz>
 * @package LinkQuick
 * @subpackage WebUI
 */
require_once 'LQDateTimeSelector.php';

/**
 * Input for Date and time pro editaci linku
 * @package LinkQuick
 * @subpackage WebUi
 * @author vitex
 */
class LQLinkDateTimeSelector extends LQDateTimeSelector {
    /**
     * Klíčujem dle id
     * @var string 
     */
    public $MyKey = 'id';
    /**
     * Pracujeme s tabulkou entries
     * @var string 
     */
    public $MyTable = 'entries';
    /**
     * Pracujem se záznamem č.
     * @var int 
     */
    public $LinkID = NULL;
    
    /**
     * Ajaxový editor datumu a času
     * @param string $PartName
     * @param date $InitialValue
     * @param int $LinkID
     * @param array $TagProperties 
     */
    function __construct($PartName, $InitialValue, $LinkID,  $TagProperties = NULL) {
        $this->LinkID = $LinkID;
        parent::__construct($PartName, $InitialValue, $TagProperties);
        $this->InputTag->SetTagProperties(array('size'=>15));
    }

    /**
     * Ukládání do DB
     */
    function AfterAdd(){
        $this->SetPartProperties(array('onClose'=>'function(dateText, inst) { $.post(\'DataSaver.php\', { SaverClass: \'' . get_class($this) .
            '\', id: ' . $this->LinkID . ', Field: \'' . $this->InputTag->GetTagProperty('name') . '\', Value: dateText } ) }'));
    }
    
}

?>
