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
$LinksEditor->SetUpUser($OUser);

$DeleteID = $OPage->GetRequestValue('DeleteID','int');
if(!is_null($DeleteID)){
    $LinksEditor->SetMyKey($DeleteID);
    if($LinksEditor->DeleteFromMySQL()){
        $OUser->AddStatusMessage(_('zkratka byla odstraněna'),'success');
    } else {
        $OUser->AddStatusMessage(_('zkratka nebyla odstraněna'),'warning');
    }
}

$OPage->AddItem(new LQPageTop(_('LinkQuick: Zkracovač pro vaše URL')));

$OPage->AddItem($LinksEditor);

$OPage->AddItem(new LQDeleteConfirm('DeleteConfirm'));

$OPage->AddItem(new LQPageBottom());


$OPage->Draw();
?>
