<?php

/**
 * Pry Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 */

namespace Pry\Form\Element;

/**
 * Element Select. 
 * @category Pry
 * @package Form
 * @subpackage Form_Element
 * @version 1.1.0 
 * @author Olivier ROGER <oroger.fr>
 */
class Select extends Multi
{

    /**
     * Stockage du nom en cas de select multiple
     *
     * @var string
     * @access private
     */
    private $name;

    /**
     * Etat de l'activation du plugin jQuery
     * @var boolean
     */
    private $jQuerymultiple;

    /**
     * Tableau contenant les traductions du plugin
     * @var array
     */
    private $jQueryTexts;

    /**
     * Nombre de résultats sélectionnés à afficher par le plugin
     * @var int
     */
    private $jQuerySelectedList;

    /**
     * Constructeur
     *
     * @param string $nom
     * @param Form_Form $form
     */
    public function __construct($nom, $form)
    {
        parent::__construct($nom, $form);
        $this->name           = $nom;
        $this->jQuerymultiple = false;
        $this->jQueryTexts    = array('checkAll' => 'Tout sélectionner',
            'unCheckAll' => 'Tout déselectionner',
            'selected' => '# sélectionnée(s)',
            'noneSelected' => 'Choisir...');
        $this->jQuerySelectedList = 0;
    }

    /**
     * Défini si il sagit d'un select multiple ou non
     *
     * @param boolean $bool
     * @param int $size Taille du select
     * @access public
     * @return Form_Element_Select
     */
    public function multiple($bool = true, $size = 5)
    {
        if ($bool)
        {
            $this->attrs['multiple'] = 'multiple';
            $this->attrs['name']     = $this->attrs['name'] . '[]';
            $this->attrs['size']     = $size;
        }
        else
        {
            unset($this->attrs['multiple']);
            unset($this->attrs['size']);
        }

        return $this;
    }

    /**
     * Active ou non le plugin jQuery pour les listes multiple
     * @param boolean $bool
     * @return Form_Element_Select
     */
    public function enableJQPlugin($bool = true)
    {
        $this->jQuerymultiple = $bool;
        return $this;
    }

    /**
     * Traduit les éléments textuels du plugin Jquery
     * Le tableau doit contenir les clés checkAll,unCheckAll,selected,noneSelected
     * @param array $texts
     * @return Form_Element_Select
     */
    public function translateJQPlugin(array $texts)
    {
        if (is_array($texts))
            array_merge($this->jQueryTexts, $texts);
        else
            throw new InvalidArgumentException('La traduction doit se faire via un array');

        return $this;
    }

    /**
     * Défini le nombre de choix sélectionner à afficher.
     * Par défaut n'afifchue que le nombre sélectionner et non le valeur.
     * @param int $num
     * @return Form_Element_Select
     */
    public function setJQSelectedList($num)
    {
        $this->jQuerySelectedList = intval($num);
        return $this;
    }

    /**
     * Ecriture de l'objet
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        $css     = $this->cssClass();
        $label   = '';
        $options = '';

        if (!empty($this->label))
        {
            $label     = "\t" . '<label for="' . $this->attrs['id'] . '" class="' . $this->cssLabel . '">' . $this->label . '</label>' . "\n";
            if ($this->labelNewLine)
                $label.="\t" . '<br />' . "\n";
        }
        $attributs = $this->attrsToString();
        //Posted value ou value par défaut
        $value     = $this->form->getPostedvalue($this->name);
        if ($value == '')
            $value     = $this->value;

        foreach ($this->choix as $itemHtml => $itemAffichage) {
            if (is_array($itemAffichage)) // Cas d'un optgroup
            {
                $options.="\t\t" . '<optgroup label="' . $itemHtml . '">' . "\n";
                foreach ($itemAffichage as $cle => $valeur) {
                    if (isset($this->attrs['multiple']) && is_array($value))
                    {
                        if (in_array($cle, $value))
                            $selected = 'selected="selected"';
                        else
                            $selected = '';
                    }
                    else
                    {
                        if ($value == $cle)
                            $selected = 'selected="selected"';
                        else
                            $selected = '';
                    }

                    $options .="\t\t" . '<option value="' . htmlspecialchars($cle) . '" ' . $selected . '>' . $valeur . '</option>' . "\n";
                }
                $options.="\t\t" . '</optgroup>' . "\n";
            }
            else
            {
                if (isset($this->attrs['multiple']) && is_array($value))
                {
                    if (in_array($itemHtml, $value))
                        $selected = 'selected="selected"';
                    else
                        $selected = '';
                }
                else
                {
                    if ($value == $itemHtml)
                        $selected = 'selected="selected"';
                    else
                        $selected = '';
                }

                $options .="\t\t" . '<option value="' . htmlspecialchars($itemHtml) . '" ' . $selected . '>' . $itemAffichage . '</option>' . "\n";
            }
        }
        $error = '';
        if (!is_null($this->errorMsg))
        {
            $error = '<span class="' . $this->errorClass . '">' . $this->errorMsg . '</span><br />';
        }
        $field = "\t" . '<select ' . $attributs . ' ' . $css . ' >' . "\n" . $options . "\n\t" . '</select>' . "\n";
        if (!empty($this->info))
            $field.="\t" . '<img src="' . $this->imgInfo . '" id="' . $this->attrs['name'] . '_tooltip" class="form_tooltip" title="' . $this->info . '" alt="" style="cursor:help;" />' . "\n";
        if ($this->fieldNewLine)
            $field.="\t" . '<br />' . "\n";

        if ($this->jQuerymultiple)
        {
            $this->form->javascript .= '$(\'#' . $this->attrs['id'] . '\').multiSelect({selectedList:' . $this->jQuerySelectedList . ',
														checkAllText:\'' . $this->jQueryTexts['checkAll'] . '\',
														unCheckAllText:\'' . $this->jQueryTexts['unCheckAll'] . '\',
														selectedText:\'' . $this->jQueryTexts['selected'] . '\',
														noneSelected:\'' . $this->jQueryTexts['noneSelected'] . '\'});';
        }
        return $label . $field . $error;
    }

}