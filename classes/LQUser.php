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
class LQUser extends EaseUser {
    /**
     * Budeme používat serializovaná nastavení uložená ve sloupečku
     * @var string 
     */
    public $SettingsColumn = 'settings';
    
    /**
     * Uživatel LinkQuick
     * @param string $UserID twitter name
     */
    function __construct($UserID = NULL,$TwitterID = NULL) {
        parent::__construct($UserID);
        if(is_string($UserID)){ //Twitter Login
            $this->SetObjectName();
            $this->LoginSuccess();
            $this->UserLogin = $UserID;
            $this->UserID = $TwitterID;
        }
    }
    
}

?>
