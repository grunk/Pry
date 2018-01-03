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
 * Element Autocompleter. Permet de définir un champs texte avec une auto complétion.
 * @category Pry
 * @package Form
 * @subpackage Form_Element
 * @version 1.5.1 
 * @author Olivier ROGER <oroger.fr>
 *       
 *
 */
class AutoCompleter extends Text
{

    /**
     * Class css du div d'autocompletion
     *
     * @var string
     * @access private
     */
    private $autoCompleteCss;

    /**
     * Url du fichier fournissant les données
     *
     * @var string
     * @access private
     */
    private $url;

    /**
     * Nom du paramèter envoyé en post
     *
     * @var string
     * @access private
     */
    private $paramName;

    /**
     * Nombre de caractère mini avant déclenchement de l'autocompletion
     *
     * @var int
     * @access private
     */
    private $minChar;

    /**
     * Nombre de résultat maximum retourné
     * 
     * @var int
     * @access private
     */
    private $max;

    /**
     * Fréquence de rafraichissement de l'input en ms
     *
     * @var float
     * @access private
     */
    private $frequency;

    /**
     * Paramètres additionnels
     *
     * @var array
     */
    private $params;

    /**
     * Constructeur.
     *
     * @param string $nom
     * @param Form_Form $form
     */
    public function __construct($nom, $form)
    {
        parent::__construct($nom, $form);
        $this->autoCompleteCss = 'autocomplete';
        $this->minChar         = 2;
        $this->max             = 25;
        $this->frequency       = 400;
        $this->params          = '{';
    }

    /**
     * Défini la class css pour le div autocompletion
     *
     * @param string $css
     * @return Form_Element_AutoCompleter
     */
    public function setAutoCompleteClass($css)
    {
        $this->autoCompleteCss = $css;
        return $this;
    }

    /**
     * Défini l'adresse du fichier
     *
     * @param string $url
     * @return Form_Element_AutoCompleter
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Défini le nom du paramètre envoyé
     *
     * @param string $name
     * @return Form_Element_AutoCompleter
     */
    public function setParamName($name)
    {
        $this->paramName = $name;
        return $this;
    }

    /**
     * Défini le nombre de caractère nécessaire
     *
     * @param int $char
     * @return Form_Element_AutoCompleter
     */
    public function minCharNeeded($char)
    {
        $this->minChar = $char;
        return $this;
    }

    /**
     * Taux de rafraichissement de l'input en sec
     *
     * @param float $frq
     * @return Form_Element_AutoCompleter
     */
    public function refreshFrequency($frq)
    {
        $this->frequency = $frq;
        return $this;
    }

    public function setMaxResult($max)
    {
        $this->max = intval($max);
    }

    /**
     * Ajoute des paramètres
     *
     * @param array $tab
     * @return Form_Element_AutoCompleter
     */
    public function addParams($tab)
    {
        foreach ($tab as $key => $value) {
            $this->params .=$key . ':' . $value . ',';
        }
        $this->params = substr($this->params, 0, strlen($this->params) - 1);
        return $this;
    }

    /**
     * Ecrit l'objet
     *
     * @return string
     */
    public function __toString()
    {
        $this->params.='}';
        $field.=parent::__toString();
        //$field.="\t".'</p><div id="'.$this->attrs['id'].'_autocomplete" class="'.$this->autoCompleteCss.'"></div><p>'."\n";
        $this->form->javascript.='$("#' . $this->attrs['id'] . '").autocomplete("{source:' . $this->url . '",delay:' . $this->frequency . ',minLength:' . $this->minChar . '});';
        return $field;
    }

}