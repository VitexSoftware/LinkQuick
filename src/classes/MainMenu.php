<?php
namespace LQ;

/**
 * Custom Main menu
 * 
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2010,2011,2012,2016
 */

class MainMenu extends \Ease\Html\Div
{

    /**
     * Main Menu Object
     */
    function __construct()
    {
        parent::__construct(null,['id'=>'MainMenu']);
    }

    /**
     * Menu including
     */
    function afterAdd()
    {
        $nav = $this->addItem(new BootstrapMenu());
        $user  = \Ease\Shared::user();
        $userID = $user->getUserID();
        
        if ($userID) {
            $icon = $user->getIcon();
            if ($icon) {
                $nav->addMenuItem(new \Ease\Html\SpanTag('UserIcon', new \Ease\HtmlATag('settings.php', new \Ease\HtmlImgTag($icon, $user->getUserLogin(), 40, 40))));
            } else {
                $nav->addMenuItem(new \Ease\HtmlSpanTag('User', new \Ease\HtmlATag('settings.php', $user->getUserLogin())));
            }
        }
        $nav->addMenuItem(new \Ease\Html\ATag('index.php', _('Add new')));
        if ($userID) {
            $nav->addMenuItem(new \Ease\Html\ATag('list.php', _('My Shortcuts')));
            $nav->addMenuItem(new \Ease\Html\ATag('logout.php', _('Sign out')));
        } else {
            $nav->addMenuItem(new \Ease\Html\ATag('login.php', _('Sign IN')));
            $nav->addMenuItem(new \Ease\Html\ATag('createaccount.php', _('Create account')));
        }

        $nav->addMenuItem(new \Ease\Html\ATag('doc/lqApiClient.phps', _('API')));
    }

    /**
     * Add status messages code
     */
    function finalize()
    {
        $this->addJavaScript('$("#StatusMessages").click(function(){ $("#StatusMessages").fadeTo("slow",0.25).slideUp("slow"); });', 3, TRUE);
        \Ease\JQuery\Part::jQueryze($this);
        $this->includeJavaScript('js/slideupmessages.js');
    }

}

