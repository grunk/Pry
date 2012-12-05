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

namespace Pry\Validate\Validator;

use Pry\Validate\ValidateAbstract;

/**
 * Validateur de date.
 * Permet de valider des date du type fr,us,sql,sqldatetime
 * @category Pry
 * @package Validate
 * @subpackage Validate_Validator
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr>
 * 
 * <code>
 * $factory = new Validate_Validate();
 * $factory -> addValidator('Date','fr');
 * </code>
 *       
 *
 */
class Date extends ValidateAbstract
{

    const DATE_SQL         = 'sql';
    const DATE_SQLDATETIME = 'sqldatetime';
    const DATE_FR          = 'fr';
    const DATE_US          = 'us';

    private $format;

    public function __construct($format = 'fr')
    {
        $this->errorMsg = "n'est pas une date valide";
        $this->format   = $format;
    }

    /**
     * Valide la date en fonction de son format
     *
     * @param string $date
     * @return boolean
     */
    public function isValid($date)
    {
        switch ($this->format)
        {
            case self::DATE_FR:
                {
                    if (preg_match('`^\d{1,2}/\d{1,2}/(\d{4}|\d{2})$`', $date))
                    {
                        list($d, $m, $y) = explode('/', $date);
                        if (checkdate($m, $d, $y))
                            return true;
                    }
                    return false;
                }
            case self::DATE_US:
                {
                    if (preg_match('`^\d{1,2}/\d{1,2}/(\d{4}|\d{2})$`', $date))
                    {
                        list($m, $d, $y) = explode('/', $date);
                        if (checkdate($m, $d, $y))
                            return true;
                    }
                    return false;
                }
            case self::DATE_SQL:
                {
                    if (preg_match('`^(\d{4}|\d{2})-\d{1,2}-\d{1,2}$`', $date))
                    {
                        list($y, $m, $d) = explode('-', $date);
                        if (checkdate($m, $d, $y))
                            return true;
                    }
                    return false;
                }
            case self::DATE_SQLDATETIME:
                {
                    if (preg_match('`^(\d{4}|\d{2})-\d{1,2}-\d{1,2} \d{1,2}:\d{1,2}:\d{1,2}$`', $date))
                    {
                        list($date, $heure) = explode(' ', $date);
                        list($y, $m, $d) = explode('-', $date);
                        list($h, $mi, $s) = explode(':', $heure);
                        if (checkdate($m, $d, $y) && ($h >= 0 && $h < 24) && ($mi >= 0 && $mi < 60) && ($s >= 0 && $s < 60))
                            return true;
                    }
                    return false;
                }
            default:
                break;
        }
    }

}