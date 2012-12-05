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
 * Validateur Alphanuméric
 * 
 * @category Pry
 * @package Validate
 * @subpackage Validate_Validator
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr>
 */
class Alnum extends ValidateAbstract
{

    /**
     * Constructeur. Par défaut autorise les espaces
     *
     * @param boolean $espace Autorise ou non les espaces dans les chaines
     * @access public
     */
    public function __construct($espace = true)
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
    public function isValid($string)
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