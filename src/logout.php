<?php
/**
 * Odhlašovací stránka
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2011
 * @package LinkQuick
 */

require_once 'includes/LQInit.php';

unset($_SESSION['access_token']); //Twitter OAuth 

if($oUser->getUserID()){
    $oUser->Logout();
    $MessagesBackup = $oUser->getStatusMessages(TRUE);
    $oUser = new EaseAnonym();
    $oUser->addStatusMessages($MessagesBackup);
}

$oPage->addItem(new LQPageTop(_('Odhlášení')));

$oPage->column2->addItem(new \Ease\HtmlDivTag(NULL,_('Děkujeme za vaši přízeň a těšíme se na další návštěvu')));

$oPage->addItem(new LQPageBottom());

$oPage->Draw();


?>
