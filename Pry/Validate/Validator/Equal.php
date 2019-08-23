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
 * Check equality
 * @category Pry
 * @package Validate
 * @subpackage Validate_Validator
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr>
 *       
 * <code>
 * $validator = new Validator\Equal(45);
 * $validator -> isValid(12); // retourne false
 * $validator -> isValid(45); // retourne true
 * </code>
 */
class Equal extends ValidateAbstract
{

    private $reference;

    public function __construct($ref)
    {
        $this->reference = $ref;
        $this->errorMsg  = "n'est pas égale à $this->reference";
    }

    /**
     * Vérifie l'égalité des deux valeurs
     *
     * @param mixed $string Element à valider
     * @return boolean
     */
    public function isValid($string):bool
    {
        return $this->reference == $string;

    }

}