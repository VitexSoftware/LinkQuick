<?php
/**
 * Ukázková prázdná stránka LinkQuick
 * Aneb jak do stránky vkádat objekty a jak jim měnit vlastnosti
 *
 * @author $Author: vitex $
 * @version $Revision: 1973 $
 * @copyright Vitex@hippy.cz (G) 2009
 *
 * $Id $
 * $Date: 2010-02-12 16:22:57 +0100 (Fri, 12 Feb 2010) $
 */

//Vložení initu aplikace
require_once 'includes/LQInit.php';

//Pouze pro přihlášené
//$OPage->OnlyForLogged();

$LQUser->addStatusMessage('Vzorová stránka administrace');
$LQUser->addStatusMessage('Úspěch','success');
$LQUser->addStatusMessage('Varování','warning');
$LQUser->addStatusMessage('Chyba','error');


//Hlavička stránek
$oPage->addItem(new LQPageTop('Template Page Title'));

//Skinovný jakoby button link
$oPage->addItem(new LQLinkButton('/admin/', 'Vstup do administrace'));


//Vložení konce stránky (aktuálně obsahuje i hlavní menu)
$oPage->addItem(new LQPageBottom());

//Vyrendrování stránky
$oPage->Draw();

?>
