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

namespace Pry\Date;

/**
 * Classe Ferie
 *
 * Class permettant de trouver les jours fériés d'une année. Valable pour la France
 *
 * @category Pry
 * @package Date
 * @version 1.1.0
 * @author Olivier ROGER <oroger.fr>
 *
 */
class Ferie
{

    /**
     * Liste des jours férié
     * @var array
     */
    private $tabDay = array();

    /**
     * Année format YYYY
     * @var int
     */
    private $annee;

    /**
     * Constructeur. Initialise les jours fériés fixes
     * @param int $annee
     */
    public function __construct($annee)
    {
        if (strlen($annee) < 4)
            throw new \InvalidArgumentException('Annee doit être sur 4 chiffres');

        $this->annee    = $annee;
        $this->tabDay[] = $annee . '-01-01';
        $this->tabDay[] = ($annee + 1) . '-01-01';
        $this->tabDay[] = ($annee - 1) . '-01-01';
        $this->tabDay[] = $annee . '-05-01';
        $this->tabDay[] = $annee . '-05-08';
        $this->tabDay[] = $annee . '-07-14';
        $this->tabDay[] = $annee . '-08-15';
        $this->tabDay[] = $annee . '-11-01';
        $this->tabDay[] = $annee . '-11-11';
        $this->tabDay[] = $annee . '-12-25';
        $this->computeDay();
    }

    /**
     * Calcul les jours fériés pouvant l'être
     */
    private function computeDay()
    {
        //Paques
        $tsPaques       = @easter_date($this->annee);
        $this->tabDay[] = date("Y-m-d", $tsPaques + 86400);
        //Ascencion
        $this->tabDay[] = date("Y-m-d", strtotime('+39 days', $tsPaques));
        //Pantecote
        $this->tabDay[] = date("Y-m-d", strtotime('+50 days', $tsPaques));
    }

    /**
     * Vérifie si un jour est férié
     * @param string $date Date au format Y-m-d
     * @return boolean
     */
    public function isFerie($date)
    {
        if (!is_string($date))
            throw new \InvalidArgumentException('Date sous forme Y-m-d');

        return in_array($date, $this->tabDay);
    }

    /**
     * Liste des jours fériés
     * @return array Liste des jours fériés 
     */
    public function getDays()
    {
        return $this->tabDay;
    }

}

?>