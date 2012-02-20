<?php

/**
 * Uloží data 
 * @package LinkQuick
 */
require_once 'includes/LQInit.php';

$OPage->OnlyForLogged();

if (!$OUser->GetUserID()) {
    die(_('nejprve se prosím přihlaš'));
}

$SaverClass = $OPage->GetRequestValue('SaverClass');
if (!$SaverClass) {
    $SaverClass = 'LBSaver';
}


if (file_exists('classes/' . $SaverClass . '.php')) {
    require_once 'classes/' . $SaverClass . '.php';
} else {
    $OUser->AddStatusMessage(_('Načítání souboru: classes/' . $SaverClass . '.php'), 'warning');
}

$Field = $OPage->GetRequestValue('Field');
$Value = $OPage->GetRequestValue('Value');

if (is_null($SaverClass) || is_null($Field) || is_null($Value)) {
    die(_('Chybné volání'));
}

$Saver = new $SaverClass($Field);
$Saver->SetUpUser($OUser);
$Saver->SetDataValue($Field, $Value);
$keyColumn = $Saver->GetMyKeyColumn();
if ($keyColumn) {
    $Key = $OPage->GetRequestValue($keyColumn);
    if ($Key) {
        $Saver->SetMyKey($Key);
    }
}

if (is_null($Saver->SaveToMySql())) {
    header('HTTP/1.0 501 Not Implemented', 501);
    $OUser->AddStatusMessage(_('Chyba ukládání do databáze: ') . ' ' . $Saver->MyDbLink->ErrorText . ': ' .
            _('Třída') . ': <strong>' . $SaverClass . '</strong> ' .
            _('Tabulka') . ': <strong>' . $Saver->MyTable . '</strong> ' .
            _('Pole') . ': <strong>' . $Field . '</strong> ' .
            _('Hodnota') . ': <strong>' . $Value . '</strong> <tt>' . $Saver->MyDbLink->LastQuery . '</tt>', 'error');
} else {
    header('HTTP/1.0 200 Data saved', 200);
}
?>
