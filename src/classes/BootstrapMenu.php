<?php
namespace LQ;


/**
 * Twitter boostrap menu
 * 
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2010,2011,2012,2016
 */

class BootstrapMenu extends \Ease\TWB\Navbar {

    /**
     * Navigace
     * @var \Ease\Html\UlTag
     */
    public $nav = NULL;

    /**
     * Hlavní menu aplikace
     *
     * @param string $name
     * @param mixed  $content
     * @param array  $properties
     */
    public function __construct($name = null, $content = null, $properties = null) {
        parent::__construct("Menu", new \Ease\Html\ImgTag('images/LinkQuickTwitterLogo.png', 'NetspotAdmin', 20, 20, array('class' => 'img-rounded')), array('class' => 'navbar-fixed-top'));

        $user = \Ease\Shared::user();
        \Ease\TWB\Part::twBootstrapize();
        if (!$user->getUserID()) {
//            $this->addMenuItem('<a href="createaccount.php">' . \Ease\TWB\Part::GlyphIcon('leaf') . ' ' . _('Register') . '</a>', 'right');
            $this->addMenuItem(
                    '
<li class="divider-vertical"></li>
<li class="dropdown">
<a class="dropdown-toggle" href="login.php" data-toggle="dropdown"><i class="icon-circle-arrow-left"></i> ' . _('Logon') . '<strong class="caret"></strong></a>
<div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px; left: -120px;">
<form method="post" class="navbar-form navbar-left" action="login.php" accept-charset="UTF-8">
<input class="form-control" style="margin-bottom: 15px;" type="text" placeholder="' . _('Username') . '" id="username" name="login">
<input class="form-control" style="margin-bottom: 15px;" type="password" placeholder="' . _('Password') . '" id="password" name="password">
<!-- input style="float: left; margin-right: 10px;" type="checkbox" name="remember-me" id="remember-me" value="1">
<label class="string optional" for="remember-me"> ' . _('zapamatuj si mne') . '</label -->
<input class="btn btn-primary btn-block" type="submit" id="sign-in" value="' . _('Log in') . '">
</form>
</div>', 'right'
            );
        } else {

       $userID = EaseShared::user()->getUserID();
        if ($userID) {
            $myLinksCount = EaseShared::myDbLink()->queryToValue('SELECT COUNT(*) FROM entries WHERE owner=' . $userID);
        } else {
            $myLinksCount = EaseShared::myDbLink()->queryToValue('SELECT COUNT(*) FROM entries');
        }

        $brand = new \Ease\HtmlDivTag('sitelogo', $myLinksCount, array('class' => 'brand'));
             
        $this->addMenuItem($brand);
            
            $userMenu = '<li class="dropdown" style="width: 120px; text-align: right; background-image: url( ' . $user->getIcon() . ' ) ;  background-repeat: no-repeat; background-position: left center; background-size: 40px 40px;"><a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $user->getLogin() . ' <b class="caret"></b></a>
<ul class="dropdown-menu" style="text-align: left; left: -60px;">
<li><a href="settings.php">' . \Ease\TWB\Part::GlyphIcon('wrench') . '<i class="icon-cog"></i> ' . _('Settings') . '</a></li>
';

            $this->addMenuItem($userMenu . '
<li><a href="http://redmine.netspot.cz/projects/netspot-soap-maintainance">' . \Ease\TWB\Part::GlyphIcon('envelope') . ' ' . _('Developer support') . '</a></li>
<li class="divider"></li>
<li><a href="logout.php">' . \Ease\TWB\Part::GlyphIcon('off') . ' ' . _('Logout') . '</a></li>
</ul>
</li>
', 'right');
        }
    }

    /**
     * Vypíše stavové zprávy
     */
    public function draw() {
        $statusMessages = $this->webPage->getStatusMessagesAsHtml();
        if ($statusMessages) {
            $this->addItem(new \Ease\Html\Div($statusMessages, array('id' => 'StatusMessages', 'class' => 'well', 'title' => _('Click to hide'), 'data-state' => 'down')));
            $this->addItem(new \Ease\Html\Div(null, array('id' => 'smdrag')));
        }
        parent::draw();
    }

}
