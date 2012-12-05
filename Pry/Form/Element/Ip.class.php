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
 * Element IP. Permet de valider une adresse IP dans un champs text
 * 
 * @category Pry
 * @package Form
 * @subpackage Form_Element
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr>     
 *
 */
class Ip extends Text
{

    /**
     * Constructeur. Défini la taille mini et maxi de l'ip
     *
     * @param string $nom
     * @param Form_Form $form
     */
    public function __construct($nom, $form)
    {
        parent::__construct($nom, $form);
        $this->maxlength(15);
        $this->minLength(7);
        $this->showMask = true;
    }

    /**
     * Validation de l'adresse IP
     *
     * @param string $value
     * @access public
     * @return boolean
     */
    public function isValid($value)
    {
        if (parent::isValid($value))
        {
            if (\Pry\Util\Strings::isIp($value) || (!$this->required && $value == ''))
                return true;
            else
                $this->errorMsg = Error::NOTIP;
        }
        return false;
    }

}

?>