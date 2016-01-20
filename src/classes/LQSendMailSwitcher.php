<?php

/**
 * Přepínač odesílání mailu s potvrzením 
 * @copyright Vitex Software © 2011
 * @author Vitex <vitex@hippy.cz>
 * @package LinkQuick
 * @subpackage WEBUI
 */
require_once 'Ease/EaseJQueryWidgets.php';

/**
 * Description of FragCCUserAdminSwitcher
 *
 * @author vitex
 */
class LQSendMailSwitcher extends EaseHtmlCheckBoxTag {

    /**
     * Popisek zobrazený za prvkem
     * @var string 
     */
    public $Caption = NULL;

    /**
     * Zobrazuje HTML Checkbox ukládající svoji hodntu do DB
     * @param string $Name jméno prvku
     * @param string $Caption popisek za prvkem
     * @param boolean $Checked stav
     * @param string $Value vracená hodnota
     * @param string $Properties 
     */
    function __construct($Name, $Caption, $Checked = false, $Value = NULL, $Properties = NULL) {
        $this->Caption = $Caption;
        parent::__construct($Name, $Checked, $Value, $Properties);
        $this->SetTagID($Name);
    }

    /**
     * Doplní klikatelným popiskem
     */
    function AfterAdd() {
        $this->IncludeJavaScript('jquery.js', 0, TRUE);
//        $this->ParentObject->AddItem(new EaseHtmlSpanTag(NULL, $this->Caption, array('OnClick' => '$(\'#' . $this->GetTagName() . '\').click()', 'class' => 'caption'))); //TODO: Checkbox posila opacnou hodnotu nez label
        $this->AddLabel($this->Caption);
    }

    /**
     * Doplní popisek prvku
     * @param string $Label 
     */
    function AddLabel($Label = NULL) {
        $ForID = $this->GetTagID();
        if (is_null($Label)) {
            $Label = $ForID;
        }
        $this->ParentObject->AddItem('<label for="' . $ForID . '">' . $Label . '</label>');
    }

    /**
     * Přidává script pro uložení hodnoty po kliknutí do DB
     */
    function Finalize() {
        $this->SetTagProperties(array('OnClick' => '$.post(\'DataSaver.php\', { SaverClass: \'' . get_class($this) . '\', Field: \'' . $this->GetTagName() . '\', Value: $(this).is(\':checked\') } );'));
        parent::Finalize();
    }

    /**
     * Převede textové hodnoty checkboxu na bool a uloží
     * @param array $Data
     * @param boolean $SearchForID 
     */
    function SaveToMySQL($Data = NULL, $SearchForID = false) {
        if (is_null($Data)) {
            $Data = $this->GetData();
        }
        if ($Data[$this->GetTagName()] == 'true') {
            $Settings['SendMail'] = 1;
        } else {
            $Settings['SendMail'] = 0;
        }
        $this->User->SetSettings($Settings);
        return $this->User->SaveToMySQL();
    }

}

?>
