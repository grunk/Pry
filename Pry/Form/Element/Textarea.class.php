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

use Pry\Form\Field;

/**
 * Element Textarea
 * @category Pry
 * @package Form
 * @subpackage Form_Element
 * @version 1.0.7 
 * @author Olivier ROGER <oroger.fr>
 */
class Textarea extends Field
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
        $this->attrs['name'] = $nom;
    }

    /**
     * Défini le nombre de colonne
     *
     * @param int $val
     * @access public
     * @return Form_Element_Textarea
     */
    public function cols($val)
    {
        if (ctype_digit((string) $val) && $val > 0)
            $this->attrs['cols'] = $val;
        else
            unset($this->attrs['cols']);
        return $this;
    }

    /**
     * Défini le nombre de ligne
     *
     * @param int $val
     * @access public
     * @return Form_Element_Textarea
     */
    public function rows($val)
    {
        if (ctype_digit((string) $val) && $val > 0)
            $this->attrs['rows'] = $val;
        else
            unset($this->attrs['rows']);
        return $this;
    }

    /**
     * Ecrit l'objet
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        $css   = $this->cssClass();
        $label = '';
        if (!empty($this->label))
        {
            $label     = "\t" . '<label for="' . $this->attrs['id'] . '" class="' . $this->cssLabel . '">' . $this->label . '</label>' . "\n";
            if ($this->labelNewLine)
                $label.="\t" . '<br />' . "\n";
        }
        $attributs = $this->attrsToString();
        //Posted value ou value par défaut
        $value     = $this->form->getPostedvalue($this->attrs['name']);
        if (empty($value))
            $value     = $this->value;

        $error = '';
        if (!is_null($this->errorMsg))
        {
            $error = '<span class="' . $this->errorClass . '">' . $this->errorMsg . '</span><br />';
        }

        $field = "\t" . '<textarea ' . $css . ' ' . $attributs . '>' . htmlspecialchars($value) . '</textarea>' . "\n";
        if (!empty($this->info))
            $field.="\t" . '<img src="' . $this->imgInfo . '" id="' . $this->attrs['name'] . '_tooltip" class="form_tooltip" title="' . $this->info . '" alt="" style="cursor:help;" />';
        if ($this->fieldNewLine)
            $field.='<br />';
        return $label . $field . $error;
    }

}

?>