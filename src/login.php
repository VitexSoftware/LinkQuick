<?php

/**
 * Přihlašovací stránka
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2011
 * @package LevyBreh
 */
require_once 'includes/LQInit.php';
require_once 'Ease/EaseJQueryWidgets.php';
require_once 'classes/LQTwitter.php';

if (!is_object($oUser)) {
    die(_('Cookies jsou vyžadovány'));
}

$Login = $oPage->getRequestValue('login');

if ($Login) {
    $_SESSION['User'] = new LQUser();
    if ($oUser->TryToLogin($_POST)) {
        $NextUrl = $oPage->getRequestValue('NextUrl');
        if (($NextUrl != 'LogOff.php') && !is_null($NextUrl)) {
            $oPage->Redirect($NextUrl);
        } else {
            $oPage->Redirect('index.php');
        }
        exit;
    }
}


$oPage->addItem(new LQPageTop(_('Přihlaš se')));

$LoginFace = new \Ease\HtmlDivTag('LoginFace');


$oPage->column1->addItem(new \Ease\HtmlDivTag('WelcomeHint', _('Zadejte, prosím, Vaše přihlašovací údaje:')));

$LoginForm = $LoginFace->addItem(new \Ease\HtmlForm('Login'));
$LoginForm->setTagID('LoginForm');
$LoginForm->addItem(new EaseLabeledInput('login',NULL,_('Login')));
$LoginForm->addItem(new EaseLabeledPasswordInput('password',NULL,_('Heslo')));
$LoginForm->addItem(new EaseJQuerySubmitButton('LogIn',_('Přihlášení')));

$oPage->column2->addItem($LoginFace);

$oPage->column3->addItem(new \Ease\HtmlDivTag('TwitterAuth', LQTwitter::AuthButton('twauth.php')));

$oPage->addItem(new LQPageBottom());

$oPage->Draw();
?>