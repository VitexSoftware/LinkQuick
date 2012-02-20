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
     * @var EaseHtmlInputTextTag 
     */
    public $InputTag = NULL;

    /**
     * Input for Date and time
     * @param string $PartName 
     */
    function __construct($PartName, $InitialValue = NULL, $TagProperties = NULL) {
        $this->TagProperties = $TagProperties;
        $this->InitialValue = $InitialValue;
        $this->SetPartName($PartName);
        parent::__construct();
        $this->EaseShared->WebPage->IncludeJavaScript('js/jquery-ui-timepicker-addon.js', 3);
        $this->EaseShared->WebPage->IncludeCss('css/jquery-ui-timepicker-addon.css');
        $this->InputTag = new EaseHtmlInputTextTag($this->PartName, $this->InitialValue, $this->TagProperties);
        $this->InputTag->SetTagID($this->PartName);
        $this->InputTag = $this->AddItem($this->InputTag);
    }

    function Finalize() {
        $this->EaseShared->WebPage->AddJavaScript('$(function() { $( "#' . $this->PartName . '" ).datetimepicker( { ' . $this->GetPartPropertiesToString() . ' });});', 10);
        parent::Finalize();
    }
 
}

?>
