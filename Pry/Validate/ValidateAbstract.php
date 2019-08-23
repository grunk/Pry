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
 * @abstract
 * @author Olivier ROGER <oroger.fr>
 */
abstract class ValidateAbstract
{

    protected $espace;
    protected $errorMsg;

    /**
     * Define an error message
     *
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->errorMsg = $message;
    }

    /**
     * Return the error
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->errorMsg;
    }

    /**
     * Remove accent from a string
     *
     * @param string $value
     * @return string
     */
    public function cleanString(string $value): string
    {
        $accent = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'à', 'á', 'â', 'ã', 'ä', 'å', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'È', 'É', 'Ê', 'Ë', 'è', 'é', 'ê', 'ë', 'Ç', 'ç', 'Ì', 'Í', 'Î', 'Ï', 'ì', 'í', 'î', 'ï', 'Ù', 'Ú', 'Û,', 'Ü', 'ù', 'ú', 'û', 'ü', 'ÿ', 'Ñ', 'ñ');
        $pasaccent = array('A', 'A', 'A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a', 'a', 'O', 'O', 'O', 'O', 'O', 'O', 'o', 'o', 'o', 'o', 'o', 'o', 'E', 'E', 'E', 'E', 'e', 'e', 'e', 'e', 'C', 'c', 'I', 'I', 'I', 'I', 'i', 'i', 'i', 'i', 'U', 'U', 'U,', 'U', 'u', 'u', 'u', 'u', 'y', 'N', 'n');
        return str_replace($accent, $pasaccent, $value);
    }

    /**
     * Check if valid
     * @param $string string to check
     * @abstract
     * @return boolean
     */
    abstract protected function isValid(string $string): bool;
}