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
 * French ZipCode validator
 * @author Olivier ROGER <oroger.fr>
 */
class Cp extends ValidateAbstract
{

    public function __construct()
    {
        $this->errorMsg = "n'est pas un code postal valide";
    }

    /**
     * Validate
     *
     * @param string $string
     * @return boolean
     */
    public function isValid(string $string): bool
    {
        $string  = $this->cleanString((string) $string);
        $pattern = '`^(2[ab]|0[1-9]|[1-9][0-9])[0-9]{3}$`';
        if (preg_match($pattern, $string))
            return true;
        return false;
    }

}