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
 *
 * @category Pry
 * @package Form
 * @subpackage Form_Element
 * @version 1.2.0
 * @author Olivier ROGER <oroger.fr>
 */
class Slider extends Hidden
{

    /**
     * Limite haute du slider
     *
     * @var int
     */
    private $maxRange;

    /**
     * Limite basse du slider
     *
     * @var int
     */
    private $minRange;

    /**
     * Pas du slider
     * @var int
     */
    private $step;

    /**
     * Afficher la valeur du slider
     * @var boolean
     */
    private $displayValue;
    private $unit;

    /**
     * Constructeur
     *
     * @param string $nom
     * @param Form_Form $form
     */
    public function __construct($nom, $form)
    {
        parent::__construct($nom, $form);
        $this->nom          = $nom;
        $this->maxRange     = 100;
        $this->minRange     = 0;
        $this->start        = 0;
        $this->step         = 1;
        $this->displayValue = false;
    }

    /**
     * Défini le range du slider
     *
     * @param int $min
     * @param int $max
     * @return Form_Element_Slider
     */
    public function setRange($min, $max)
    {
        $this->maxRange = $max;
        $this->minRange = $min;
        return $this;
    }

    /**
     * Défini le pas du slider
     * @param int $val
     * @return Form_Element_Slider
     */
    public function step($val)
    {
        $this->step = $val;
        return $this;
    }

    /**
     * Valeur de départ
     * @param mixed $val
     */
    public function setStartValue($val)
    {
        $this->start = $val;
        return $this;
    }

    /**
     * Affiche ou non la valeur courante du slider
     * @param boolean $bool
     * @param string $unit Unité à afficher à coté de la valeur
     * @return Form_Element_Slider
     */
    public function displayValue($bool, $unit = '%')
    {
        $this->displayValue = $bool;
        $this->unit         = $unit;
        return $this;
    }

    public function __toString()
    {
        $css  = $this->cssClass();
        $html = '';
        if (!empty($this->label))
        {
            $html .= "\t" . '<label for="' . $this->attrs['id'] . '" class="' . $this->cssLabel . '">' . $this->label . '</label>' . "\n";
            if ($this->labelNewLine)
                $html.="\t" . '<br />' . "\n";
        }

        $value = $this->form->getPostedvalue($this->attrs['name']);
        if (empty($value))
            $value = $this->start;

        $html .= '<div id="prynslide_' . $this->nom . '"></div>';
        if ($this->displayValue)
            $html .='<div id="prynslidedisp_' . $this->nom . '">' . $value . $this->unit . '</div>';

        $this->form->javascript.='$("#prynslide_' . $this->nom . '").slider({
            max : ' . $this->maxRange . ',
            min : ' . $this->minRange . ',
            step : ' . $this->step . ',
            value : ' . $value . ',
            change : function(event,ui){$("#' . $this->attrs['id'] . '").val(ui.value)}';

        if ($this->displayValue)
            $this->form->javascript.=',slide:function(event,ui){$("#prynslidedisp_' . $this->nom . '").html(ui.value+"%")}';

        $this->form->javascript.='});';
        $html.= parent::__toString();
        return $html;
    }

}