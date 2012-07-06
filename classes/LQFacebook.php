<?php
/**
 * Description of LQFacebook
 *
 * @author vitex
 */
class LQFacebook
{

    static function getLikeButton($Url)
    {
        return new LQFBLikeButton($Src);
    }

    static function getLoginButton()
    {
        return new EaseJQueryLinkButton('fblogin.php', _('Přihlásit přez facebook'));
    }

}

?>
