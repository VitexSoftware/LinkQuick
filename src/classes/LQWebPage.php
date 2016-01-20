<?php

/**
 * Třídy pro vykreslení stránky
 * 
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2010,2011,2012
 */
require_once 'Ease/EaseWebPage.php';
require_once 'Ease/EaseHtmlForm.php';
require_once 'Ease/EaseJQueryWidgets.php';

class LQWebPage extends EaseWebPage
{

    /**
     * Skin JQuery UI stránky
     * @var string
     */
    public $jQueryUISkin = 'css/le-frog/jquery-ui.css';

    /**
     * Hlavní blok stránky
     * @var EaseHtmlDivTag 
     */
    public $container = NULL;

    /**
     * První sloupec
     * @var EaseHtmlDivTag 
     */
    public $column1 = NULL;

    /**
     * Druhý sloupec
     * @var EaseHtmlDivTag 
     */
    public $column2 = NULL;

    /**
     * Třetí sloupec
     * @var EaseHtmlDivTag 
     */
    public $column3 = NULL;

    /**
     * Základní objekt stránky Levého břehu
     * @param LBUser $UserObject 
     */
    function __construct(&$UserObject = NULL)
    {
        parent::__construct($UserObject);
        $this->IncludeCss('css/bootstrap.css');
        $this->IncludeCss('css/bootstrap-responsive.css');
        $this->IncludeCss('css/default.css');
        $this->Head->addItem('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
        $this->addCss('body {
                padding-top: 60px;
                padding-bottom: 40px;
            }');
        $this->Head->addItem('        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->');
        $this->Head->addItem('<link rel="apple-touch-icon-precomposed" sizes="144x144" href="http://twitter.github.com/bootstrap/assets/ico/apple-touch-icon-144-precomposed.png">');
        $this->Head->addItem('<link rel="apple-touch-icon-precomposed" sizes="114x114" href="http://twitter.github.com/bootstrap/assets/ico/apple-touch-icon-114-precomposed.png">');
        $this->Head->addItem('<link rel="apple-touch-icon-precomposed" sizes="72x72" href="http://twitter.github.com/bootstrap/assets/ico/apple-touch-icon-72-precomposed.png">');
        $this->Head->addItem('<link rel="apple-touch-icon-precomposed" href="http://twitter.github.com/bootstrap/assets/ico/apple-touch-icon-57-precomposed.png">');


        $this->container = $this->addItem(new EaseHtmlDivTag(null, null, array('class' => 'container')));

        $this->heroUnit = $this->container->addItem(new EaseHtmlDivTag('heroUnit', null, array('class' => 'hero-unit')));

        $row = $this->container->addItem(new EaseHtmlDivTag(null, null, array('class' => 'row')));

        $this->column1 = $row->addItem(new EaseHtmlDivTag(null, null, array('class' => 'span4')));
        $this->column2 = $row->addItem(new EaseHtmlDivTag(null, null, array('class' => 'span4')));
        $this->column3 = $row->addItem(new EaseHtmlDivTag(null, null, array('class' => 'span4')));
    }

}

/**
 * Vršek stránky
 */
class LQPageTop extends EasePage
{

    /**
     * Titulek stránky
     * @var type 
     */
    public $PageHeading = 'Page Heading';

    /**
     * Nastavuje titulek
     * 
     * @param string $PageHeading
     */
    function __construct($PageHeadeing = NULL)
    {
        $this->PageHeading = $PageHeadeing;
        parent::__construct();
    }

    /**
     * Vloží vršek stránky a hlavní menu
     */
    function Finalize()
    {
        $this->SetupWebPage();
        $this->WebPage->PageHeading = $this->PageHeading;
        $this->AddItem(new LQMainMenu());
    }

}

/**
 * Hlavní menu
 */
class LQMainMenu extends EaseHtmlDivTag
{

    /**
     * Vytvoří hlavní menu
     */
    function __construct()
    {
        parent::__construct('MainMenu');
    }

    /**
     * Vložení menu
     */
    function AfterAdd()
    {
        $nav = $this->addItem(new LQBootstrapMenu());
        $UserID = $this->User->GetUserID();
        
        if ($UserID) {
            $Icon = $this->User->GetIcon();
            if ($Icon) {
                $nav->addMenuItem(new EaseHtmlSpanTag('UserIcon', new EaseHtmlATag('settings.php', new EaseHtmlImgTag($Icon, $this->User->getUserLogin(), 40, 40))));
            } else {
                $nav->addMenuItem(new EaseHtmlSpanTag('User', new EaseHtmlATag('settings.php', $this->User->getUserLogin())));
            }
        }
        $nav->addMenuItem(new EaseHtmlATag('index.php', _('Přidat')));
        if ($UserID) {
            $nav->addMenuItem(new EaseHtmlATag('list.php', _('Moje zkratky')));
            $nav->addMenuItem(new EaseHtmlATag('logout.php', _('Odhlášení')));
        } else {
            $nav->addMenuItem(new EaseHtmlATag('login.php', _('Přihlášení')));
            $nav->addMenuItem(new EaseHtmlATag('createaccount.php', _('Vytvořit účet')));
        }

        $nav->addMenuItem(new EaseHtmlATag('doc/lqApiClient.phps', _('API')));
    }

    /**
     * Přidá do stránky javascript pro skrývání oblasti stavových zpráv
     */
    function finalize()
    {
        $this->setupWebPage();
        EaseJQueryPart::jQueryze($this);
        $this->addJavaScript('$("#StatusMessages").click(function(){ $("#StatusMessages").fadeTo("slow",0.25).slideUp("slow"); });', 3, TRUE);
    }

    /**
     * Vloží do stránky stavové hlášky a vykreslí ji
     */
    function Draw()
    {
        $FootStatus = $this->AddItem(new EaseHtmlDivTag('StatusMessages', NULL, array('title' => _('kliknutím skryjete zprávy'))));
        $this->WebPage->TakeStatusMessages($this->User->GetStatusMessages(TRUE));
        $StatusMessages = $this->WebPage->GetStatusMessagesAsHtml(TRUE);

        if (!$StatusMessages) {
            $StatusMessages = '&nbsp';
        }
        $FootStatus->AddItem($StatusMessages);
        parent::Draw();
    }

}

/**
 * Spodek stránky
 */
class LQPageBottom extends EaseHtmlDivTag
{

    /**
     * Zobrazí přehled právě přihlášených a spodek stránky
     */
    function finalize()
    {
        if (!count($this->WebPage->heroUnit->PageParts)) {
            unset($this->WebPage->container->PageParts['EaseHtmlDivTag@heroUnit']);
        };
        $this->SetTagID('footer');
        $this->addItem('<hr>');
        $Foot = $this->addItem(new EaseHtmlDivTag('FootAbout', '&nbsp;&nbsp; &copy; 2012 <a href="http://vitexsoftware.cz/">Vitex Software</a>'));


//Twitter
        $Foot->addItem(new EaseHtmlATag('https://twitter.com/share', 'Napiš o LinkQuick', array('class' => 'twitter-share-button', 'data-via' => 'Vitexus')));
        $Foot->addJavaScript('!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");');

//Google+ 
        $Foot->addItem(new EaseHtmlDivTag(NULL, NULL, array('class' => 'g-plus', 'data-action' => 'share')));
        $Foot->addItem(new EaseJavaScript('  window.___gcfg = {lang: \'cs\', parsetags: \'onload\'};
  (function() {
    var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
    po.src = \'https://apis.google.com/js/plusone.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
  })();
'));

        $Foot->addItem(new LQFlattrButton());

//FaceBook
        $Foot->addItem(new EaseHtmlATag(null, _('sdílej na Facebooku'), array('name' => 'fb_share', 'type' => 'button', 'share_url' => dirname(EasePage::phpSelf()))));
        $Foot->addItem(new EaseJavaScript(NULL, array('src' => 'http://static.ak.fbcdn.net/connect.php/js/FB.Share')));



        $Foot->addItem('<a href="http://www.spoje.net"><img align="right" style="border:0" src="images/spojenet_small_white.gif" alt="SPOJE.NET>" title="Sponzorují SPOJE.NET s.r.o." /></a>');
    }

}

class LQBootstrapMenu extends EaseHtmlDivTag
{

    public $nav = NULL;

    function __construct($Name = null, $Content = null, $Properties = null)
    {
        $Properties['class'] = 'navbar navbar-fixed-top';
        parent::__construct($Name, $Content, $Properties);
        $navbarInner = $this->addItem(new EaseHtmlDivTag(null, null, array('class' => 'navbar-inner')));
        $container = $navbarInner->addItem(new EaseHtmlDivTag(null, null, array('class' => 'container')));
        $btnNavbar = $container->addItem(new EaseHtmlATag(null, null, array('class' => "btn btn-navbar", 'data-toggle' => "collapse", 'data-target' => ".nav-collapse")));
        $btnNavbar->addItem(new EaseHtmlSpanTag(null, null, array('class' => 'icon-bar')));
        $btnNavbar->addItem(new EaseHtmlSpanTag(null, null, array('class' => 'icon-bar')));
        $btnNavbar->addItem(new EaseHtmlSpanTag(null, null, array('class' => 'icon-bar')));

        $UserID = EaseShared::user()->GetUserID();
        if ($UserID) {
            $MyLinksCount = EaseShared::myDbLink()->queryToValue('SELECT COUNT(*) FROM entries WHERE owner=' . $UserID);
        } else {
            $MyLinksCount = EaseShared::myDbLink()->queryToValue('SELECT COUNT(*) FROM entries');
        }

        $brand = new EaseHtmlDivTag('sitelogo', $MyLinksCount, array('class' => 'brand'));
        $brand->addItem(new EaseHtmlATag('index.php', new EaseHtmlImgTag('images/LinkQuick.png', 'LinkQuick')));
        $container->addItem($brand);

        $navCollapse = $container->addItem(new EaseHtmlDivTag(null, null, array('class' => 'nav-collapse')));
        $this->nav = $navCollapse->addItem(new EaseHtmlUlTag(null, array('class' => 'nav')));
    }

    /**
     * 
     * @param EaseHtmlATag $PageItem
     * @return type 
     */
    function &addMenuItem($PageItem)
    {
        $MenuItem = $this->nav->addItem($PageItem);
        if (isset($PageItem->TagProperties['href'])) {
            $href = basename($PageItem->TagProperties['href']);
            if (strstr($href, '?')) {
                list($TargetPage, $Params) = explode('?', $href);
            } else {
                $TargetPage = $href;
            }
            if ($TargetPage == basename(EasePage::phpSelf())) {
                $this->nav->lastItem()->setTagProperties(array('class' => 'active'));
            }
        }
        return $MenuItem;
    }

}


/**
 * Like Button Facebooku 
 */
class LQFBLikeButton extends EaseHtmlIframeTag
{

    /**
     * Like Button facebooku
     * 
     * @param string $Src Url pro lajk facebooku
     */
    function __construct($Src)
    {
        $Properties['scrcolling'] = 'no';
        $Properties['frameborder'] = 'no';
        parent::__construct('http://www.facebook.com/plugins/like.php?href=' . $Src, $Properties);
    }

}

class LQFlattrButton extends EaseContainer
{

    function finalize()
    {
        $this->EaseShared->webPage()->addJavaScript('
   (function() {
        var s = document.createElement(\'script\'), t = document.getElementsByTagName(\'script\')[0];
        s.type = \'text/javascript\';
        s.async = true;
        s.src = \'http://api.flattr.com/js/0.6/load.js?mode=auto\';
        t.parentNode.insertBefore(s, t);
    })();'
        );
        $this->addItem('
<a class="FlattrButton" style="display:none;" rev="flattr;button:compact;" href="http://q.cz/"></a>
<noscript><a href="http://flattr.com/thing/671170/LinkQuick" target="_blank">
<img src="http://api.flattr.com/button/flattr-badge-large.png" alt="Flattr this" title="Flattr this" border="0" /></a></noscript>'
        );
    }

}

?>
