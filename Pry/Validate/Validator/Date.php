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
 * Date validator.
 * Try to validate date in the following format : fr,us,sql,sqldatetime
 * @author Olivier ROGER <oroger.fr>
 * 
 * <code>
 * $factory = new Validate();
 * $factory -> addValidator('Date','fr');
 * </code>
 *       
 *
 */
class Date extends ValidateAbstract
{

    public const DATE_SQL         = 'sql';
    public const DATE_SQLDATETIME = 'sqldatetime';
    public const DATE_FR          = 'fr';
    public const DATE_US          = 'us';

    private $format;

    public function __construct(string $format = 'fr')
    {
        $this->errorMsg = "n'est pas une date valide";
        $this->format   = $format;
    }

    /**
     * Validate date accordinf to it's format
     *
     * @param string $date
     * @return bool
     */
    public function isValid($date): bool
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