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

use InvalidArgumentException;
/**
 * Validator factory
 * @author Olivier ROGER <oroger.fr>
 */
class Validate
{

    /**
     * Available validators
     *
     * @var array
     */
    private $validators;


    public function __construct()
    {
        $this->validators = [];
    }

    /**
     * Add a new Validator
     *
     * @param string $nom Validator name
     * @param array $options Validator option
     * @param string $message Custom error message
     * @return Validate
     * @throws InvalidArgumentException if a validator does not exist
     */
    public function addValidator(string $nom, ?array $options = null, string $message = '')
    {
        if ($this->validatorExist($nom))
        {
            $class = '\Pry\Validate\Validator\\' . $nom;
            if (is_null($options))
                $objet = new $class();
            else
                $objet = new $class($options);

            if ($message != '')
            {
                if($objet instanceof ValidateAbstract)
                    $objet->setMessage($message);
            }
            $this->validators[] = $objet;
            unset($objet);
        }
        else
            throw new InvalidArgumentException('Validateur inconnu');
        return $this;
    }

    /**
     * Validate a value
     *
     * @param string $value
     * @return mixed Retourne true si valide, message d'erreur sinon
     */
    public function isValid(string $value): bool
    {
        foreach ($this->validators as $validator) {
            if (!$validator->isValid($value))
            {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if a validator exists
     *
     * @param string $nom name
     * @return boolean
     */
    private function validatorExist($nom): bool
    {
        return file_exists(dirname(__FILE__) . '/Validator/' . $nom . '.class.php');
    }

}