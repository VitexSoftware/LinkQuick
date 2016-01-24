<?php

/**
 * Dialog volby data a času
 * @copyright Vitex Software © 2011
 * @author Vítězslav Dvořák <dvorak@mobile-partnership.cz>
 * @package LinkQuick
 * @subpackage WEBUI
 */
require_once 'Ease/EaseHtmlForm.php';
require_once 'Ease/EaseJQuery.php';

/**
 * Input for Date and time
 * @link http://trentrichardson.com/examples/timepicker/
 * @package SIWA
 * @author vitex
 */
class LQDateTimeSelector extends EaseJQueryUIPart {

    /**
     * Propetries pass to Input
     * @var array 
     */
    public $TagProperties = NULL;
    /**
     * Initial datetime
     * @var string 
     */
    public $InitialValue = NULL;
    /**
     * Datetime Picker parameters
     * @var array 
     */
    public $PartProperties = array(
        'dateFormat' => 'yy-mm-dd',
        'showSecond' => true,
        'timeFormat' => 'hh:mm:ss');
    /**
     * Text Input
     * @var \Ease\HtmlInputTextTag 
     */
    public $InputTag = NULL;

    /**
     * Input for Date and time
     * @param string $PartName 
     */
    function __construct($PartName, $InitialValue = NULL, $TagProperties = NULL) {
        $this->TagProperties = $TagProperties;
        $this->InitialValue = $InitialValue;
        $this->setPartName($PartName);
        parent::__construct();
        $this->EaseShared->webPage->IncludeJavaScript('js/jquery-ui-timepicker-addon.js', 3);
        $this->EaseShared->webPage->IncludeCss('css/jquery-ui-timepicker-addon.css');
        $this->InputTag = new \Ease\HtmlInputTextTag($this->PartName, $this->InitialValue, $this->TagProperties);
        $this->InputTag->setTagID($this->PartName);
        $this->InputTag = $this->addItem($this->InputTag);
        if($InitialValue &&  (strtotime($InitialValue) < time( ))){
            $this->InputTag->setTagCss(array('background-color'=>'red'));
        }
    }

    function Finalize() {
        $this->EaseShared->webPage->addJavaScript('$(function() { $( "#' . $this->PartName . '" ).datetimepicker( { ' . $this->getPartPropertiesToString() . ' });});', 10);
        parent::Finalize();
    }
 
}


/**
 * Zobrazuje vstup pro heslo s měřičem síly opatřený patřičným popiskem
 */
class LQLabeledDateTimeSelector extends EaseLabeledInput {

    /**
     * Který input opatřit labelem ?
     * @var string EaseInputClass name 
     */
    public $ItemClass = 'LQDateTimeSelector';

}


?>
