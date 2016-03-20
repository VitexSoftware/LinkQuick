<?php

/**
 * Změna hesla uživatele
 * @author Vítězslav Dvořák <vitex@hippy.cz>
 * @copyright Vitex Software © 2011
 * @package LinkQuick
 * @subpackage WEBUI
 */

namespace LQ;



$oPage->onlyForLogged(); //Pouze pro přihlášené
$formOK = true;

if (!isset($_POST['password']) || !strlen($_POST['password'])) {
    $oUser->addStatusMessage(_('Please enter new password'));
    $formOK = false;
} else {
    if ($_POST['password'] == $oUser->getUserLogin()) {
        $oUser->addStatusMessage(_('Password cannot be like username'), 'waring');
        $formOK = false;
    }
    /* TODO:
      if(!$OUser->PasswordCrackCheck($_POST['password'])){
      $OUser->addStatusMessage('Heslo není dostatečně bezpečné');
      $FormOK = false;
      }
     */
}
if (!isset($_POST['passwordConfirm']) || !strlen($_POST['passwordConfirm'])) {
    $oUser->addStatusMessage(_('Please enter password confirmation'));
    $formOK = false;
}
if ((isset($_POST['passwordConfirm']) && isset($_POST['password'])) && ($_POST['passwordConfirm'] != $_POST['password'])) {
    $oUser->addStatusMessage(_('Pasword confirmation does not match', 'waring'));
    $formOK = false;
}

if (!isset($_POST['CurrentPassword'])) {
    $oUser->addStatusMessage(_('Please enter current passweod'));
    $formOK = false;
} else {
    if (!$oUser->PasswordValidation($_POST['CurrentPassword'], $oUser->getDataValue($oUser->passwordColumn))) {
        $oUser->addStatusMessage(_('Current password is invalid'), 'warning');
        $formOK = false;
    }
}


$oPage->addItem(new PageTop(_('User password change')));

if ($formOK && isset($_POST)) {
    $oUser->setDataValue($oUser->passwordColumn, $oUser->EncryptPassword($_POST['password']));
    if ($oUser->saveToSQL()) {
        $oUser->addStatusMessage(_('Password was changed'), 'success');

        $email = $oPage->addItem(new \Ease\Mail($oUser->getDataValue($oUser->mailColumn), _('Changed password')));
        $email->addItem(_('Dear user, your password was changed') . "\n");

        $email->addItem(_('Login') . ': ' . $oUser->getUserLogin() . "\n");
        $email->addItem(_('Password') . ': ' . $_POST['password'] . "\n");

        $email->send();
    }
} else {
    $pwchform = new \Ease\TWB\Form(NULL);

    $pwchform->addInput(new \Ease\Html\InputPasswordTag('CurrentPassword'), NULL, _('Current password'));
    $pwchform->addInput(new \Ease\Html\InputPasswordTag('password'), NULL, _('New Password'));
    $pwchform->addInput(new \Ease\Html\InputPasswordTag('passwordConfirm'), NULL, _('New Password confirmation'));


    $pwchform->addItem(new \Ease\TWB\SubmitButton(_('Change the password')));

    $pwchform->fillUp($_POST);

    $oPage->column2->addItem(new \Ease\TWB\Panel(_('Password Change'), $pwchform, 'warning'));
}

$oPage->addItem(new PageBottom());

$oPage->draw();
