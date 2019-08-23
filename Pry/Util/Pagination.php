<?php

/**
 * Pry Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * 
 */

namespace Pry\Util;

use UnexpectedValueException;

/**
 * Generate a pagination from results set
 * Only return an array.
 * <code>
 * $pager = new Pagination($total);
 * $pagin = $pager->create();
 * $data = $sql->query('SELECT id FROM planning LIMIT '.$pager->itemMini.','.$pager->nbItemParPage);
 * foreach($pagin as $tab)
 * {
 *    if($tab['encours'])
 *      echo $tab['page'];
 *    else
 *      echo' <a href="?p='.$tab['page'].'">'.$tab['page'].'</a> ';
 * }
 * </code>
 * @author Olivier ROGER <oroger.fr>
 *       
 */
class Pagination
{
    public const BASIC    = 1;
    public const ADVANCED = 2;

    /**
     * Total items
     * @var int
     */
    private $nbItemTotal;

    /**
     * Pagination type. BASIC/ADVANCED
     * @var int
     */
    private $pagerType;

    /**
     * Current page
     * @var int
     */
    private $currentPage;

    /**
     * Output array
     * @var array
     */
    private $output;

    /**
     * Total number of pages
     * @var int
     */
    public $nbPageTotal;

    /**
     * Number of element per page
     * @var int
     */
    public $nbItemParPage;

    /**
     * Minimal item to use
     * @var int
     */
    public $itemMini;

    /**
     * Number of page to show with the current page
     * Used only in ADVANCED mode
     *
     * @var int
     */
    public $pageAdjacente;

    /**
     * Array with previous/next link
     * Used only in ADVANCED mode
     * @var array
     */
    public $nextPrev;

    /**
     * Init pagination
     *
     * @param int $total Total number of result
     * @param int $type Pagination type
     * @param int $nbParPage Item per page
     * @param int $page Current page
     */
    public function __construct(int $total, int $type = 1, int $nbParPage = 10, int $page = 1)
    {
        $this->nbItemTotal   = intval($total);
        $this->nbItemParPage = $nbParPage;

        $this->currentPage = intval($page);
        $this->nbPageTotal = ceil($this->nbItemTotal / $this->nbItemParPage);
        $this->pagerType   = intval($type);
        $this->output      = [];
        $this->nextPrev = [];
        $this->pageAdjacente = 2;
    }

    /**
     * Create pagination
     * @throws UnexpectedValueException
     * @return mixed
     */
    public function create()
    {

        if (isset($this->currentPage) && $this->currentPage > 1)
            $pageEnCours = $this->currentPage;
        else
            $pageEnCours = 1;

        $this->itemMini = ($pageEnCours - 1) * $this->nbItemParPage;

        if ($this->nbPageTotal > 1)
        {
            if ($this->pagerType == self::BASIC)
            {
                $this->buildSimple($pageEnCours);
            }
            elseif ($this->pagerType == self::ADVANCED)
            {
                $prev = $pageEnCours - 1;
                $next = $pageEnCours + 1;

                //Bouton Précédent
                if ($pageEnCours > 1)
                    $this->nextPrev['prev'] = $prev;
                else
                    $this->nextPrev['prev'] = '#';


                //Si le nombre de page est insuffisant pour la représentation avancée
                if ($this->nbPageTotal <= 3 * ($this->pageAdjacente * 2))
                    $this->buildSimple($pageEnCours);
                else
                    $this->buildAdvanced($pageEnCours);
                //Bouton Suivant

                if ($pageEnCours < $this->nbPageTotal)
                    $this->nextPrev['next'] = $next;
                else
                    $this->nextPrev['next'] = '#';
            }
            else
                throw new UnexpectedValueException('Le type de pagination doit être BASIC ou ADVANCED');

            return $this->output;
        }
        else
        {
            //1 seule page
            return false;
        }
    }

    /**
     * Build simple pagination
     * @param int $pageEnCours Current page
     */
    private function buildSimple(int $pageEnCours) : void
    {
        for ($i = 1; $i <= $this->nbPageTotal; $i++) {
            if ($i == $pageEnCours)
                $this->output[$i]['encours'] = true;
            else
                $this->output[$i]['encours'] = false;

            $this->output[$i]['page'] = $i;
        }
    }

    /**
     * Build advanced pagination
     * @param int $pageEnCours Current page
     */
    private function buildAdvanced(int $pageEnCours) : void
    {
        //Cas 1 : Début de pagination
        if ($pageEnCours < 2 + (2 * $this->pageAdjacente))
        {
            //Troncature de la fin de pagination
            for ($i = 1; $i < 4 + (2 * $this->pageAdjacente); $i++) {
                if ($i == $pageEnCours)
                    $this->output[$i]['encours'] = true;
                else
                    $this->output[$i]['encours'] = false;

                $this->output[$i]['page']                        = $i;
            }
            $this->output[$i + 1]['encours']                 = true;
            $this->output[$i + 1]['page']                    = '...';
            //Fin de la pagination
            $this->output[$this->nbPageTotal - 1]['encours'] = false;
            $this->output[$this->nbPageTotal - 1]['page']    = $this->nbPageTotal - 1;

            $this->output[$this->nbPageTotal]['encours'] = false;
            $this->output[$this->nbPageTotal]['page']    = $this->nbPageTotal;
        }
        //Cas 2 : Millieu de pagination (2 au début , 2 à la fin et un groupe au millieu)
        elseif (($this->pageAdjacente * 2) + 1 < $pageEnCours && $pageEnCours < $this->nbPageTotal - ($this->pageAdjacente * 2))
        {
            //Affichage des 2 premiers
            $this->output[1]['encours'] = false;
            $this->output[1]['page']    = 1;
            $this->output[2]['encours'] = false;
            $this->output[2]['page']    = 2;
            //Affichage de la séparation
            $this->output[3]['encours'] = true;
            $this->output[3]['page']    = '...';

            for ($i = $pageEnCours - $this->pageAdjacente; $i <= $pageEnCours + $this->pageAdjacente; $i++) {
                if ($i == $pageEnCours)
                    $this->output[$i]['encours'] = true;
                else
                    $this->output[$i]['encours'] = false;

                $this->output[$i]['page'] = $i;
            }

            $this->output[$i + 1]['encours']                 = true;
            $this->output[$i + 1]['page']                    = '...';
            //Fin de la pagination
            $this->output[$this->nbPageTotal - 1]['encours'] = false;
            $this->output[$this->nbPageTotal - 1]['page']    = $this->nbPageTotal - 1;

            $this->output[$this->nbPageTotal]['encours'] = false;
            $this->output[$this->nbPageTotal]['page']    = $this->nbPageTotal;
        }
        //Cas 3 : Fin de pagination. Affichage des 2 premiers et des 2 derniers
        else
        {
            //Affichage des 2 premiers
            $this->output[1]['encours'] = false;
            $this->output[1]['page']    = 1;
            $this->output[2]['encours'] = false;
            $this->output[2]['page']    = 2;
            //Affichage de la séparation
            $this->output[3]['encours'] = true;
            $this->output[3]['page']    = '...';

            for ($i = $this->nbPageTotal - (2 + (2 * $this->pageAdjacente)); $i <= $this->nbPageTotal; $i++) {
                if ($i == $pageEnCours)
                    $this->output[$i]['encours'] = true;
                else
                    $this->output[$i]['encours'] = false;

                $this->output[$i]['page'] = $i;
            }
        }
    }
}