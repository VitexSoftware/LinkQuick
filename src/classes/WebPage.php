<?php

namespace LQ;

/**
 * Custom Twitter Bootstrap based webpage class
 * 
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2010,2011,2012,2016
 */
class WebPage extends \Ease\TWB\WebPage {

    /**
     * Page main continer
     * @var TWB\Contaner 
     */
    public $container = NULL;

    /**
     * First column 
     * @var TWB\Col 
     */
    public $column1 = NULL;

    /**
     * Second column
     * @var TWB\Col 
     */
    public $column2 = NULL;

    /**
     * Third column
     * @var TWB\Col 
     */
    public $column3 = NULL;

    /**
     * Basic Custom Twitter Bootstrap based webpage class
     * 
     * @param string $pageTitle
     */
    function __construct($pageTitle = null) {
        parent::__construct($pageTitle);
        $this->includeCss('css/main.css');
        $this->head->addItem('<link rel="apple-touch-icon-precomposed" href="images/LinkQuickTwitterLogo.png">');
        $this->head->addItem('<link rel="shortcut icon"  type="image/png" href="images/LinkQuickTwitterLogo.png">');
        $this->head->addItem('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
        $this->addCss('body {
                padding-top: 60px;
                padding-bottom: 40px;
            }');
        $this->addItem('<br>');

        $this->container = $this->addItem(new \Ease\TWB\Container);

        $this->heroUnit = $this->container->addItem(new \Ease\Html\Div(null, array('class' => 'hero-unit')));

        $row = $this->container->addItem(new \Ease\TWB\Row);

        $this->column1 = $row->addColumn(4);
        $this->column2 = $row->addColumn(4);
        $this->column3 = $row->addColumn(4);
    }

}
