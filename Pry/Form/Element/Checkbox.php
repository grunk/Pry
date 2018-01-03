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
 * Elément checkbox
 * 
 * @category Pry
 * @package Form
 * @subpackage Form_Element
 * @version 1.1.2 
 * @author Olivier ROGER <oroger.fr>  
 *
 */
class Checkbox extends Input
{

    /**
     * Case cochée
     *
     * @var boolean
     * @access private
     */
    private $checked;

    /**
     * Constructeur. Par défaut case non cochée
     *
     * @param string $nom
     * @param Form_Form $form
     * @access public
     */
    public function __construct($nom, $form)
    {
        parent::__construct($nom, $form);
        $this->attrs['type'] = 'checkbox';
        $this->checked       = false;
    }

    /**
     * Défini si la case est cochée ou non
     *
     * @param boolean $bool
     * @access public
     * @return Form_Element_Checkbox
     */
    public function checked($bool = true)
    {
        $this->checked = $bool;
        return $this;
    }

    /**
     * Ecrit l'objet
     *
     * @access public
     * @return ustring
     */
    public function __toString()
    {
        $this->cssClass();
        $label = '';
        $css   = '';

        if (!empty($this->label))
        {
            $label = "\t" . '<span class="' . $this->cssLabel . '">' . $this->label . '</span>' . "\n";
            if (!empty($this->info))
                $label.="\t" . '<img src="' . $this->imgInfo . '" id="' . $this->attrs['name'] . '_tooltip" class="form_tooltip" title="' . $this->info . '" alt="" style="cursor:help;" />';
            if ($this->labelNewLine)
                $label.="\t" . '<br />' . "\n";
        }

        $error = '';

        if (!is_null($this->errorMsg))
        {
            $error = '<span class="' . $this->errorClass . '">' . $this->errorMsg . '</span><br />';
        }

        //Posted value ou value par défaut
        $value = $this->form->getPostedvalue($this->attrs['name']);
        if (empty($value))
        {
            $value                  = $this->value;
            if (!$this->checked)
                unset($this->attrs['checked']);
            else
                $this->attrs['checked'] = 'checked';
        }
        elseif (!empty($value))
        {
            $this->attrs['checked'] = 'checked';
        }
        elseif ($this->value == $value || $this->checked)
            $this->attrs['checked'] = 'checked';

        $attributs = $this->attrsToString();

        $field = "\t" . '<input ' . $css . ' ' . $attributs . ' />' . "\n";

        return $field . $label . $error;
    }

}