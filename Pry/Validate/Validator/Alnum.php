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
 * Alphanumeric validator
 *
 * @author Olivier ROGER <oroger.fr>
 */
class Alnum extends ValidateAbstract
{

    /**
     * Default constructor
     *
     * @param boolean $espace Allow space in string or not
     */
    public function __construct(bool $espace = true)
    {
        $this->espace   = (boolean) $espace;
        $this->errorMsg = "n'est pas une valeur alphanumérique";
    }

    /**
     * Vérifie si $string est alphanumérique
     *
     * @param string $string
     * @return boolean
     */
    public function isValid(string $string): bool
    {
        $string = $this->cleanString($string);
        //On cherche si on ne trouve pas d alnum. Donc si true = la chaine n'est pas alnum on renvoi false.
        if ($this->espace)
            if (preg_match('/[^a-zA-Z0-9\s\-]/', $string))
                return false;
            else
                return true;
        else
        if (preg_match('/[^a-zA-Z0-9\-]/', $string))
            return false;
        else
            return true;
    }

}