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
 * Validateur Alphabétique.
 * Accepte également le -
 * @category Pry
 * @package Validate
 * @subpackage Validate_Validator
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr>
 */
class Alpha extends ValidateAbstract
{

    /**
     * Constructeur. par défaut accepte les espaces
     *
     * @param boolean $espace
     * @access public
     */
    public function __construct($espace = true)
    {
        $this->espace   = (boolean) $espace;
        $this->errorMsg = "n'est pas une valeur alphabétique";
    }

    /**
     * Vérifie la présence de caractère alphabétique (uniquement)
     *
     * @param string $string
     * @return boolean
     */
    public function isValid($string)
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