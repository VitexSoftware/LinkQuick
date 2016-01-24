<?php

namespace LQ;

/**
 * Uživatele zkracovače
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2011
 * @package LinkQuick
 * @subpackage Engine
 */

/**
 * Uživatel LinkQuicku
 */
class User extends \Ease\User {

    /**
     * we use this table
     * @var string 
     */
    public $myTable = 'user';
    
    /**
     * Column for settings
     * @var string 
     */
    public $settingsColumn = 'settings';

    /**
     * Column for Create time
     * @var string 
     */
    public $myCreateColumn = 'DatCreate';

    /**
     * Column for last change time
     * @var string 
     */
    public $myLastModifiedColumn = 'DatSave';

    /**
     * Give you icon image link
     * 
     * @return string 
     */
    function getIcon() {
        $icon = $this->getSettingValue('icon');
        if (is_null($icon)) {
            return parent::GetIcon();
        } else {
            return $icon;
        }
    }

}
