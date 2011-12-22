<?php
/**
 * Přesměrovávač LinkQuick
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2011
 */


require_once 'includes/LQInit.php';
require_once 'LQEncoder.php';


$Code = $OPage->GetRequestValue('U');
$Encoder = new LQEncoder();
$Encoder->SetCode(strtolower($Code));

$Url = $Encoder->GetURLByCode();
if(!$Url){
    header('HTTP/1.0 404 Not Found',404);
    $OPage->AddItem(new LQPageTop(_('LinkQuick: '._('Zkratka nenalezena'))));
    $OPage->AddItem(new EaseHtmlDivTag('404',_('Zkratka nenalezena')) );
    $OPage->AddItem(new LQPageBottom());
    $OPage->Draw();
} else {
    $Encoder->UpdateCounter();
    header('Location: '.$Url);
}

?>
