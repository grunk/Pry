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

use Pry\Util\Strings;
use Pry\Validate\ValidateAbstract;

/**
 * Email address validator.
 * @author Olivier ROGER <oroger.fr>
 * 
 */
class Email extends ValidateAbstract
{

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
    public function isValid(string $string): bool
    {
        $string = $this->cleanString((string) $string);
        return Strings::isMail($string);
    }

}