<?php

/**
 * LinkQuick - vložení nové adresy do databáze
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2011
 */
require_once 'includes/LQInit.php';
require_once 'LQEncoder.php';
require_once 'Ease/EaseMail.php';

$Encoder = new LQEncoder();

$OK = $OPage->GetRequestValue('OK');
$Notify = $OPage->GetRequestValue('Notify');
if ($OK) {
    $NewURL = $OPage->GetRequestValue('NewURL');
    if (strlen(trim($NewURL)) && preg_match("/^(?:[;\/?:@&=+$,]|(?:[^\W_]|[-_.!~*\()\[\] ])|(?:%[\da-fA-F]{2}))*$/", $NewURL)) {
        $Encoder->SaveUrl($NewURL);
        if ($Encoder->RecordID) {
            $OUser->AddStatusMessage(_('Url uloženo do databáze').': ' . $NewURL, 'success');
            if ($Notify) {
                $Mail = new EaseMail($Notify, _('LinkQuick: Zkratka vašeho URL'), $NewURL . "\n = \n" . $Encoder->GetShortCutURL());
                $Mail->Send();
            }
        }
    } else {
        $OUser->AddStatusMessage(_('Toto není webová adresa!').': ' . $NewURL, 'warning');
    }
}

//Hlavička stránek
$OPage->AddItem(new LQPageTop(_('LinkQuick: Zkracovač pro vaše URL')));
$OPage->AddItem(new EaseHtmlImgTag('images/LinkQuick.png', 'LinkQuick', 378, 68));

$AddNewFrame = new EaseHtmlFieldSet(_('Vytvořit novou zkratku'), new EaseHtmlInputTextTag('NewURL',NULL,array('size'=>80)));

$MailTo = ''; 
if($OUser->GetSettingValue('SendMail')){
    $MailTo = $OUser->GetUserEmail();
}

$AddNewFrame->AddItem(new EaseLabeledTextInput('Notify',$MailTo,_('Odeslat potvrzení mailem na adresu'),$Notify));
$AddNewFrame->AddItem(new EaseJQuerySubmitButton('OK', 'Ok', 'Ok'));
$AddNewForm = new EaseHtmlForm('AddNewURL', NULL, NULL, $AddNewFrame);

if ($OK && $Encoder->GetCode()) {
    $OPage->AddItem( new EaseHtmlDivTag('result', array ( _('Zkratka byla vytvořena'),': ', new EaseHtmlATag ( $Encoder->GetCode() , $Encoder->GetShortCutURL()))));
}

$OPage->AddItem($AddNewForm);

$OPage->AddItem(_('Počet adres v databázi').': ' . $Encoder->MyDbLink->GetTableNumRows());

$OPage->AddItem(new LQPageBottom());


$OPage->Draw();
?>
