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
 * Inclusive numercial interval validator
 * @author Olivier ROGER <oroger.fr>
 * @todo Gestion inclusif/strict      
 *
 */
class Interval extends ValidateAbstract
{

    private $max;
    private $min;

    /**
     * Constructor
     *
     * @param array $numbers list of numbers
     * @access public
     */
    public function __construct(array $numbers)
    {
        if (!isset($numbers[0]) || !isset($numbers[1]))
        {
            throw new \InvalidArgumentException('Veuillez fournir les options d\'interval : array(12,24)');
        }
        $this->max      = max($numbers);
        $this->min      = min($numbers);
        $this->errorMsg = 'La valeur n\'est pas contenu dans l\'interval ' . $this->min . ' - ' . $this->max;
    }

    /**
     * Check if $value is between $numbers
     *
     * @param int $value
     * @return bool
     */
    public function isValid($value): bool
    {
        if (is_numeric($value))
            if ($value >= $this->min && $value <= $this->max)
                return true;
        return false;
    }

}