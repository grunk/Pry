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

/**
 * Classe permettant de générer une pagination pour un jeu de résultat.
 * Retourne uniquement un array , pas de formatage HTML
 * <code>
 * $pager = new Util_Pagination($total);
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
 * @category Pry
 * @package Util
 * @version 1.1.0
 * @author Olivier ROGER <oroger.fr>
 *       
 */
class Pagination
{

    const BASIC    = 1;
    const ADVANCED = 2;

    /**
     * Nombre d'élément total
     * @access private
     * @var int
     */
    private $nbItemTotal;

    /**
     * Type de pagination. BASIC/ADVANCED
     * @access private
     * @var int
     */
    private $pagerType;

    /**
     * Valeur de la page
     * @access private
     * @var int
     */
    private $currentPage;

    /**
     * Sortie générée
     * @access private
     * @var array
     */
    private $output;

    /**
     * Nombre de page total
     * @access public
     * @var int
     */
    public $nbPageTotal;

    /**
     * Nombre d'élément par page
     * @access public
     * @var int
     */
    public $nbItemParPage;

    /**
     * Item minimal à utiliser pour la requete
     * @access public
     * @var int
     */
    public $itemMini;

    /**
     * Nombre de page afficher à coter de la page courante
     * Utilisée en mode avancé uniquement
     *
     * @access public
     * @var int
     */
    public $pageAdjacente;

    /**
     * Tableau avec les lien précédent/suivant
     * Utilisé en mode avancé
     * @access public
     * @var array
     */
    public $nextPrev;

    /**
     * Constructeur. Initialise la pagination
     *
     * @access public
     * @param int $total Nombre total de résultat
     * @param int $type Type de pagination
     * @param int $nbParPage Nombre d'éléments par page
     * @param string $get Nom du paramètre de page
     */
    public function __construct($total, $type = 1, $nbParPage = 10, $page = 1)
    {
        $this->nbItemTotal   = intval($total);
        $this->nbItemParPage = $nbParPage;

        $this->currentPage = intval($page);
        $this->nbPageTotal = ceil($this->nbItemTotal / $this->nbItemParPage);
        $this->pagerType   = intval($type);
        $this->output      = array();
        $this->nextPrev = array();
        $this->pageAdjacente = 2;
    }

    /**
     * Créer la pagination
     *
     * @access public
     * @return array
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
                throw new \UnexpectedValueException('Le type de pagination doit être BASIC ou ADVANCED');

            return $this->output;
        }
        else
        {
            //1 seule page
            return false;
        }
    }

    /**
     * Construit la pagination de base
     * @access private
     * @param int $pageEnCours
     */
    private function buildSimple($pageEnCours)
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
     * Construit la pagination avancée
     * @access private
     * @param unknown_type $pageEnCours
     */
    private function buildAdvanced($pageEnCours)
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