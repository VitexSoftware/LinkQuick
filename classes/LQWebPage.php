<?php

/**
 * Třídy pro vykreslení stránky
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2010,2011,2012
 */
require_once 'Ease/EaseWebPage.php';
require_once 'Ease/EaseHtmlForm.php';
require_once 'Ease/EaseJQueryWidgets.php';

class LQWebPage extends EaseWebPage {

    /**
     * Skin JQuery UI stránky
     * @var string
     */
    public $jQueryUISkin = 'css/le-frog/jquery-ui.css';

    /**
     * Základní objekt stránky Levého břehu
     * @param LBUser $UserObject 
     */
    function __construct(&$UserObject = NULL) {
        parent::__construct($UserObject);
        $this->IncludeCss('css/default.css');
    }

}

/**
 * Vršek stránky
 */
class LQPageTop extends EasePage {

    /**
     * Titulek stránky
     * @var type 
     */
    public $PageHeading = 'Page Heading';

    /**
     * Nastavuje titulek
     * @param string $PageHeading
     * @param string $BodyID 
     */
    function __construct($PageHeadeing = NULL) {
        $this->PageHeading = $PageHeadeing;
        parent::__construct();
    }

    /**
     * Vloží vršek stránky a hlavní menu
     */
    function Finalize() {
        $this->SetupWebPage();
        $this->WebPage->PageHeading = $this->PageHeading;
        $this->AddItem(new LQMainMenu());
        $this->AddItem(new EaseHtmlH1Tag(_('LinkQuick')));
        $this->AddItem(new EaseHtmlH3Tag(_('Zkracovač pro vaše adresy')));
        parent::Finalize();
    }

}

/**
 * Hlavní menu
 */
class LQMainMenu extends EaseHtmlDivTag {

    /**
     * Vytvoří hlavní menu
     */
    function __construct() {
        parent::__construct('MainMenu');
    }

    /**
     * Vložení menu
     */
    function AfterAdd() {
        $UserID = $this->User->GetUserID();
        if ($UserID) {
            if($this->User->GetUserEmail()){
                $this->AddItem(new EaseHtmlSpanTag('login', new EaseHtmlATag('Settings.php', $this->User->GetUserLogin())));
            } else {
                $this->AddItem(new EaseHtmlSpanTag('login', $this->User->GetUserLogin()));
            }
            $MyLinks = $this->WebPage->MyDbLink->QueryToArray('SELECT COUNT(*) FROM entries WHERE owner=' . $UserID);
            if (isset($MyLinks[0])) {
                $this->AddItem(current($MyLinks[0]));
            } else {
                $this->AddItem(0);
            }
        }
        $this->AddItem(new EaseHtmlATag('index.php', _('Přidat')));
        if ($UserID) {
            $this->AddItem(new EaseHtmlATag('List.php', _('Moje zkratky')));
            $this->AddItem(new EaseHtmlATag('LogOut.php', _('Odhlášení')));
        } else {
            $this->AddItem(new EaseHtmlATag('Login.php', _('Přihlášení')));
            $this->AddItem(new EaseHtmlATag('CreateAccount.php', _('Vytvořit účet')));
        }
    }

    /**
     * Přidá do stránky javascript pro skrývání oblasti stavových zpráv
     */
    function Finalize() {
        $this->SetupWebPage();
        EaseJQueryPart::jQueryze($this);
        $this->AddJavaScript('$("#StatusMessages").click(function(){ $("#StatusMessages").fadeTo("slow",0.25).slideUp("slow"); });', 3, TRUE);
        parent::Finalize();
    }

    /**
     * Vloží do stránky stavové hlášky a vykreslí ji
     */
    function Draw() {
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
class LQPageBottom extends EasePage {

    /**
     * Zobrazí přehled právě přihlášených a spodek stránky
     */
    function Finalize() {
        $this->AddItem(new EaseHtmlDivTag('FootAbout', '&nbsp;&nbsp; &copy; 2011 Vitex Software'));
        parent::Finalize();
    }

}

?>
