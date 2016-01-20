<?php

/**
 * LinkQuick - vložení nové adresy do databáze
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2011
 */
require_once 'includes/LQInit.php';
require_once 'LQEncoder.php';
require_once 'Ease/EaseMail.php';
require_once 'classes/LQDateTimeSelector.php';

$Encoder = new LQEncoder();

$OK = $OPage->GetRequestValue('OK');
$Notify = $OPage->GetRequestValue('Notify');
$Domain = $OPage->GetRequestValue('Domain');
$NewURL = $OPage->GetRequestValue('NewURL');
if ($OK) {
    if (strlen(trim($NewURL)) && preg_match("/^(?:[;\/?:@&=+$,]|(?:[^\W_]|[-_.!~*#\()\[\] ])|(?:%[\da-fA-F]{2}))*$/", $NewURL)) {
        $Encoder->SetDataValue('ExpireDate', $OPage->GetRequestValue('ExpireDate'));
        if ($Encoder->SaveUrl($NewURL,$Domain)) {
            $OUser->AddStatusMessage(_('Url uloženo do databáze') . ': ' . $NewURL, 'success');
            if ($Notify) {
                $Mail = new EaseMail($Notify, _('LinkQuick: Zkratka vašeho URL'), $NewURL . "\n = \n" . $Encoder->GetShortCutURL());
                $Mail->Send();
            }
            $NewURL = '';
            $OPage->AddItem( new EaseJQueryDialog('NewUrlSuccess', _('Zkratka byla vytvořena'), $Encoder->getDataValue('title'), 'ui-icon-circle-check', new EaseHtmlATag($Encoder->GetCode(), 'http://' . LQEncoder::getDomain() . $Encoder->GetShortCutURL())));
        }
    } else {
        $OUser->AddStatusMessage(_('Toto není webová adresa!') . ': ' . $NewURL, 'warning');
    }
}

//Hlavička stránek
$OPage->AddItem(new LQPageTop(_('LinkQuick: Zkracovač pro vaše URL')));

$Domains = LQEncoder::getDomainList();

$ActualDomain = LQEncoder::getDomain();

if(!in_array($ActualDomain, $Domains)){
    $Domains[] = $ActualDomain;
}


$DomainTabs = new EaseJQueryUITabs('DomainTabs');


foreach ($Domains as $Domain) {
    $NextCode = LQEncoder::getNextCode($Domain);

    $AddNewFrame = new EaseHtmlFieldSet(_('Vytvořit novou zkratku').' '.$Domain);


    $AddNewFrame->addItem(new EaseLabeledTextInput('NewURL', $NewURL, _('url, které chceš zkrátit'), array('size' => 80,'style'=>'font-size: 30px; height: 40px; width: 100%;')));

    $MailTo = '';
    if ($OUser->GetSettingValue('SendMail')) {
        $MailTo = $OUser->GetUserEmail();
    }

//    $AddNewFrame->AddItem(new LQLabeledDateTimeSelector('ExpireDate'.  str_replace('/','_',$Domain), $OPage->GetRequestValue('ExpireDate'), _('Datum expirace')));

    $AddNewFrame->addItem(new EaseLabeledTextInput('Notify', $MailTo, _('Odeslat potvrzení mailem na adresu'), $Notify));
    $AddNewFrame->addItem(new EaseJQuerySubmitButton('OK', 'Ok', 'Ok'));
    $AddNewFrame->addItem( new EaseHtmlInputHiddenTag('Domain', $Domain) );
    $AddNewForm = new EaseHtmlForm('AddNewURL'.$Domain, NULL, NULL, $AddNewFrame);

    $DomainTabs->addTab($Domain . $NextCode, $AddNewForm);
}

$OPage->addItem($DomainTabs);


$OPage->addItem(new LQPageBottom());


$OPage->draw();
?>
