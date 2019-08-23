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
 * Numerical validator
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr> 
 *
 */
class Digit extends ValidateAbstract
{

    public function __construct()
    {
        $this->errorMsg = "doit être composé de chiffre uniquement";
    }

    /**
     * Validation
     *
     * @param string $string Element à valider
     * @return boolean
     */
    public function isValid(string $string): bool
    {
        $string  = $this->cleanString((string) $string);
        $pattern = '`^([0-9]+)$`';
        if (preg_match($pattern, $string))
            return true;
        return false;
    }

}