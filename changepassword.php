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

$OPage->OnlyForLogged(); //Pouze pro přihlášené
$FormOK = true;

if (!isset($_POST['password']) || !strlen($_POST['password'])) {
    $OUser->AddStatusMessage('Prosím zadejte nové heslo');
    $FormOK = false;
} else {
    if ($_POST['password'] == $OUser->GetUserLogin()) {
        $OUser->AddStatusMessage('Heslo se nesmí shodovat s přihlašovacím jménem', 'waring');
        $FormOK = false;
    }
    /* TODO:
      if(!$OUser->PasswordCrackCheck($_POST['password'])){
      $OUser->AddStatusMessage('Heslo není dostatečně bezpečné');
      $FormOK = false;
      }
     */
}
if (!isset($_POST['passwordConfirm']) || !strlen($_POST['passwordConfirm'])) {
    $OUser->AddStatusMessage('Prosím zadejte potvrzení hesla');
    $FormOK = false;
}
if ((isset($_POST['passwordConfirm']) && isset($_POST['password'])) && ($_POST['passwordConfirm'] != $_POST['password'])) {
    $OUser->AddStatusMessage('Zadaná hesla se neshodují', 'waring');
    $FormOK = false;
}

if (!isset($_POST['CurrentPassword'])) {
    $OUser->AddStatusMessage('Prosím zadejte stávající heslo');
    $FormOK = false;
} else {
    if (!$OUser->PasswordValidation($_POST['CurrentPassword'], $OUser->GetDataValue($OUser->PasswordColumn))) {
        $OUser->AddStatusMessage('Stávající heslo je neplatné', 'warning');
        $FormOK = false;
    }
}


$OPage->AddItem(new LQPageTop(_('Změna hesla uživatele')));

if ($FormOK && isset($_POST)) {
    $OUser->SetDataValue($OUser->PasswordColumn, $OUser->EncryptPassword($_POST['password']));
    if ($OUser->SaveToMySQL()) {
        $OUser->AddStatusMessage('Heslo bylo změněno', 'success');

        $Email = $OPage->AddItem(new EaseMail($OUser->GetDataValue($OUser->MailColumn), 'Změněné heslo pro FragCC'));
        $Email->AddItem("Vážený zákazníku vaše přihlašovací údaje byly změněny:\n");

        $Email->AddItem(' Login: ' . $OUser->GetUserLogin() . "\n");
        $Email->AddItem(' Heslo: ' . $_POST['password'] . "\n");

        $Email->Send();
    }
} else {
    $LoginForm = new EaseHtmlForm(NULL);

    $LoginForm->AddItem(new EaseLabeledPasswordInput('CurrentPassword', NULL, _('Stávající heslo')));

    $LoginForm->AddItem(new EaseLabeledPasswordStrongInput('password', NULL, _('Nové heslo') . ' *'));
    $LoginForm->AddItem(new EaseLabeledPasswordControlInput('passwordConfirm', NULL, _('potvrzení hesla') . ' *', array('id' => 'confirmation')));

    $LoginForm->AddItem(new EaseJQuerySubmitButton('Ok' , 'Změnit heslo'));

    $LoginForm->FillUp($_POST);

    $OPage->column2->addItem( new EaseHtmlFieldSet(_('změna hesla'), $LoginForm));
}

$OPage->AddItem(new LQPageBottom());

$OPage->Draw();
?>
