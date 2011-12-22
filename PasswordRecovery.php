<?php
/**
 * Reset hesla
 * @package LevyBreh
 */


require_once 'includes/LBBegin.php';
require_once 'Ease/EaseMail.php';
require_once 'Ease/EaseHtmlForm.php';
$Success = false;

$EmailTo = $OPage->GetPostValue('Email');

if($EmailTo) {
    $OPage->TakeMyTable();
    $UserEmail = $OPage->EaseAddSlashes($EmailTo);
    $UserFound = $OPage->MyDbLink->QueryToArray('SELECT u_id,u_username FROM users WHERE email=\''. $UserEmail .'\'');
    if(count($UserFound)) {
        $UserID = intval($UserFound[0]['u_id']);
        $UserLogin = $UserFound[0]['u_username'];
        $NewPassword = $OPage->RandomString(8);

        $PassChanger = new LBUser($UserID);
        $PassChanger->PasswordChange($NewPassword);

        $Email = $OPage->AddItem(new EaseShopMail($UserEmail,_('Nové heslo pro ').$_SERVER['SERVER_NAME']));
        $Email->AddItem(_("Tvoje přihlašovací údaje byly změněny:\n"));
        
        $Email->AddItem(' Login: '.$UserLogin."\n");
        $Email->AddItem(' Heslo: '.$NewPassword."\n");

        $Email->Send();
        
        $OUser->AddStatusMessage('Tvoje nové heslo vám bylo odesláno mailem na zadanou adresu <strong>'.$_REQUEST['Email'].'</strong>');
        $Success = true;
    } else {
        $OUser->AddStatusMessage('Promiňnte, ale email <strong>'.$_REQUEST['Email'].'</strong> nebyl v databázi nalezen','warning');
    }
} else {
    $OUser->AddStatusMessage('Zadejte prosím váš eMail.');
}


$OPage->AddItem(new LBPageTop('Obnova zapomenutého hesla'));



if(!$Success) {
    $OPage->AddItem('<h1>Zapoměl jsem své heslo!</h1>');

    $OPage->AddItem('Zapoměl jste heslo? Vložte svou e-mailovou adresu, kterou jste zadal při registraci a my Vám pošleme nové.');

    $EmailForm = $OPage->AddItem(new EaseHtmlForm('PassworRecovery'));
    $EmailForm->AddItem('Email: ');
    $EmailForm->AddItem(new EaseHtmlInputTextTag('Email', NULL, array('size' => '40')));
    $EmailForm->AddItem(new EaseHtmlInputSubmitTag('ok', _('Zaslat nové heslo')));

    if (isset($_POST)) {
        $EmailForm->FillUp($_POST);
    }
} else {
    $OPage->AddItem(new EaseHtmlATag('Login.php', _('Pokračovat')));
}

$OPage->AddItem(new LBPageBottom());

$OPage->Draw();
?>
