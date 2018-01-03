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
 * Element MAC. permet de valider une adresse MAC dans un champs text
 * 
 * @category Pry
 * @package Form
 * @subpackage Form_Element
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr>
 */
class Mac extends Text
{

    /**
     * Construteur.
     *
     * @param string $nom
     * @param Form_Form $form
     * @access public
     */
    public function __construct($nom, $form)
    {
        parent::__construct($nom, $form);
        $this->maxlength(17);
        $this->minLength(17);
    }

    /**
     * Valide l'adresse mac
     *
     * @param string $value
     * @access public
     * @return boolean
     */
    public function isValid($value)
    {
        if (parent::isValid($value))
        {
            if (\Pry\Util\Strings::isMac($value) || (!$this->required && $value == ''))
                return true;
            else
                $this->errorMsg = Error::NOTMAC;
        }
        return false;
    }

}