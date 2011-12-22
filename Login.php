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



$OPage->AddCss('
#LoginFace {
    display: inline-block;
    overflow: auto
}

#WelcomeHint {
    width: 400px;
    float:left;
}

#Spacer {
    width: 60px;
    float:left;
}

#LoginForm {
    width: 400px;
    float:left;
}

#TwitterAuth {
    float:right;
}
    
');


$OPage->AddItem(new LQPageTop(_('Přihlaš se')));

$LoginFace = new EaseHtmlDivTag('LoginFace');


$LoginFace->AddItem(new EaseHtmlDivTag('WelcomeHint', _('<p>Zadejte, prosím, Vaše přihlašovací údaje:</p>')));
$LoginFace->AddItem(new EaseHtmlDivTag('Spacer', '&nbsp;'));

$LoginForm = $LoginFace->AddItem(new EaseHtmlForm('Login'));
$LoginForm->SetTagID('LoginForm');
$LoginForm->AddItem(new EaseLabeledInput('login',NULL,_('Login')));
$LoginForm->AddItem(new EaseLabeledPasswordInput('password',NULL,_('Heslo')));
$LoginForm->AddItem(new EaseJQuerySubmitButton('LogIn',_('Přihlášení')));

$OPage->AddItem($LoginFace);

$OPage->AddItem(new EaseHtmlDivTag('TwitterAuth', LQTwitter::AuthButton('auth.php')));

$OPage->AddItem(new LQPageBottom());

$OPage->Draw();
?>