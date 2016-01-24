<?php

/**
 * Uloží data 
 * @package LinkQuick
 */
require_once 'includes/LQInit.php';

$oPage->OnlyForLogged();

if (!$oUser->getUserID()) {
    die(_('nejprve se prosím přihlaš'));
}

$SaverClass = $oPage->getRequestValue('SaverClass');
if (!$SaverClass) {
    $SaverClass = 'LBSaver';
}


if (file_exists('classes/' . $SaverClass . '.php')) {
    require_once 'classes/' . $SaverClass . '.php';
} else {
    $oUser->addStatusMessage(_('Načítání souboru: classes/' . $SaverClass . '.php'), 'warning');
}

$Field = $oPage->getRequestValue('Field');
$Value = $oPage->getRequestValue('Value');

if (is_null($SaverClass) || is_null($Field) || is_null($Value)) {
    die(_('Chybné volání'));
}

$Saver = new $SaverClass($Field);
$Saver->setUpUser($oUser);
$Saver->setDataValue($Field, $Value);
$keyColumn = $Saver->getMyKeyColumn();
if ($keyColumn) {
    $Key = $oPage->getRequestValue($keyColumn);
    if ($Key) {
        $Saver->setMyKey($Key);
    }
}

if (is_null($Saver->saveToMySql())) {
    header('HTTP/1.0 501 Not Implemented', 501);
    $oUser->addStatusMessage(_('Chyba ukládání do databáze: ') . ' ' . $Saver->MyDbLink->ErrorText . ': ' .
            _('Třída') . ': <strong>' . $SaverClass . '</strong> ' .
            _('Tabulka') . ': <strong>' . $Saver->myTable . '</strong> ' .
            _('Pole') . ': <strong>' . $Field . '</strong> ' .
            _('Hodnota') . ': <strong>' . $Value . '</strong> <tt>' . $Saver->MyDbLink->LastQuery . '</tt>', 'error');
} else {
    header('HTTP/1.0 200 Data saved', 200);
}
?>
