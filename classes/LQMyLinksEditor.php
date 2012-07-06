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
class LQMyLinksEditor extends EaseHtmlDivTag
{

    /**
     * Pracujeme s tabulkou entries
     * @var string 
     */
    public $MyTable = 'entries';

    /**
     * Položky k zobrazení
     * @var array 
     */
    public $Entries = array();

    /**
     * Kolik položek zobrazovat na stránku
     * @var int unsigned 
     */
    public $EntriesPerPage = 20;

    /**
     *
     * @var type 
     */
    public $Pages = 0;

    /**
     * Načte data z databáze 
     */
    function LoadSqlData( $Domain = NULL )
    {
        if(is_null($Domain)){
            $DomFragment = '';
        } else {
            $DomFragment = ' `domain` LIKE \''.$Domain.'\' AND ';
        }
        $PageNo = intval($this->WebPage->GetRequestValue('PageNo'));
        $Pages = $this->MyDbLink->QueryToArray('SELECT count(*) FROM ' . $this->MyTable . ' WHERE deleted=0 AND '.$DomFragment.' `owner`=' . $this->EaseShared->User->GetUserID());
        if (isset($Pages[0])) {
            $this->Pages = ceil(current($Pages[0]) / $this->EntriesPerPage);
        }
        $this->Entries = $this->MyDbLink->QueryToArray('SELECT * FROM ' . $this->MyTable . ' WHERE deleted=0 AND '.$DomFragment.' `owner`=' . $this->EaseShared->User->GetUserID() . ' LIMIT ' . $this->EntriesPerPage . ' OFFSET ' . $PageNo * $this->EntriesPerPage, 'id');
    }

    /**
     * načte položky z databáze a zobrazí je
     */
    function Finalize()
    {
        $Domains = LQEncoder::getDomainList();
        $DomTabs = $this->addItem(new EaseJQueryUITabs('DomTabs'));
        foreach ($Domains as $Domain) {
            $this->LoadSqlData($Domain);
            $TabTable = new EaseHtmlTableTag();
            $TabTable->AddRowHeaderColumns(array(_('zobr.'), _('od'), _('zkratka'), _('adresa'), _('Datum expirace'), _('odstranění')));
            foreach ($this->Entries as $LinkID => $Link) {
                $TabTable->AddRowColumns(array(
                    $Link['used'],
                    self::ShowTime($Link['created']),
                    new EaseHtmlATag( $Link['code'], $Link['code']),
                    new EaseHtmlATag($Link['url'], $Link['title']),
                    new LQLinkDateTimeSelector('ExpireDate' . $Link['id'], $Link['ExpireDate'], $Link['id'], array('Field' => 'ExpireDate')),
                    new EaseJQueryLinkButton('?DeleteID=' . $Link['id'], _('odstranit'), NULL, array('class' => 'delete')))
                );
            }
            $this->AddNavigation($TabTable);
            $DomTabs->addTab($Domain, $TabTable);
        }

    }

    /**
     * Přidává navigaci do tabulky
     * 
     * @param type $Table 
     */
    function AddNavigation( & $Table )
    {
        if ($this->Pages > 1) {
            $Navigator = array();
            for ($i = 1; $i <= $this->Pages; $i++) {
                $Navigator[] = '<a href="?PageNo=' . ($i - 1) . '">' . $i . '</a>';
            }
            $Table->AddRowColumns(array(1 => implode(' ', $Navigator)), array('colspan' => 4));
        }
    }

    /**
     * Zobrazuje datum v národním tvaru
     * @param string $Time sql-time
     * @return EaseHtmlSpanTag 
     */
    static function ShowTime($Time)
    {
        if (is_null($Time)) {
            return '';
        }
        $Stamp = strtotime($Time);
        return new EaseHtmlSpanTag(NULL, strftime('%e.%m. %Y', $Stamp), array('title' => $Time));
    }

    function SetUpUser(&$User, &$TargetObject = NULL)
    {
        $this->SetDataValue('owner', $User->GetUserID());
        return parent::SetUpUser($User, $TargetObject);
    }

}

?>
