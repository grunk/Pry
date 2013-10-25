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

/**
 * Element Radio
 * @category Pry
 * @package Form
 * @subpackage Form_Element
 * @version 1.0.6 
 * @author Olivier ROGER <oroger.fr>
 */
class Radio extends Multi
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
        $this->attrs['type'] = 'radio';
    }

    /**
     * Affiche les boutons radios
     * @access public
     * @return string
     */
    public function __toString()
    {
        $css   = $this->cssClass();
        $label = '';
        if (!empty($this->label))
        {
            $label = "\t" . '<span class="' . $this->cssLabel . '">' . $this->label . '</span>' . "\n";
            if (!empty($this->info))
                $label.="\t" . '<img src="' . $this->imgInfo . '" id="' . $this->attrs['name'] . '_tooltip" class="form_tooltip" title="' . $this->info . '" alt="" style="cursor:help;" />';
            if ($this->labelNewLine)
                $label.="\t" . '<br />' . "\n";
        }

        $field     = '';
        $attributs = $this->attrsToString();
        //Posted value ou value par défaut
        $value     = $this->form->getPostedvalue($this->attrs['name']);
        if ($value == '')
            $value     = $this->value;

        foreach ($this->choix as $valhtml => $valAffichee) {
            if ($value == $valhtml)
                $checked = ' checked="checked"';
            else
                $checked = '';

            $field.="\t" . '<input ' . $css . ' value="' . htmlspecialchars($valhtml) . '" ' . $attributs . $checked . ' /> ' . $valAffichee . "\n";
            if ($this->fieldNewLine)
                $field.="\t" . '<br />' . "\n";
        }
        $error = '';
        if (!is_null($this->errorMsg))
        {
            $error = '<span class="' . $this->errorClass . '">' . $this->errorMsg . '</span><br />';
        }
        return $label . $field . $error;
    }

}