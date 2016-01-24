<?php

/**
 * Změna hesla uživatele
 * @author Vítězslav Dvořák <vitex@hippy.cz>
 * @copyright Vitex Software © 2011
 * @package LinkQuick
 * @subpackage WEBUI
 */
require_once 'includes/LQInit.php';
require_once 'Ease/EaseMail.php';
require_once 'Ease/EaseHtmlForm.php';
require_once 'Ease/EaseJQueryWidgets.php';

$oPage->OnlyForLogged(); //Pouze pro přihlášené
$FormOK = true;

if (!isset($_POST['password']) || !strlen($_POST['password'])) {
    $oUser->addStatusMessage('Prosím zadejte nové heslo');
    $FormOK = false;
} else {
    if ($_POST['password'] == $oUser->getUserLogin()) {
        $oUser->addStatusMessage('Heslo se nesmí shodovat s přihlašovacím jménem', 'waring');
        $FormOK = false;
    }
    /* TODO:
      if(!$OUser->PasswordCrackCheck($_POST['password'])){
      $OUser->addStatusMessage('Heslo není dostatečně bezpečné');
      $FormOK = false;
      }
     */
}
if (!isset($_POST['passwordConfirm']) || !strlen($_POST['passwordConfirm'])) {
    $oUser->addStatusMessage('Prosím zadejte potvrzení hesla');
    $FormOK = false;
}
if ((isset($_POST['passwordConfirm']) && isset($_POST['password'])) && ($_POST['passwordConfirm'] != $_POST['password'])) {
    $oUser->addStatusMessage('Zadaná hesla se neshodují', 'waring');
    $FormOK = false;
}

if (!isset($_POST['CurrentPassword'])) {
    $oUser->addStatusMessage('Prosím zadejte stávající heslo');
    $FormOK = false;
} else {
    if (!$oUser->PasswordValidation($_POST['CurrentPassword'], $oUser->getDataValue($oUser->PasswordColumn))) {
        $oUser->addStatusMessage('Stávající heslo je neplatné', 'warning');
        $FormOK = false;
    }
}


$oPage->addItem(new LQPageTop(_('Změna hesla uživatele')));

if ($FormOK && isset($_POST)) {
    $oUser->setDataValue($oUser->PasswordColumn, $oUser->EncryptPassword($_POST['password']));
    if ($oUser->saveToMySQL()) {
        $oUser->addStatusMessage('Heslo bylo změněno', 'success');

        $email = $oPage->addItem(new EaseMail($oUser->getDataValue($oUser->MailColumn), 'Změněné heslo pro FragCC'));
        $email->addItem("Vážený zákazníku vaše přihlašovací údaje byly změněny:\n");

        $email->addItem(' Login: ' . $oUser->getUserLogin() . "\n");
        $email->addItem(' Heslo: ' . $_POST['password'] . "\n");

        $email->send();
    }
} else {
    $LoginForm = new \Ease\HtmlForm(NULL);

    $LoginForm->addItem(new EaseLabeledPasswordInput('CurrentPassword', NULL, _('Stávající heslo')));

    $LoginForm->addItem(new EaseLabeledPasswordStrongInput('password', NULL, _('Nové heslo') . ' *'));
    $LoginForm->addItem(new EaseLabeledPasswordControlInput('passwordConfirm', NULL, _('potvrzení hesla') . ' *', array('id' => 'confirmation')));

    $LoginForm->addItem(new EaseJQuerySubmitButton('Ok' , 'Změnit heslo'));

    $LoginForm->FillUp($_POST);

    $oPage->column2->addItem( new \Ease\HtmlFieldSet(_('změna hesla'), $LoginForm));
}

$oPage->addItem(new LQPageBottom());

$oPage->Draw();
?>
