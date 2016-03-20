<?php

namespace LQ;

require_once 'includes/LQInit.php';

/**
 * Account Create Page
 *
 * @package    LinkQuick
 * @subpackage LQ
 * @author     Vitex <vitex@hippy.cz>
 * @copyright  2009-2016 info@vitexsoftware.cz (G)
 */
$firstname     = $oPage->getPostValue('firstname');
$lastname      = $oPage->getPostValue('lastname');
$email_address = $oPage->getPostValue('email_address');
$login         = $oPage->getPostValue('login');
$password      = $oPage->getPostValue('password');
$confirmation  = $oPage->getPostValue('confirmation');


if ($oPage->isPosted()) {
    $error = false;

    if (!$oUser->IsEmail($email_address, true)) {
        $error = true;
        $oUser->addStatusMessage(_('email address check error'), 'warning');
    } else {
        $check_email = \Ease\Shared::db()->queryToValue("SELECT COUNT(*) AS total FROM user WHERE email = '".$oPage->EaseAddSlashes($email_address)."'");
        if ($check_email > 0) {
            $error = true;
            $oUser->addStatusMessage(_('email address allready registred'),
                'warning');
        }
    }

    if (strlen($password) < 5) {
        $error = true;
        $oUser->addStatusMessage(_('password is too short'), 'warning');
    } elseif ($password != $confirmation) {
        $error = true;
        $oUser->addStatusMessage(_('password confirmation does not match'),
            'warning');
    }

    $allreadyExists = \Ease\Shared::db()->queryToValue('SELECT id FROM user WHERE login=\''.$oPage->EaseAddSlashes($login).'\'');
    if ($allreadyExists) {
        $error = true;
        $oUser->addStatusMessage(sprintf(_('Given Username %s already exists'),
                $login), 'warning');
    }

    if ($error == false) {

        $newOUser     = new User();
        $customerData = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email_address,
            'password' => $newOUser->encryptPassword($password),
            'login' => $login];

        $customerID = $newOUser->insertToSQL($customerData);

        if ($customerID) {
            $newOUser->setMyKey($customerID);

            $oUser->addStatusMessage(_('Account Was Created'), 'success');
            $newOUser->loginSuccess();

            $email = $oPage->addItem(new \Ease\Mail($newOUser->getDataValue('email'),
                _('New LinkQuick account')));
            $email->setMailHeaders(['From' => EMAIL_FROM]);
            $email->addItem(new \Ease\Html\Div(
                _("Welcome to LinkQuick")."\n"));
            $email->addItem(new \Ease\Html\Div(
                _('Login').': '.$newOUser->getUserLogin()."\n"));
            $email->addItem(new \Ease\Html\Div(
                _('Password').': '.$password."\n"));
            $email->send();


            \Ease\Shared::user($newOUser);  //Assign newly created user as default
            $oPage->redirect('index.php');
            exit;
        } else {
            $oUser->addStatusMessage(_('Error creating account'), 'error');
        }
    }
}


$oPage->addItem(new PageTop(_('Account Registration')));

$oPage->column1->addItem(new \Ease\Html\Div(_('Register to edit your shortcuts'),
    ['id' => 'WelcomeHint']));

$regBlock = $oPage->column2->addItem(new \Ease\TWB\Panel(_('Registration'),
    'success'));


$regForm = $regBlock->addItem(new \Ease\TWB\Form('create_account',
    'createaccount.php'));
$regForm->setTagID('LoginForm');

$regForm->addItem(new \Ease\Html\H3Tag(_('Account')));
$regForm->addInput(new \Ease\Html\InputTextTag('login', $login), _('Login'),
    null, _('Requied'));
$regForm->addInput(new \Ease\Html\InputPasswordTag('password', $password),
    _('Password'), null, _('Requied'));
$regForm->addInput(new \Ease\Html\InputPasswordTag('confirmation', $confirmation),
    _('Password confirm'), null, _('Requied'));

$regForm->addItem(new \Ease\Html\H3Tag(_('Personal')));
$regForm->addInput(new \Ease\Html\InputTextTag('firstname', $firstname),
    _('Name'));
$regForm->addInput(new \Ease\Html\InputTextTag('lastname', $lastname),
    _('Last name'));
$regForm->addInput(new \Ease\Html\InputTextTag('email_address', $email_address,
    ['type' => 'email']), _('Email Address'));

$regForm->addItem(new \Ease\TWB\SubmitButton(_('Register'), 'success'));

$oPage->addItem(new PageBottom());
$oPage->draw();


