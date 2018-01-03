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

namespace Pry\Form;

/**
 *
 * @category Pry
 * @package Form
 * @abstract 
 * @version 1.0.5 
 * @author Olivier ROGER <oroger.fr>
 *       
 */
abstract class Input extends Field
{

    /**
     * Constructeur.
     *
     * @param string $nom
     * @param Form_Form $form
     * @access protected
     */
    protected function __construct($nom, $form)
    {
        parent::__construct($nom, $form);
        $this->attrs['name'] = $nom;
    }

    /**
     * Défini un attribut de l'élément
     *
     * @param string $nom
     * @param string $valeur
     */
    public function setAttributes($nom, $valeur)
    {
        if (!isset($this->attrs[$nom]))
            $this->attrs[$nom] = $valeur;
        return $this;
    }

}