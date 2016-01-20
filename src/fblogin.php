<?php
/**
 * Autentifikace přes facebook
 * 
 * @package    U.L.N.S
 * @subpackage Engine
 * @author     Vitex <vitex@hippy.cz>
 * @copyright  2012 Vitex@hippy.cz (G)
 */
require_once 'includes/ULNSInit.php';
require_once 'classes/ULNSFacebook.php';


/**
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
require_once 'classes/facebook.php';

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
            'appId' => FB_APP_ID,
            'secret' => FB_SECRET,
        ));

// Get User ID
$user = $facebook->getUser();

// We may or may not have this data based on whether the user is logged in.
//
// If we have a $user id here, it means we know the user is logged into
// Facebook, but we don't know if the access token is valid. An access
// token is invalid if the user logged out of Facebook.

if ($user) {
    try {
        // Proceed knowing you have a logged in user who's authenticated.

    } catch (FacebookApiException $e) {
        error_log($e);
        $user = null;
    }
}

// Login or logout url will be needed depending on current user state.
if ($user) {
    $logoutUrl = $facebook->getLogoutUrl();
} else {
    $loginUrl = $facebook->getLoginUrl();
}

if ($user) {

    EaseShared::user(new ULNSFacebookUser($facebook));
    EaseShared::user()->LoginSuccess();
    EaseShared::webPage()->Redirect('index.php');
    exit;
}
?>

<?php if ($user): ?>
    <a href="<?php echo $logoutUrl; ?>">Logout</a>
<?php
else:

    EaseShared::webPage()->Redirect($loginUrl);
    exit;
endif;


EaseShared::webPage()->Redirect('login.php');
?>
    
