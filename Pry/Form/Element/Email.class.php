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
 * Element Email. Permet de valider une adresse mail dans un champs text
 * 
 * @category Pry
 * @package Form
 * @subpackage Form_Element
 * @version 1.0.1 
 * @author Olivier ROGER <oroger.fr>
 */
class Email extends Text
{

    /**
     * Validation de contenu
     *
     * @param string $value
     * @access public
     * @return boolean
     */
    public function isValid($value)
    {
        if (parent::isValid($value))
        {
            if (\Pry\Util\Strings::isMail($value) || (!$this->required && empty($value)))
                return true;
            else
                $this->errorMsg = Error::MAIL;
            return false;
        }
    }

}