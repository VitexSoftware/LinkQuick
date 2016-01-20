<?php
/**
 * LinkQuick - vložení nové adresy do databáze
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2011
 */
require_once 'includes/LQInit.php';
require_once 'classes/LQEncoder.php';

$Encoder = new LQEncoder();

$server = new SoapServer(null, array('uri' => EasePage::phpSelf()));
$server->setObject($Encoder);
$server->handle();


/*
function hello($someone) { 
   return "Hello " . $someone . "!";
} 
   $server = new SoapServer(null, array('uri' => "urn://www.herong.home/res"));
   $server->addFunction("hello"); 
   $server->handle(); 
  */ 
   
   
   
?>
