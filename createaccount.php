<?php

/**
 * Založení nového accountu
 * @package LinkQuick
 */
require_once 'includes/LQInit.php';
require_once 'Ease/EaseMail.php';
require_once 'Ease/EaseJQueryWidgets.php';

if (!is_object($OPage->MyDbLink)) {
    $OPage->TakeMyTable();
}


$process = false;
if (isset($_POST) && count($_POST)) {
    $process = true;

    $firstname = addslashes($_POST['firstname']);
    $lastname = addslashes($_POST['lastname']);
    $email_address = addslashes(strtolower($_POST['email_address']));

    if (isset($_POST['parent'])) {
        $CustomerParent = addslashes($_POST['parent']);
    } else {
        $CustomerParent = $OUser->GetUserID();
    }
    $login = addslashes($_POST['login']);
    if (isset($_POST['password']))
        $password = addslashes($_POST['password']);
    if (isset($_POST['confirmation']))
        $confirmation = addslashes($_POST['confirmation']);

    $error = false;

    if (strlen($firstname) < 2) {
        $error = true;
        $OUser->AddStatusMessage(_('jméno je příliš krátké'), 'warning');
    }

    if (strlen($lastname) < 2) {
        $error = true;
        $OUser->AddStatusMessage(_('příjmení je příliš krátké'), 'warning');
    }

    if (strlen($firstname) + strlen($lastname) > 30) {
        $error = true;
        $OUser->AddStatusMessage(_('jména jsou dohromady příliš dlouhá'), 'warning');
    }

    if (strlen($email_address) < 5) {
        $error = true;
        $OUser->AddStatusMessage(_('mailová adresa je příliš krátká'), 'warning');
    } else {
        if (!$OUser->IsEmail($email_address, true)) {
            $error = true;
            $OUser->AddStatusMessage(_('email address check error'), 'warning');
        } else {
            $check_email = $OPage->MyDbLink->QueryToArray("SELECT COUNT(*) AS total FROM users WHERE email = '" . $OPage->EaseAddSlashes($email_address) . "'");
            if ($check_email[0]['total'] > 0) {
                $error = true;
                $OUser->AddStatusMessage(_('email address allready registred'), 'warning');
            }
        }
    }



    if (strlen($password) < 5) {
        $error = true;
        $OUser->AddStatusMessage(_('heslo je příliš krátké'), 'warning');
    } elseif ($password != $confirmation) {
        $error = true;
        $OUser->AddStatusMessage(_('kontrola hesla nesouhlasí'), 'warning');
    }

    $OPage->MyDbLink->ExeQuery('SELECT id FROM users WHERE login=\'' . $OPage->EaseAddSlashes($login) . '\'');
    if ($OPage->MyDbLink->GetNumRows()) {
        $error = true;
        $OUser->AddStatusMessage(sprintf(_('Zadané uživatelské jméno %s je již v databázi použito. Zvolte prosím jiné.'),$login), 'warning');
    }

    if ($error == false) {

        $NewOUser = new LQUser();
        //TODO zde by se měly doplnit defaultní hodnoty z konfiguráku Registry.php
        $CustomerData = array(
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email_address,
            'password' => $NewOUser->EncryptPassword($password),
            'parent' => (int) $CustomerParent,
            'login' => $login);
        
        $CustomerID = $NewOUser->InsertToMySQL($CustomerData);

        if ($CustomerID) {
            $NewOUser->SetMyKey($CustomerID);
            
            $OUser->AddStatusMessage(_('Zákaznický účet byl vytvořen'), 'success');
            $NewOUser->LoginSuccess();

            $Email = $OPage->AddItem(new EaseMail($NewOUser->GetDataValue('email'), 'Potvrzení registrace'));
            $Email->SetMailHeaders(array('From' => EMAIL_FROM));
            $Email->AddItem(new EaseHtmlDivTag(null, "Právě jste byl/a zaregistrován/a do Apl   ikace LB s těmito přihlašovacími údaji:\n"));
            $Email->AddItem(new EaseHtmlDivTag(null, ' Login: ' . $NewOUser->GetUserLogin() . "\n"));
            $Email->AddItem(new EaseHtmlDivTag(null, ' Heslo: ' . $_POST['password'] . "\n"));
            $Email->Send();

            $Email = $OPage->AddItem(new EaseShopMail(SEND_ORDERS_TO, 'Nová registrace do LBu: ' . $NewOUser->GetUserLogin()));
            $Email->SetMailHeaders(array('From' => EMAIL_FROM));
            $Email->AddItem(new EaseHtmlDivTag(null, "Do LBu právě zaregistrován nový uživatel:\n"));
            $Email->AddItem(new EaseHtmlDivTag('login', ' Login: ' . $NewOUser->GetUserLogin() . "\n"));
            $Email->AddItem($NewOUser->CustomerAddress);
            $Email->Send();

            $_SESSION['User'] = clone $NewOUser;
            $OPage->Redirect('Main.php');
            exit;
        } else {
            $OUser->AddStatusMessage(_('Zápis do databáze se nezdařil!'), 'error');
            $Email = $OPage->AddItem(new EaseMail(constant('SEND_ORDERS_TO'), 'Registrace uzivatel se nezdařila'));
            $Email->AddItem(new EaseHtmlDivTag('Fegistrace', $OPage->PrintPre($CustomerData)));
            $Email->Send();
        }
    }
}


$OPage->AddCss('
input.ui-button { width: 220px; }
');


$OPage->AddItem(new LQPageTop(_('Registrace')));

$OPage->AddItem(new EaseHtmlDivTag('WelcomeHint', _('Registrací získáš možnost editovat a mazat své uložené zkratky')));

$RegFace = $OPage->column2->addItem( new EaseHtmlDivTag('RegFace') );


$RegForm = $RegFace->AddItem(new EaseHtmlForm('create_account', 'CreateAccount.php'));
$RegForm->SetTagID('LoginForm');
if ($OUser->GetUserID()) {
    $RegForm->AddItem(new EaseHtmlInputHiddenTag('u_parent', $OUser->GetUserID()));
}

$Account = new EaseHtmlH3Tag(_('Účet'));
$Account->AddItem(new EaseLabeledTextInput('login',NULL,_('přihlašovací jméno') . ' *'));
$Account->AddItem(new EaseLabeledPasswordStrongInput('password',NULL,_('heslo') . ' *'));
$Account->AddItem(new EaseLabeledPasswordControlInput('confirmation',NULL,_('potvrzení hesla') . ' *',array('id'=>'confirmation')));

$Personal = new EaseHtmlH3Tag(_('Osobní'));
$Personal->AddItem(new EaseLabeledTextInput('firstname',NULL,_('jméno') . ' *'));
$Personal->AddItem(new EaseLabeledTextInput('lastname',NULL,_('příjmení') . ' *'));
$Personal->AddItem(new EaseLabeledTextInput('email_address', NULL, _('emailová adresa') . ' *' . ' (pouze malými písmeny)'));

$RegForm->AddItem(new EaseHtmlDivTag('Account', $Account));
$RegForm->AddItem(new EaseHtmlDivTag('Personal', $Personal));
$RegForm->AddItem(new EaseHtmlDivTag('Submit', new EaseJQuerySubmitButton('Register',_('Registrovat'),_('dokončit registraci'),array())));


if (isset($_POST)) {
    $RegForm->FillUp($_POST);
}

$OPage->AddItem(new LQPageBottom());
$OPage->Draw();
?>

