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
 * Validateur d'adresse email.
 * @category Pry
 * @package Validate
 * @subpackage Validate_Validator
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr>
 * 
 */
class Email extends ValidateAbstract
{

    /**
     * Constructeur
     * @access public
     */
    public function __construct()
    {
        $this->errorMsg = "n'est pas un email valide";
    }

    /**
     * Validation
     *
     * @param string $string Elément à valider
     * @return boolean
     */
    public function isValid($string)
    {
        $string = $this->cleanString((string) $string);
        return \Pry\Util\Strings::isMail($string);
    }

}