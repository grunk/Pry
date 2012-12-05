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
use Pry\Form\Error;

/**
 * Input de type Text
 * @category Pry
 * @package Form
 * @subpackage Form_Element
 * @version 1.0.6 
 * @author Olivier ROGER <oroger.fr>
 */
class Text extends Input
{

    /**
     * Taille minimal
     *
     * @var int
     * @access protected
     */
    protected $minLength;

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
            $this->setAttributes('type', 'text');
        $this->minLength = 0;
    }

    /**
     * Défini une taille minimal pour le contenu
     *
     * @param int $taille
     * @access public
     * @return Form_Element_Text
     */
    public function minLength($taille)
    {
        if (ctype_digit((string) $taille) && $taille >= 0)
            $this->minLength = $taille;
        return $this;
    }

    /**
     * Vérifie que la valeur est valide
     *
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (parent::isValid($value))
        {
            if (!$this->required && empty($value))
                return true;
            if (!$this->minLength == 0)
                $carac = $this->minLength - 1;
            else
                return true;

            if (isset($value[$carac]))
                return true;
            else
                $this->errorMsg = Error::TOOSHORT . $this->minLength;
        }
        return false;
    }

    /**
     * Ecris l'élément avec toutes ses options
     *
     * @param array $array
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

        $field = "\t" . '<input ' . $css . ' value="' . htmlspecialchars($value) . '" ' . $attributs . ' />' . "\n";

        $error = '';
        if (!is_null($this->errorMsg))
        {
            $error = '<span class="' . $this->errorClass . '">' . $this->errorMsg . '</span><br />';
        }
        if (!empty($this->info))
            $field.="\t" . '<img src="' . $this->imgInfo . '" id="' . $this->attrs['name'] . '_tooltip" class="form_tooltip" title="' . $this->info . '" alt="" style="cursor:help;" />';
        if ($this->fieldNewLine)
            $field.="\t" . '<br />' . "\n";
        return $label . $field . $error;
    }

}

?>