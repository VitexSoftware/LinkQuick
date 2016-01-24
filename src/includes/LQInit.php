<?php
/**
 * Appliacation Init
 * @author vitex
 * @copyright Vitex@hippy.cz (G) 2010
 */

namespace LQ;
use Ease;

require_once 'includes/Configure.php';
require_once '../vendor/autoload.php';


//Initialise Gettext
$langs = array(
    'en_US' => array('en', 'English (International)'),
    'cs_CZ' => array('cs', 'Česky (Čeština)'),
);
$locale = "en_US";
if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
    $locale = locale_accept_from_http($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
}
if (isSet($_GET["locale"])) {
    $locale = preg_replace("/[^a-zA-Z_]/", "", substr($_GET["locale"], 0, 10));
}
foreach ($langs as $code => $lang) {
    if ($locale == $lang[0]) {
        $locale = $code;
    }
}
setlocale(LC_ALL, $locale);
bind_textdomain_codeset("linkquick", "UTF-8");
putenv("LC_ALL=$locale");
if (file_exists('../i18n')) {
    bindtextdomain('linkquick', '../i18n');
}
textdomain('linkquick');


/**
 * User Object
 * @global \Ease\Anonym | LQ\User
 */
$oUser = Ease\Shared::user();

/**
 * WebPage Object
 * @global LQPage
 */
$oPage = new WebPage();
