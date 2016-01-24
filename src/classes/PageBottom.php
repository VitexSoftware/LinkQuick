<?php
namespace LQ;

/**
 * Custom page bottom
 * 
 * @author Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2009,2010,2011,2012,2016
 */

class PageBottom extends \Ease\TWB\Container
{

    /**
     * Zobrazí přehled právě přihlášených a spodek stránky
     */
    public function finalize()
    {
        $this->SetTagID('footer');
        $this->addItem('<hr>');
        $footrow = new \Ease\TWB\Row();
        $footrow->addColumn(4, '<iframe src="https://ghbtns.com/github-btn.html?user=VitexSoftware&repo=LinkQuick&type=star&count=true" frameborder="0" scrolling="0" width="170px" height="20px"></iframe>');
        $footrow->addColumn(4, 'Powered by <a href="https://www.vitexsoftware.cz/ease.php">Ease Framework</a>&nbsp;&nbsp;<br/> &copy; 2009-2016 <a href="http://vitexsoftware.cz/">Vitex Software</a>');
        $footrow->addColumn(4, '<a href="http://www.spoje.net"><img style="position: relative; top: -7px; left: -10px;" align="right" style="border:0" src="images/spojenet_small_white.gif" alt="SPOJE.NET" title="Housing zajišťují SPOJE.NET s.r.o." /></a>');
        $this->addItem($footrow);
    }

}

