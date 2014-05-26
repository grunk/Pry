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
 * Classe bench. Mesure de performance de script php avec possibilité d'ajout de flag intermédiaire.
 * 
 * @category Pry
 * @package Util
 * @version 1.0.3
 * @author Olivier ROGER <oroger.fr>
 * 
 */
class Bench // Amel bench ? ==>[]
{

    /**
     * Temps de début.<br />Valeur par défaut : 0
     * @var int
     * @access private
     */
    private $start;

    /**
     * Tableau de résultat.
     * @var array
     * @access public
     */
    private $resultat;

    /**
     * Constructeur.
     * Initialisation des valeurs
     *
     */
    public function __construct()
    {
        $this->start    = 0;
        $this->resultat = array();
    }

    /**
     * Lance le début du calcul de temps
     * @return float µtime de départ
     */
    public function start()
    {
        $this->start = $this->get_micro();
        return $this->start;
    }

    /**
     * Prend un temps intermédiaire.
     *
     * @param $nom Nom du temps intermédiaire
     * @return float delay entre début et ajout du flag
     */
    public function add_flag($nom)
    {
        $top                  = $this->get_micro() - $this->start;
        $this->resultat[$nom] = $top;
        
        return $top;
    }

    /**
     * Arrete le calcul.
     * @return float µtime à l'arret
     */
    public function stop()
    {
        $end                     = $this->get_micro() ;
        $this->resultat['total'] = $end - $this->start;
        return $end;
    }

    /**
     * Donne les tableau de résultat.
     *
     * @return array Tableau avec comme clé les flags et "total".
     */
    public function result()
    {
        return $this->resultat;
    }

    /**
     * Donne les miliseconde
     * @access private
     * @return Float temps en milisecondes
     */
    private function get_micro()
    {
        $temps = microtime();
        $temps = explode(' ', $temps);
        $temps = $temps[1] + $temps[0];
        return (float) $temps;
    }

}