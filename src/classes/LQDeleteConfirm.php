<?php

/**
 * Dialog s potvrzením 
 * @copyright Vitex Software © 2011
 * @author Vítězslav Dvořák <dvorak@mobile-partnership.cz>
 * @package LinkQuick
 * @subpackage WEBUI
 */
class LQDeleteConfirm extends \Ease\HtmlDivTag {

    /**
     * Dialog potvrzení mazání položky 
     * @param string $DialogID 
     */
    function __construct($DialogID) {
        parent::__construct($DialogID, NULL, array('title' => _('žádost o potvrzení')));
        $this->setTagCss(array('visibility' => 'hidden'));
    }

    /**
     * Vložení nezbytností pro jQuery
     */
    function afterAdd() {
        EaseJQueryUIPart::jQueryze($this);
    }

    /**
     * Vloží javascript s dialogem potvrzení smazání linku
     */
    function finalize() {
        $this->addJavaScript('
	$(function() {
	        $(\'.delete\').click(function(e){
	            e.preventDefault();
                    $("#' . $this->getTagID() . '").css("visibility",("visible"));   
                    var targetUrl = $(this).attr("href");
                    $("#' . $this->getTagID() . '").dialog({
 			resizable: false,
                        autoOpen: false,
			modal: true,
                        buttons : {
                                    "' . _('ano') . '" : function() {
                                    window.location.href = targetUrl;
                                  },
                                   "' . _('ne') . '" : function() { 
                                       $(this).dialog("close");
                                    }
                        }
                });

                $( \'#' . $this->getTagID() . '\' ).dialog( \'open\' );
        });
});', NULL, TRUE);

        $this->addItem('<p>
        <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
        <div>' . _('Opravdu nevratně smazat ?') . '</div>
        </p>');
    }

}
?>
