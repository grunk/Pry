<?php
/*
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
 * Class Quarter
 * 
 * Helper pour la gestion des trimestres
 *
 * @category Pry
 * @package Date
 * @version 1.0.0
 * @author Olivier ROGER <oroger.fr>
 */
class Quarter 
{
    /**
     *  Retourne le trimestre en cours
     * @return array Date de début et fin du trimestre
     */
    public static function getCurrent()
    {
        return Quarter::getFromDate(new \DateTime());
    }
    
    /**
     * Retourne le trimestre suivant
     * @param Datetime $date Date optionnelle à partir de laquelle calculer le suivant
     * @return array Date de début et fin du trimestre
     */
    public static function getNext(\DateTime $date = null)
    {
        if(empty($date))
            $date   = new DateTime();
        
        $month  = $date->format('n');
        $year   = $date->format('Y');
        $current_quarter = ceil($month / 3);
        
        if($current_quarter == 4) {
            $current_quarter = 1;
            $year++;
        } else {
            $current_quarter++;
        }
        
        $quarter = 'getQ'.$current_quarter;
        
        return Quarter::$quarter($year); 
    }
    
    /**
     * Retourne le trimestre suivant
     * @param Datetime $date Date optionnelle à partir de laquelle calculer le précédent
     * @return array Date de début et fin du trimestre
     */
    public static function getPrevious(\DateTime $date = null)
    {
        if(empty($date))
            $date   = new DateTime();
        
        $month  = $date->format('n');
        $year   = $date->format('Y');
        $current_quarter = ceil($month / 3);
        
        if($current_quarter == 1) {
            $current_quarter = 4;
            $year--;
        } else {
            $current_quarter--;
        }
        
        $quarter = 'getQ'.$current_quarter;
        
        return Quarter::$quarter($year); 
    }
    
    /**
     * Retourne le trimestre associé à une date
     * @param \DateTime $date Date
     * @return array date début et fin
     */
    public static function getFromDate(\DateTime $date)
    {
        if(empty($date))
            return new \BadMethodCallException("Date time expected");
        
        $month = $date->format('n');
        $year  = $date->format('Y');
        
        $current_quarter = ceil($month / 3);
        $quarter = 'getQ'.$current_quarter;

        return Quarter::$quarter($year); 

    }
    
    /**
     * Retourne le 1er trimestre
     * @param int $year Année optionelle
     * @return array Date de début et fin du trimestre
     */
    public static function getQ1($year = null)
    {
        if(empty($year))
            $year = date('Y');
        
        return array(
            new \DateTime('first day of january '.$year),
            new \DateTime('last day of march '.$year)
        );
    }
    
    /**
     * Retourne le 2eme trimestre
     * @param int $year Année optionelle
     * @return array Date de début et fin du trimestre
     */
    public static function getQ2($year = null)
    {
        if(empty($year))
            $year = date('Y');
        
        return array(
            new \DateTime('first day of April '.$year),
            new \DateTime('last day of June '.$year)
        );
    }
    
    /**
     * Retourne le 3ème trimestre
     * @param int $year Année optionelle
     * @return array Date de début et fin du trimestre
     */
    public static function getQ3($year = null)
    {
        if(empty($year))
            $year = date('Y');
        
        return array(
            new \DateTime('first day of July '.$year),
            new \DateTime('last day of September '.$year)
        );
    }
    
    /**
     * Retourne le 4ème trimestre
     * @param int $year Année optionelle
     * @return array Date de début et fin du trimestre
     */
    public static function getQ4($year = null)
    {
        if(empty($year))
            $year = date('Y');
        
        return array(
            new \DateTime('first day of October '.$year),
            new \DateTime('last day of December '.$year)
        );
    }
}
