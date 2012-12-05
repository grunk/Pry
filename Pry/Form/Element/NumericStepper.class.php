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

namespace Pry\Form\Element;

use Pry\Form\Error;

/**
 * Element numeric stepper
 * @category Pry
 * @package Form
 * @subpackage Form_Element
 * @version 1.1.0 
 * @author Olivier ROGER <oroger.fr>
 */
class NumericStepper extends Text
{

    /**
     * Valeur maxi autorisée
     *
     * @var mixed
     * @access private
     */
    private $max;

    /**
     * Valeur mini autorisée
     *
     * @var mixed
     * @access private
     */
    private $min;

    /**
     * Valeur de pas
     *
     * @var mixed
     * @access public
     */
    private $step;

    /**
     * Valeur de départ
     *
     * @var mixed
     * @access private
     */
    private $start;

    /**
     * Chemin vers le sprite contenant les boutons
     * @var string
     */
    private $sprite;

    /**
     * Constructeur. Initialise les valeurs par défaut
     *
     * @param string $nom
     * @param Form_Form $form
     */
    public function __construct($nom, $form)
    {
        parent::__construct($nom, $form);
        $this->max       = 99;
        $this->min       = 0;
        $this->step      = 1;
        $this->start     = 0;
        $this->sprite    = 'spinbox-sprite.png';
        $this->container = '';
        $this->value     = $this->start;
    }

    /**
     * Défini la valeur max
     *
     * @param mixed $value
     * @access public
     * @return Form_Element_NumericStepper
     */
    public function setMax($value)
    {
        $this->max = $value;
        return $this;
    }

    /**
     * Défini la valeur mini
     *
     * @param mixed $value
     * @access public
     * @return Form_Element_NumericStepper
     */
    public function setMin($value)
    {
        $this->min = $value;
        return $this;
    }

    /**
     * Défini la valeur de pas
     *
     * @param mixed $value
     * @access public
     * @return Form_Element_NumericStepper
     */
    public function setStep($value)
    {
        $this->step = $value;
        return $this;
    }

    /**
     * Défini la valeur de départ
     *
     * @param mixed $value
     * @access public
     * @return Form_Element_NumericStepper
     */
    public function startAt($value)
    {
        $this->startAt($value);
        $this->value($value);
        return $this;
    }

    /**
     * Défini le chemin des images des boutons.
     * Chemins relatifs à la page web
     *
     * @param string $up
     * @param string $down
     * @access public
     * @return Form_Element_NumericStepper
     */
    public function setSprite($path)
    {
        $this->sprite = $path;
        return $this;
    }

    /**
     * Valide la donnée recue
     *
     * @param mixed $value
     * @access public
     * @return boolean
     */
    public function isValid($value)
    {
        if (parent::isValid($value))
        {
            if (!is_numeric($value))
            {
                $this->errorMsg = Error::NUMERIC;
                return false;
            }
        }
        return true;
    }

    /**
     * Ecrit l'objet
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        //$this->check();
        $field = '';
        $field.= parent::__toString();
        $this->form->javascript .= '$("#' . $this->attrs['id'] . '").spinbox({min:' . $this->min . ',max:' . $this->max . ',step:' . $this->step . '});';
        return $field;
    }

}

?>