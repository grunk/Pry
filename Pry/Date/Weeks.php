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

use Pry\Date\Ferie;

/**
 * Classe Weeks
 *
 * Class permettant la gestion d'un semainier
 *
 * @category Pry
 * @package Date
 * @version 1.1.1
 * @author Olivier ROGER <oroger.fr>
 *
 */
class Weeks
{

    /**
     * Mois dans laquelle la semaine se trouve
     * @var string
     */
    public $mois;

    /**
     * Jours de la semaine
     * @var array
     */
    public $jours;

    /**
     * Tableau des mois en français
     * @var array
     */
    private $tabMois;

    /**
     * Instance des jours fériés
     * @var \Pry\Date\Ferie
     */
    public $ferie;

    /**
     * Année de la semaine
     * @var int
     */
    private $annee;

    /**
     * Numéro de semaine
     * @var int
     */
    private $semaine;

    /**
     * Timestamp des jour de la semaine
     * @var array
     */
    private $daysOfWeek;

    /**
     * Constructeur
     * @param int $semaine Numéro de la semaine
     * @param int $annee Années souhaité
     */
    public function __construct($semaine, $annee)
    {
        $this->jours = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche');
        $this->tabMois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        $this->ferie   = new Ferie($annee);
        $this->semaine = intval($semaine);
        $this->annee   = intval($annee);
    }

    /**
     * Retourne les limite Lundi et Dimanche pour une semaine donnée
     *
     * @return array
     */
    public function getBoundaries()
    {
        $dt = new \DateTime($this->annee . 'W' . sprintf('%02d', $this->semaine));

        $lundi    = (int) $dt->format('U');
        $dt->modify('Sunday');
        $dimanche = (int) $dt->format('U');

        $this->daysOfWeek[0] = $lundi;
        $this->mois          = $this->tabMois[date("m", $this->daysOfWeek[0]) - 1];

        return array('lundi' => $lundi, 'dimanche' => $dimanche);
    }

    /**
     * Calcul les 7 jours de la semaine à partir du lundi trouvé.
     * getBoundaries doit avoir été appellé auparavant.
     *
     * @return array
     */
    public function computeDays()
    {
        if (empty($this->daysOfWeek))
            throw new \BadFunctionCallException('Use getBoundaries() first');
        for ($i = 1; $i < 7; $i++)
            $this->daysOfWeek[$i] = $this->daysOfWeek[$i - 1] + 86400;

        return $this->daysOfWeek;
    }

    /**
     * Calcul l'année et le numéro de semaine suivant/précédent
     *
     * @return array
     */
    public function computeNextPrev()
    {
        if ($this->semaine == 1)
        {
            $anneePrec   = $this->annee - 1;
            $semainePrec = date("W", $this->daysOfWeek[0] - 86400);

            $anneeSuiv   = $this->annee;
            $semaineSuiv = 2;
        }
        else if ($this->semaine == 52)
        {
            $anneePrec   = $this->annee;
            $semainePrec = 51;

            if (date("W", $this->daysOfWeek[6] + 86400) == 53)
            {
                $anneeSuiv   = $this->annee;
                $semaineSuiv = 53;
            }
            else
            {
                $anneeSuiv   = $this->annee + 1;
                $semaineSuiv = 1;
            }
        }
        elseif ($this->semaine == 53)
        {
            $anneePrec   = $this->annee;
            $semainePrec = 52;
            $anneeSuiv   = $this->annee + 1;
            $semaineSuiv = 1;
        }
        else
        {
            $anneePrec   = $anneeSuiv   = $this->annee;
            $semainePrec = $this->semaine - 1;
            $semaineSuiv = $this->semaine + 1;
        }

        return array('annee' => array('next' => $anneeSuiv, 'prev' => $anneePrec),
            'semaine' => array('next' => $semaineSuiv, 'prev' => $semainePrec));
    }

    /**
     * Retourne les dates des jours sous format mysql
     * 
     * @return array
     */
    public function getMysqlDays()
    {
        $total     = count($this->daysOfWeek);
        $mysqlDays = array();

        for ($i = 0; $i < $total; $i++)
            $mysqlDays[$i] = date("Y-m-d", $this->daysOfWeek[$i]);

        return $mysqlDays;
    }

}