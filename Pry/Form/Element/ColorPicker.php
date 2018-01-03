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
 * Element ColorPicker. Permet de choisir une couleur dans une palette
 * @category Pry
 * @package Form
 * @subpackage Form_Element
 * @version 1.5.0 
 * @author Olivier ROGER <oroger.fr>   
 *
 */
class Colorpicker extends Text
{

    /**
     * Constructeur. Défini la couleur de base à rouge
     *
     * @param string $nom
     * @param Form_Form $form
     */
    public function __construct($nom, $form)
    {
        parent::__construct($nom, $form);
        $this->value('#FF0000');
    }

    /**
     * Défini une couleur de base en RGB
     *
     * @param array $rgb
     * @access public
     * @return Form_Element_Colorpicker
     */
    public function setRGBColor($rgb)
    {
        for ($i = 0; $i < 2; $i++) {
            if ($rgb[$i] < 0 || $rgb[$i] > 255)
                throw new Image_Exception('La valeur RGB est incorrecte (compris en 0 et 255');
        }

        $this->value('#' . str_pad((dechex($rgb[0]) . dechex($rgb[1]) . dechex($rgb[2])), 6, "0", STR_PAD_LEFT));
        return $this;
    }

    /**
     * Défini une couleur de base en Hexa
     *
     * @param string $color
     * @access public
     * @return Form_Element_Colorpicker
     */
    public function setHexaColor($color)
    {
        if ($color[0] == '#')
            $this->value($color);
        else
            $this->value('#' . $color);

        return $this;
    }

    /**
     * Vérifie la couleur recu
     *
     * @param string $value
     * @access public
     * @return boolean
     */
    public function isValid($value)
    {
        if (parent::isValid($value))
        {
            if (!preg_match('`^#([0-9a-fA-F]{6})$`', $value))
            {
                $this->errorMsg = Error::COLOR;
                return false;
            }
            return true;
        }
    }

    /**
     * Ecrit l'objet
     *
     * @return unknown
     */
    public function __toString()
    {
        $field = '';
        $field.=parent::__toString();
        $this->form->javascript .='$("#' . $this->attrs['id'] . '").gccolor({
			onChange : function(target,color){
				target.val("#"+color);
			}
		});';
        return $field;
    }

}