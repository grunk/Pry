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
 * Validateur d'heure format sql.
 * @category Pry
 * @package Validate
 * @subpackage Validate_Validator
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr>
 * 
 */
class Time extends ValidateAbstract
{

    /**
     * Constructeur
     * @access public
     */
    public function __construct()
    {
        $this->errorMsg = "n'est pas une heure valide";
    }

    /**
     * Validation
     *
     * @param string $string Elément à valider
     * @return boolean
     */
    public function isValid($string)
    {
        $string  = $this->cleanString((string) $string);
        $pattern = '`^([0-9]{2}:[0-9]{2}:[0-9]{2})$`';
        if (preg_match($pattern, $string))
        {
            list($heure, $min, $sec) = explode(':', $string);
            if (($heure >= 0 && $heure < 24) && ($min >= 0 && $min < 60) && ($sec >= 0 && $sec < 60))
                return true;
        }
        return false;
    }

}