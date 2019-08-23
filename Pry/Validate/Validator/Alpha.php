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
 * Alphabetical validator
 * Also allow the -
 * @author Olivier ROGER <oroger.fr>
 */
class Alpha extends ValidateAbstract
{

    /**
     * Default constructor
     *
     * @param bool $espace Allow space in string or not
     */
    public function __construct(bool $espace = true)
    {
        $this->espace   = (bool) $espace;
        $this->errorMsg = "n'est pas une valeur alphabétique";
    }

    /**
     * Vérifie la présence de caractère alphabétique (uniquement)
     *
     * @param string $string
     * @return bool
     */
    public function isValid(string $string): bool
    {
        $string = $this->cleanString($string);
        //On cherche si on ne trouve pas d alpha. Donc si true = la chaine n'est pas alpha on renvoi false.
        if ($this->espace)
            if (preg_match('/[^a-zA-Z\s\-]/', $string))
                return false;
            else
                return true;
        else
        if (preg_match('/[^a-zA-Z\-]/', $string))
            return false;
        else
            return true;
    }

}