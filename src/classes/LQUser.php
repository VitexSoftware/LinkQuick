<?php

/**
 * Uživatele zkracovače
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2011
 * @package LinkQuick
 * @subpackage Engine
 */
require_once 'Ease/EaseUser.php';

/**
 * Uživatel LinkQuicku
 */
class LQUser extends EaseUser
{

    /**
     * Budeme používat serializovaná nastavení uložená ve sloupečku
     * @var string 
     */
    public $SettingsColumn = 'settings';

    /**
     * Vrací odkaz na ikonu
     * 
     * @return string 
     */
    function getIcon()
    {
        $Icon = $this->GetSettingValue('icon');
        if (is_null($Icon)) {
            return parent::GetIcon();
        } else {
            return $Icon;
        }
    }

}

class LQTwitterUser extends LQUser
{

    /**
     * Uživatel autentifikující se vůči twitteru
     * 
     * @param int    $TwitterID   id uživatele
     * @param string $TwitterName jméno uživatele
     */
    function __construct($TwitterID = null, $TwitterName = null)
    {
        parent::__construct();
        if (!is_null($TwitterID)) {
            $this->SetDataValue('twitter_id', $TwitterID);
            $this->SetDataValue($this->LoginColumn, $TwitterName);
            $this->SetMyKeyColumn('twitter_id');
            if (!$this->LoadFromMySQL()) {
                $this->RestoreObjectIdentity();
                if ($this->InsertToMySQL()) {
                    $this->addStatusMessage(_(sprintf('Vytvořeno spojení s Twitterem', $TwitterName), 'success'));
                    $this->LoginSuccess();
                }
            } else {
                $this->RestoreObjectIdentity();
            }
            $this->setObjectName();
        }
    }

}

require_once 'classes/facebook.php';

class LQFacebookUser extends LQUser
{

    /**
     * Třída facebooku
     * @var Facebook
     */
    public $facebook = NULL;

    /**
     * Uživatel autentifikující se vůči facebooku
     * 
     * @param Facebook $Facebook objekt facebooku
     */
    function __construct($Facebook = NULL)
    {
        parent::__construct();

        if (is_null($Facebook)) {
            $this->facebook = new Facebook(array(
                        'appId' => FB_APP_ID,
                        'secret' => FB_SECRET,
                    ));
        } else {
            $this->facebook = $Facebook;
        }

        $user_profile = $this->facebook->api('/me');
        $this->UserLogin = $user_profile['name'];
        $this->setDataValue('facebook_id', $user_profile['id']);
        $this->setDataValue($this->LoginColumn, $user_profile['name']);

        $this->setMyKeyColumn('facebook_id');
        if (!$this->loadFromMySQL()) {
            $this->restoreObjectIdentity();
            $this->setSettingValue('icon', 'https://graph.facebook.com/' . $user_profile['id'] . '/picture');
            $this->setDataValue('email', $user_profile['email']);
            $this->setDataValue('firstname', $user_profile['first_name']);
            $this->setDataValue('lastname', $user_profile['last_name']);

            if ($this->saveToMySQL()) {
                $this->addStatusMessage(_(sprintf('Vytvořeno spojení %s s Facebookem', $this->UserLogin)), 'success');
            }
        } else {
            $this->RestoreObjectIdentity();
        }
        $this->setObjectName();
    }

}

?>
