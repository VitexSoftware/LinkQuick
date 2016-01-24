<?php
namespace LQ;

/**
 * LinkQuick Web page Top
 * 
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2010,2011,2012,2016
 */


/**
 * Vršek stránky
 */
class PageTop extends \Ease\Page
{

    /**
     * Titulek stránky
     * @var type 
     */
    public $pageHeading = 'Page Heading';

    /**
     * Nastavuje titulek
     * 
     * @param string $PageHeading
     */
    function __construct($pageHeadeing = NULL)
    {
        $this->pageHeading = $pageHeadeing;
        parent::__construct();
    }

    /**
     * Vloží vršek stránky a hlavní menu
     */
    function finalize()
    {
        $this->webPage->pageHeading = $this->pageHeading;
        $this->addItem(new MainMenu());
    }

}

