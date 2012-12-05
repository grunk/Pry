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
 * Validateur de code postal Français. Prend en charge les codes postaux corse.
 * @category Pry
 * @package Validate
 * @subpackage Validate_Validator
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr>
 */
class Cp extends ValidateAbstract
{

    /**
     * Constructeur
     * @access public
     */
    public function __construct()
    {
        $this->errorMsg = "n'est pas un code postal valide";
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
        $pattern = '`^(2[ab]|0[1-9]|[1-9][0-9])[0-9]{3}$`';
        if (preg_match($pattern, $string))
            return true;
        return false;
    }

}