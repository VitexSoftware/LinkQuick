<?php
/**
 * Init aplikace
 * @author vitex
 * @copyright Vitex@hippy.cz (G) 2010
 */
require_once 'includes/Configure.php';
set_include_path('classes' . PATH_SEPARATOR . get_include_path());

$language = "cs_CZ";
$codeset = "cs_CZ.UTF-8";
$domain = "messages";
putenv("LANGUAGE=" . $language);
putenv("LANG=" . $language);
bind_textdomain_codeset($domain, "UTF8");
setlocale(LC_ALL, $codeset);
bindtextdomain($domain, realpath("./locale"));
textdomain($domain);

require_once 'LQUser.php';

session_start();

if (!isset($_SESSION['User']) || !is_object($_SESSION['User'])) {
    $_SESSION['User'] = new EaseAnonym();
}




/**
 * Objekt uživatele LQCustomer nebo LQAnonym
 * @global LQUser
 */
$OUser = &$_SESSION['User'];

require_once 'LQWebPage.php';

/**
 * Objekt pro práci se stránkou
 * @global LQPage
 */
$OPage = new LQWebPage($OUser);
?>