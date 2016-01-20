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

if (!is_object($OUser)) {
    die(_('Cookies jsou vyžadovány'));
}

$Login = $OPage->GetRequestValue('login');

if ($Login) {
    $_SESSION['User'] = new LQUser();
    if ($OUser->TryToLogin($_POST)) {
        $NextUrl = $OPage->GetRequestValue('NextUrl');
        if (($NextUrl != 'LogOff.php') && !is_null($NextUrl)) {
            $OPage->Redirect($NextUrl);
        } else {
            $OPage->Redirect('index.php');
        }
        exit;
    }
}


$OPage->AddItem(new LQPageTop(_('Přihlaš se')));

$LoginFace = new EaseHtmlDivTag('LoginFace');


$OPage->column1->addItem(new EaseHtmlDivTag('WelcomeHint', _('Zadejte, prosím, Vaše přihlašovací údaje:')));

$LoginForm = $LoginFace->AddItem(new EaseHtmlForm('Login'));
$LoginForm->SetTagID('LoginForm');
$LoginForm->AddItem(new EaseLabeledInput('login',NULL,_('Login')));
$LoginForm->AddItem(new EaseLabeledPasswordInput('password',NULL,_('Heslo')));
$LoginForm->AddItem(new EaseJQuerySubmitButton('LogIn',_('Přihlášení')));

$OPage->column2->addItem($LoginFace);

$OPage->column3->addItem(new EaseHtmlDivTag('TwitterAuth', LQTwitter::AuthButton('twauth.php')));

$OPage->AddItem(new LQPageBottom());

$OPage->Draw();
?>