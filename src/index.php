<?php
/**
 * LinkQuick - Enter new addrese into database
 *
 * @package    LinkQuick
 * @subpackage LQ
 * @author     Vitex <vitex@hippy.cz>
 * @copyright  2009-2016 info@vitexsoftware.cz (G)
 */

namespace LQ;

require_once 'includes/LQInit.php';


$encoder = new Encoder();

$ok     = $oPage->getRequestValue('OK');
$notify = $oPage->getRequestValue('Notify');
$domain = $oPage->getRequestValue('Domain');
$newURL = $oPage->getRequestValue('NewURL');
if ($ok) {
    if (strlen(trim($newURL)) && preg_match("/^(?:[;\/?:@&=+$,]|(?:[^\W_]|[-_.!~*#\()\[\] ])|(?:%[\da-fA-F]{2}))*$/",
            $newURL)) {
        $encoder->setDataValue('ExpireDate',
            $oPage->getRequestValue('ExpireDate'));
        if ($encoder->saveUrl($newURL, $domain)) {
            $oUser->addStatusMessage(_('Url was saved').': '.$newURL, 'success');
            if ($notify) {
                $mail = new EaseMail($notify, _('LinkQuick: You URL Shortcut'),
                    $newURL."\n = \n".$encoder->getShortCutURL());
                $mail->send();
            }
            $newURL = '';
            $oPage->addItem(new EaseJQueryDialog('NewUrlSuccess',
                _('Zkratka byla vytvořena'), $encoder->getDataValue('title'),
                'ui-icon-circle-check',
                new \Ease\HtmlATag($encoder->getCode(),
                'http://'.LQEncoder::getDomain().$encoder->getShortCutURL())));
        }
    } else {
        $oUser->addStatusMessage(_('This is not an web address!').': '.$newURL,
            'warning');
    }
}

//Hlavička stránek
$oPage->addItem(new PageTop(_('LinkQuick: Your URL shortener')));

$domains = Encoder::getDomainList();

$actualDomain = Encoder::getDomain();

if (!in_array($actualDomain, $domains)) {
    $domains[] = $actualDomain;
}


$domainTabs = new \Ease\TWB\Tabs('DomainTabs');


foreach ($domains as $domain) {
    $nextCode = Encoder::getNextCode($domain);

    $addNewForm = new \Ease\TWB\Form('AddNewURL'.$domain);
    $addNewForm->addInput(new \Ease\Html\InputTextTag('NewURL', $newURL,
        ['size' => 80, 'style' => 'font-size: 30px; height: 40px; width: 100%;']),
        _('URL to short')
    );

    $mailTo = '';
    if ($oUser->getSettingValue('SendMail')) {
        $mailTo = $oUser->getUserEmail();
    }

//    $AddNewFrame->addItem(new LQLabeledDateTimeSelector('ExpireDate'.  str_replace('/','_',$Domain), $OPage->getRequestValue('ExpireDate'), _('Datum expirace')));

    $addNewForm->addInput(new \Ease\Html\InputTextTag('Notify', $mailTo),
        _('Send email confirmation to'), $notify);
    $addNewForm->addItem(new \Ease\TWB\SubmitButton(_('OK')));
    $addNewForm->addItem(new \Ease\Html\InputHiddenTag('Domain', $domain));

    $addNewFrame = new \Ease\TWB\Panel(_('New shortcut').' '.$domain, 'success',
        $addNewForm);

    $domainTabs->addTab($domain.$nextCode, $addNewFrame);
}

$oPage->container->addItem($domainTabs);


$oPage->addItem(new PageBottom());


$oPage->draw();

