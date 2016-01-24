<?php
/**
 * Přesměrovávač LinkQuick
 * 
 * @author    Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2011
 */


require_once 'includes/LQInit.php';
require_once 'LQEncoder.php';


$Code = $oPage->getRequestValue('U');
$encoder = new Encoder();
$encoder->setCode(strtolower($Code));

$Url = $encoder->getURLByCode();
if(!$Url){
    header('HTTP/1.0 404 Not Found',404);
    $oPage->addItem(new LQPageTop(_('LinkQuick: '._('Zkratka nenalezena'))));
    $oPage->addItem(new \Ease\HtmlDivTag('Sorry',_('Zkratka nenalezena')) );
    $oPage->addItem(new LQPageBottom());
    $oPage->Draw();
} else {
    $Expired = $encoder->getDataValue('Expired');
    if(strlen($Expired) && ($Expired != '0000-00-00 00:00:00')){
        header('HTTP/1.0 410 Expired',410);
        $oPage->addItem(new LQPageTop(_('LinkQuick: '._('Zkratka vypršela'))));
        $oPage->addItem(new \Ease\HtmlDivTag('Sorry',_('Zkratka vypršela')) );
        $oPage->addItem(new LQPageBottom());
        $oPage->Draw();
    } else {
        $encoder->UpdateCounter();
        header('Location: '.$Url);
    }
}

?>
