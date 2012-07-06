<?php
/**
 * Ukázka vzdáleného vytvoření zkratky
 * 
 * @author     Vitex <vitex@hippy.cz>
 * @copyright  Vitex@hippy.cz (G) 2012
 * @package    LinkQuick
 * @subpackage Examples
 * @link       LQEncoder
 */


$client = new SoapClient(null, array(
            'location' => "http://l.q.cz/api.php",
            'uri' => "urn://l.q.cz/req",
            'trace' => 1));

$dlouheURL = 'http://jqueryui.com/demos/dialog/#modal-message';
$domena = 'l.q.cz'; //nebo také q.cz 

/**
 * Vytvoření zkratky 
 */
$kratkeURL = $client->__soapCall("saveUrl", array($dlouheURL, $domena));

/**
 * Zpětné dekodování - vrací původní url pro zkratku 
 */
$puvodniURL = $client->__soapCall("getURLByCode", array($kratkeURL, $domena));



?>

