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

namespace Pry\Validate;

/**
 *
 * @category Pry
 * @package Validate
 * @abstract 
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr>
 */
abstract class ValidateAbstract
{

    protected $espace;
    protected $errorMsg;

    /**
     * Défini le message d'erreur
     *
     * @param string $message
     * @access public
     */
    public function setMessage($message)
    {
        $this->errorMsg = $message;
    }

    /**
     * Retourne l'erreur
     *
     * @access public
     * @return string
     */
    public function getError()
    {
        return $this->errorMsg;
    }

    /**
     * Enlève les accents d'une chaine pour REGEX.
     *
     * @param string $value
     * @access public
     * @return string
     */
    public function cleanString($value)
    {
        $accent = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'à', 'á', 'â', 'ã', 'ä', 'å', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'È', 'É', 'Ê', 'Ë', 'è', 'é', 'ê', 'ë', 'Ç', 'ç', 'Ì', 'Í', 'Î', 'Ï', 'ì', 'í', 'î', 'ï', 'Ù', 'Ú', 'Û,', 'Ü', 'ù', 'ú', 'û', 'ü', 'ÿ', 'Ñ', 'ñ');
        $pasaccent = array('A', 'A', 'A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a', 'a', 'O', 'O', 'O', 'O', 'O', 'O', 'o', 'o', 'o', 'o', 'o', 'o', 'E', 'E', 'E', 'E', 'e', 'e', 'e', 'e', 'C', 'c', 'I', 'I', 'I', 'I', 'i', 'i', 'i', 'i', 'U', 'U', 'U,', 'U', 'u', 'u', 'u', 'u', 'y', 'N', 'n');
        return str_replace($accent, $pasaccent, $value);
    }

    /**
     * Validation
     *
     * @abstract 
     * @param string $string
     * @return boolean
     */
    abstract protected function isValid($string);
}