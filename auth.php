<?php
/**
 * Přihlašovací stránka
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2011
 * @package LinkQuick
 * @subpackage Engine
 */
require_once 'includes/LQInit.php';
require_once 'classes/LQTwitter.php';


require 'classes/tmhOAuth.php';
require 'classes/tmhUtilities.php';
$tmhOAuth = new tmhOAuth(array(
            'consumer_key' => 'sTvSZYSWUo60ZkuCGRvWYg',
            'consumer_secret' => 'qRPR8cOWmYA7r2AtMh7mQPu2PyHSGkpjCPqqTeZ7Taw',
        ));

$here = tmhUtilities::php_self();

function outputError($tmhOAuth) {
    echo 'Error: ' . $tmhOAuth->response['response'] . PHP_EOL;
    tmhUtilities::pr($tmhOAuth);
}

// reset request?
if (isset($_REQUEST['wipe'])) {
    unset($_SESSION['access_token']);
    $OPage->Redirect('LogOut.php');

// already got some credentials stored?
} elseif (isset($_SESSION['access_token'])) {
    $tmhOAuth->config['user_token'] = $_SESSION['access_token']['oauth_token'];
    $tmhOAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];

    $code = $tmhOAuth->request('GET', $tmhOAuth->url('1/account/verify_credentials'));
    if ($code == 200) {
        $resp = json_decode($tmhOAuth->response['response']);
        $_SESSION['User'] = new LQUser($resp->screen_name,$resp->id);
        $OPage->Redirect('index.php');
    } else {
        outputError($tmhOAuth);
    }
// we're being called back by Twitter
} elseif (isset($_REQUEST['oauth_verifier'])) {
    $tmhOAuth->config['user_token'] = $_SESSION['oauth']['oauth_token'];
    $tmhOAuth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];

    $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/access_token', ''), array(
        'oauth_verifier' => $_REQUEST['oauth_verifier']
            ));

    if ($code == 200) {
        $_SESSION['access_token'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
        unset($_SESSION['oauth']);
        header("Location: {$here}");
        exit;
    } else {
        outputError($tmhOAuth);
    }
// start the OAuth dance
} elseif (isset($_REQUEST['authenticate']) || isset($_REQUEST['authorize'])) {
    $callback = isset($_REQUEST['oob']) ? 'oob' : $here;

    $params = array(
        'oauth_callback' => $callback
    );

    if (isset($_REQUEST['force_write'])) :
        $params['x_auth_access_type'] = 'write';
    elseif (isset($_REQUEST['force_read'])) :
        $params['x_auth_access_type'] = 'read';
    endif;

    $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/request_token', ''), $params);

    if ($code == 200) {
        $_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
        $method = isset($_REQUEST['authenticate']) ? 'authenticate' : 'authorize';
        $force = isset($_REQUEST['force']) ? '&force_login=1' : '';
        $authurl = $tmhOAuth->url("oauth/{$method}", '') . "?oauth_token={$_SESSION['oauth']['oauth_token']}{$force}";
        $OPage->Redirect($authurl);
        exit;
    } else {
        outputError($tmhOAuth);
    }
}
        $OPage->Redirect('index.php');


/*
<ul>
    <li><a href="?authenticate=1">Sign in with Twitter</a></li>
    <li><a href="?authenticate=1&amp;force=1">Sign in with Twitter (force login)</a></li>
    <li><a href="?authorize=1">Authorize Application (with callback)</a></li>
    <li><a href="?authorize=1&amp;oob=1">Authorize Application (oob - pincode flow)</a></li>
    <li><a href="?authorize=1&amp;force_read=1">Authorize Application (with callback) (force read-only permissions)</a></li>
    <li><a href="?authorize=1&amp;force_write=1">Authorize Application (with callback) (force read-write permissions)</a></li>
    <li><a href="?authorize=1&amp;force=1">Authorize Application (with callback) (force login)</a></li>
    <li><a href="?wipe=1">Start Over and delete stored tokens</a></li>
</ul>

*/

?>
