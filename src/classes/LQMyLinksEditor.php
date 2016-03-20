<?php
/**
 * zobrazení a editace přehledu vložených adres
 * @copyright Vitex Software © 2012
 * @author Vítězslav Dvořák <vitex@vitexsoftware.cz>
 * @package LinkQuick
 * @subpackage WebUI
 */
require_once 'LQLinkDateTimeSelector.php';

/**
 * Třída zobrazení přehledu vložených adres
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2011
 * @package LinkQuick
 * @subpackage Engine
 * @todo Editace
 */
class LQMyLinksEditor extends \Ease\HtmlDivTag
{
    /**
     * Pracujeme s tabulkou entry
     * @var string
     */
    public $MyTable = 'entry';

    /**
     * Položky k zobrazení
     * @var array
     */
    public $entry = [];

    /**
     * Kolik položek zobrazovat na stránku
     * @var int unsigned
     */
    public $entryPerPage = 20;

    /**
     *
     * @var type
     */
    public $Pages = 0;

    /**
     * Načte data z databáze
     */
    function LoadSqlData($Domain = NULL)
    {
        if (is_null($Domain)) {
            $DomFragment = '';
        } else {
            $DomFragment = ' `domain` LIKE \''.$Domain.'\' AND ';
        }
        $PageNo = intval($this->webPage->getRequestValue('PageNo'));
        $Pages  = $this->MyDbLink->QueryToArray('SELECT count(*) FROM '.$this->MyTable.' WHERE deleted=0 AND '.$DomFragment.' `owner`='.$this->EaseShared->User->getUserID());
        if (isset($Pages[0])) {
            $this->Pages = ceil(current($Pages[0]) / $this->entryPerPage);
        }
        $this->entry = $this->MyDbLink->QueryToArray('SELECT * FROM '.$this->MyTable.' WHERE deleted=0 AND '.$DomFragment.' `owner`='.$this->EaseShared->User->getUserID().' LIMIT '.$this->entryPerPage.' OFFSET '.$PageNo
            * $this->entryPerPage, 'id');
    }

    /**
     * načte položky z databáze a zobrazí je
     */
    function Finalize()
    {
        $Domains = Encoder::getDomainList();
        $DomTabs = $this->addItem(new EaseJQueryUITabs('DomTabs'));
        foreach ($Domains as $Domain) {
            $this->LoadSqlData($Domain);
            $TabTable = new \Ease\HtmlTableTag();
            $TabTable->addRowHeaderColumns([_('zobr.'), _('od'), _('zkratka'), _('adresa'),
                _('Datum expirace'), _('odstranění')]);
            foreach ($this->entry as $LinkID => $Link) {
                $TabTable->addRowColumns([
                    $Link['used'],
                    self::ShowTime($Link['created']),
                    new \Ease\HtmlATag($Link['code'], $Link['code']),
                    new \Ease\HtmlATag($Link['url'], $Link['title']),
                    new LQLinkDateTimeSelector('ExpireDate'.$Link['id'],
                        $Link['ExpireDate'], $Link['id'],
                        ['Field' => 'ExpireDate']),
                    new EaseJQueryLinkButton('?DeleteID='.$Link['id'],
                        _('odstranit'), NULL, ['class' => 'delete'])]
                );
            }
            $this->addNavigation($TabTable);
            $DomTabs->addTab($Domain, $TabTable);
        }
    }

    /**
     * Přidává navigaci do tabulky
     *
     * @param type $Table
     */
    function AddNavigation(& $Table)
    {
        if ($this->Pages > 1) {
            $Navigator = [];
            for ($i = 1; $i <= $this->Pages; $i++) {
                $Navigator[] = '<a href="?PageNo='.($i - 1).'">'.$i.'</a>';
            }
            $Table->addRowColumns([1 => implode(' ', $Navigator)],
                ['colspan' => 4]);
        }
    }

    /**
     * Zobrazuje datum v národním tvaru
     * @param string $Time sql-time
     * @return \Ease\HtmlSpanTag
     */
    static function ShowTime($Time)
    {
        if (is_null($Time)) {
            return '';
        }
        $Stamp = strtotime($Time);
        return new \Ease\HtmlSpanTag(NULL, strftime('%e.%m. %Y', $Stamp),
            ['title' => $Time]);
    }

    function SetUpUser(&$User, &$TargetObject = NULL)
    {
        $this->setDataValue('owner', $User->getUserID());
        return parent::SetUpUser($User, $TargetObject);
    }
}
?>
