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

use Pry\Form\Input;

/**
 * Element Hidden
 * @category Pry
 * @package Form
 * @subpackage Form_Element
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr>  
 *
 */
class Hidden extends Input
{

    /**
     * Constructeur
     *
     * @param string $nom
     * @param Form_Form $form
     * @access public
     */
    public function __construct($nom, $form)
    {
        parent::__construct($nom, $form);
        if (!isset($this->attrs['type']))
            $this->attrs['type'] = 'hidden';
    }

    /**
     * Ecriture de l'objet
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        $attributs = $this->attrsToString();
        return "\t" . '<input ' . $attributs . ' value="' . htmlspecialchars($this->value) . '" />' . "\n";
    }

}

?>