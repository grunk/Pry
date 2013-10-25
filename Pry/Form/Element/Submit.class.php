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
 * Element submit
 * @category Pry
 * @package Form
 * @subpackage Form_Element
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr>
 */
class Submit extends Input
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
            $this->attrs['type'] = 'submit';
    }

    /**
     * Ecrit l'objet
     *
     * @access public
     * @return unknown
     */
    public function __toString()
    {
        $css = implode(' ', $this->class);
        if ($css != '')
            $css = 'class="' . $css . '"';
        else
            $css = '';

        $attributs = $this->attrsToString();
        $field     = "\t" . '<input ' . $css . ' value="' . htmlspecialchars($this->value) . '" ' . $attributs . ' />' . "\n";
        if ($this->fieldNewLine)
            $field.="\t" . '<br />' . "\n";
        return $field;
    }

}