<?php
/**
 * Odhlašovací stránka
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2011
 * @package LinkQuick
 */

require_once 'includes/LQInit.php';

unset($_SESSION['access_token']); //Twitter OAuth 

if($OUser->GetUserID()){
    $OUser->Logout();
    $MessagesBackup = $OUser->GetStatusMessages(TRUE);
    $OUser = new EaseAnonym();
    $OUser->AddStatusMessages($MessagesBackup);
}

$OPage->AddItem(new LQPageTop(_('Odhlášení')));

$OPage->AddItem(new EaseHtmlDivTag(NULL,_('Děkujeme za vaši přízeň a těšíme se na další návštěvu')));

$OPage->AddItem(new LQPageBottom());

$OPage->Draw();


?>
