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
 * Element DatePicker. Champs date avec selection JS de la date.
 * 
 * @category Pry
 * @package Form
 * @subpackage Form_Element
 * @version 1.5.0 
 * @author Olivier ROGER <oroger.fr>
 */
class DatePicker extends Date
{

    private $icon;
    private $timepicker;

    /**
     * Constructeur. Par défaut pub/struct/picto/clock.png et pas de séléction de l'heure
     *
     * @param string $nom
     * @param Form_Form $form
     * @access public
     */
    public function __construct($nom, $form)
    {
        parent::__construct($nom, $form);
        $this->attrs['id'] = $nom;
        $this->icon        = 'pub/struct/picto/clock.png';
        $this->timePicker  = false;
    }

    /**
     * Défini l'icone illustrant la date
     *
     * @param string $icon Chemin vers l'icone
     * @access public
     * @return Form_Element_DatePicker
     */
    public function icon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    public function setTimepicker($bool)
    {
        $this->timepicker = $bool;
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

        $field            = parent::__toString();
        $timepickerOption = '';
        if ($this->timepicker)
            $timepickerOption = 'showTime:true,
			constrainInput:true,
			time24h:true';

        $this->form->javascript .= '$(function() {
		$.datepicker.setDefaults($.extend({
			showMonthAfterYear: false,
			showOn:\'both\',
			duration:\'\',
			buttonImage:\'' . $this->icon . '\',
			buttonImageOnly:true}';
        if ($this->format == 'fr')
            $this->form->javascript.=',$.datepicker.regional[\'fr\']';
        $this->form->javascript.='
		));
		$("#' . $this->attrs['id'] . '").datepicker({' . $timepickerOption . '});
		});
		';
        $error = '';
        return $field;
    }

}

?>