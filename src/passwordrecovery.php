<?php
/**
 * Reset hesla
 * @package LevyBreh
 */
require_once 'includes/LQInit.php';
require_once 'Ease/EaseMail.php';
require_once 'Ease/\Ease\HtmlForm.php';
$Success = false;

$EmailTo = $oPage->getPostValue('Email');

if ($EmailTo) {
    $oPage->TakeMyTable();
    $UserEmail = $oPage->EaseAddSlashes($EmailTo);
    $UserFound = $oPage->MyDbLink->QueryToArray('SELECT u_id,u_username FROM users WHERE email=\''.$UserEmail.'\'');
    if (count($UserFound)) {
        $UserID      = intval($UserFound[0]['u_id']);
        $UserLogin   = $UserFound[0]['u_username'];
        $NewPassword = $oPage->RandomString(8);

        $PassChanger = new LBUser($UserID);
        $PassChanger->PasswordChange($NewPassword);

        $email = $oPage->addItem(new EaseShopMail($UserEmail,
            _('Nové heslo pro ').$_SERVER['SERVER_NAME']));
        $email->addItem(_("Tvoje přihlašovací údaje byly změněny:\n"));

        $email->addItem(' Login: '.$UserLogin."\n");
        $email->addItem(' Heslo: '.$NewPassword."\n");

        $email->send();

        $oUser->addStatusMessage('Tvoje nové heslo vám bylo odesláno mailem na zadanou adresu <strong>'.$_REQUEST['Email'].'</strong>');
        $Success = true;
    } else {
        $oUser->addStatusMessage('Promiňnte, ale email <strong>'.$_REQUEST['Email'].'</strong> nebyl v databázi nalezen',
            'warning');
    }
} else {
    $oUser->addStatusMessage('Zadejte prosím váš eMail.');
}


$oPage->addItem(new LQPageTop('Obnova zapomenutého hesla'));



if (!$Success) {
    $oPage->addItem('<h1>Zapoměl jsem své heslo!</h1>');

    $oPage->addItem('Zapoměl jste heslo? Vložte svou e-mailovou adresu, kterou jste zadal při registraci a my Vám pošleme nové.');

    $EmailForm = $oPage->addItem(new \Ease\HtmlForm('PassworRecovery'));
    $EmailForm->addItem('Email: ');
    $EmailForm->addItem(new \Ease\HtmlInputTextTag('Email', NULL,
        ['size' => '40']));
    $EmailForm->addItem(new \Ease\HtmlInputSubmitTag('ok',
        _('Zaslat nové heslo')));

    if (isset($_POST)) {
        $EmailForm->FillUp($_POST);
    }
} else {
    $oPage->addItem(new \Ease\HtmlATag('Login.php', _('Pokračovat')));
}

$oPage->addItem(new LQPageBottom());

$oPage->Draw();
?>
