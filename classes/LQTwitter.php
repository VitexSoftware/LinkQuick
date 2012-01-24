<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class EaseOAuth extends EaseAtom {
    static public $RequestTokenURL = '';
    static public $AuthorizeURL = '';
    static public $AccessTokenURL = '';
    
    static public $OAuthVersion = '1.0';
    static public $OAuthSignatureMethod = 'HMAC-SHA1';

    static public $ConsumerKey = '';
    static public $ConsumerSecret = '';
    
    static public $OAuthCallback = '';


    public $Curl = NULL;

    function __construct() {
        $this->InitCurl();
    }

    function InitCurl(){
        $this->Curl = curl_init();
        curl_setopt($this->Curl,CURLOPT_POST , TRUE);
//        curl_setopt($this->Curl,CURLOPT_POST , TRUE);
//        curl_setopt($this->Curl,CURLOPT_POST , TRUE);
    }
    
    function AuthRequest(){
        curl_setopt($this->Curl, CURLOPT_URL, self::$RequestTokenURL);        
        $AuthHeader = 'OAuth oauth_nonce="K7ny27JTpKVsTgdyLdDfmQQWVLERj2zAK5BslRsqyw", oauth_callback="'.self::$OAuthCallback.'", oauth_signature_method="'.self::$OAuthSignatureMethod.'", oauth_timestamp="'.time().'", oauth_consumer_key="'.self::$ConsumerKey.'", oauth_signature="'.$this->OAuthSignature().'", oauth_version="'.self::$OauthVersion.'"';
    }

    function OAuthSignature(){
        
    }

    /**
     * Je již uživatel přihlášen k twitteru ?
     * @return boolean 
     */
    static function IsAuthenticated(){
        return isset ($_SESSION['access_token']['user_id']);
    }
    
    /**
     *
     * @param type $Base
     * @return EaseJQueryLinkButton 
     */
    static function AuthButton($Base = ''){
        if(!self::IsAuthenticated()){
            return new EaseJQueryLinkButton($Base.'?authenticate=1', _('přihlásit přez Twitter'));
        } else {
            return new EaseJQueryLinkButton($Base.'?wipe=1', _('odhlasit twitter'));
        }
    }

    function __destruct() {
        curl_close($this->Curl);
    }
    
}

/**
 * Description of LQTwitter
 *
 * @author vitex
 */
class LQTwitter extends EaseOAuth {
    static public $RequestTokenURL = 'https://api.twitter.com/oauth/request_token';
    static public $AuthorizeURL = 'https://api.twitter.com/oauth/authorize';
    static public $AccessTokenURL = 'https://api.twitter.com/oauth/access_token';
    
    static public $AccessToken = '226774444-aDWQrAa0OW2MPXhRsmoQc5GP6L2Q4l08UL5u6FZo';
    static public $AccessTokenSecret = 'Nll1vXUVcu0YTlcH9gEuTbXMBPMtnSMmRYQP7JYcc';

    static public $ConsumerKey = 'sTvSZYSWUo60ZkuCGRvWYg';
    static public $ConsumerSecret = 'qRPR8cOWmYA7r2AtMh7mQPu2PyHSGkpjCPqqTeZ7Taw';
}

?>
