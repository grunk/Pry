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
 * Validateur d'interval numérique. Inclusif
 * @category Pry
 * @package Validate
 * @subpackage Validate_Validator
 * @version 1.1.0 
 * @author Olivier ROGER <oroger.fr>
 * @todo Gestion inclusif/strict      
 *
 */
class Interval extends ValidateAbstract
{

    private $max;
    private $min;

    /**
     * Constructeur
     *
     * @param array $options
     * @access public
     */
    public function __construct(array $options)
    {
        if (!isset($options[0]) || !isset($options[1]))
        {
            throw new \InvalidArgumentException('Veuillez fournir les options d\'interval : array(12,24)');
        }
        $this->max      = max($options);
        $this->min      = min($options);
        $this->errorMsg = 'La valeur n\'est pas contenu dans l\'interval ' . $this->min . ' - ' . $this->max;
    }

    /**
     * Vérifie si $value est dans l'interval
     *
     * @param int $value
     * @access public
     * @return boolean
     */
    public function isValid($value)
    {
        if (is_numeric($value))
            if ($value >= $this->min && $value <= $this->max)
                return true;
        return false;
    }

}