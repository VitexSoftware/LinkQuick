<?php

/**
 * Přehled vložených adres uživatele
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2011
 */
require_once 'includes/LQInit.php';
require_once 'LQEncoder.php';
require_once 'Ease/EaseMail.php';
require_once 'LQMyLinksEditor.php';
require_once 'LQDeleteConfirm.php';

$LinksEditor = new LQMyLinksEditor();
$LinksEditor->setUpUser($oUser);

$DeleteID = $oPage->getRequestValue('DeleteID','int');
if(!is_null($DeleteID)){
    $LinksEditor->setMyKey($DeleteID);
    $LinksEditor->setDataValue('deleted', 1);
    if($LinksEditor->updateToMySQL()){
        $oUser->addStatusMessage(_('zkratka byla odstraněna'),'success');
    } else {
        $oUser->addStatusMessage(_('zkratka nebyla odstraněna'),'warning');
    }
}

$oPage->addItem(new LQPageTop(_('LinkQuick: Zkracovač pro vaše URL')));

$oPage->addItem($LinksEditor);

$oPage->addItem(new LQDeleteConfirm('DeleteConfirm'));

$oPage->addItem(new LQPageBottom());


$oPage->Draw();
?>
