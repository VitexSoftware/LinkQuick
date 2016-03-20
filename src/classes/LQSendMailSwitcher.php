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
class LQSendMailSwitcher extends \Ease\HtmlCheckBoxTag
{
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
    function __construct($Name, $Caption, $Checked = false, $Value = NULL,
                         $Properties = NULL)
    {
        $this->Caption = $Caption;
        parent::__construct($Name, $Checked, $Value, $Properties);
        $this->setTagID($Name);
    }

    /**
     * Doplní klikatelným popiskem
     */
    function AfterAdd()
    {
        $this->IncludeJavaScript('jquery.js', 0, TRUE);
//        $this->ParentObject->addItem(new \Ease\HtmlSpanTag(NULL, $this->Caption, array('OnClick' => '$(\'#' . $this->getTagName() . '\').click()', 'class' => 'caption'))); //TODO: Checkbox posila opacnou hodnotu nez label
        $this->addLabel($this->Caption);
    }

    /**
     * Doplní popisek prvku
     * @param string $Label
     */
    function AddLabel($Label = NULL)
    {
        $ForID = $this->getTagID();
        if (is_null($Label)) {
            $Label = $ForID;
        }
        $this->ParentObject->addItem('<label for="'.$ForID.'">'.$Label.'</label>');
    }

    /**
     * Přidává script pro uložení hodnoty po kliknutí do DB
     */
    function Finalize()
    {
        $this->setTagProperties(['OnClick' => '$.post(\'DataSaver.php\', { SaverClass: \''.get_class($this).'\', Field: \''.$this->getTagName().'\', Value: $(this).is(\':checked\') } );']);
        parent::Finalize();
    }

    /**
     * Převede textové hodnoty checkboxu na bool a uloží
     * @param array $Data
     * @param boolean $SearchForID
     */
    function SaveToMySQL($Data = NULL, $SearchForID = false)
    {
        if (is_null($Data)) {
            $Data = $this->getData();
        }
        if ($Data[$this->getTagName()] == 'true') {
            $Settings['SendMail'] = 1;
        } else {
            $Settings['SendMail'] = 0;
        }
        $this->User->setSettings($Settings);
        return $this->User->saveToMySQL();
    }
}
?>
