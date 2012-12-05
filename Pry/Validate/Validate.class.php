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
 * Factory de validateur
 * @category Pry
 * @package Validate
 * @version 1.1.0 
 * @author Olivier ROGER <oroger.fr>
 */
class Validate
{

    /**
     * Validateurs instanciés
     *
     * @var array
     * @access private
     */
    private $validators;

    /**
     * Constructeur
     *
     */
    public function __construct()
    {
        $this->validators = array();
    }

    /**
     * Ajoute un nouveau validateur
     *
     * @param string $nom Nom du validateur
     * @param array $options Option possible pour le validateur
     * @param string $message Message d'erreur personalisé
     * @access public
     * @return Validate_Validate
     */
    public function addValidator($nom, $options = null, $message = '')
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
                $objet->setMessage($message);
            }
            $this->validators[] = $objet;
            unset($objet);
        }
        else
            throw new \InvalidArgumentException('Validateur inconnu');
        return $this;
    }

    /**
     * Validation de la valeur avec les différents validateurs
     *
     * @param string $value
     * @access public
     * @return mixed Retourne true si valide, message d'erreur sinon
     */
    public function isValid($value)
    {
        foreach ($this->validators as $validator) {
            if (!$validator->isValid($value))
            {
                return $validator->getError();
            }
        }
        return true;
    }

    /**
     * Vérifie l'existance du validateur
     *
     * @param string $nom
     * @return boolean
     */
    private function validatorExist($nom)
    {
        return file_exists(dirname(__FILE__) . '/Validator/' . $nom . '.class.php');
    }

}