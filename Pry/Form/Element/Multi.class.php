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
 * Représentation des élément a choix multiple (select, radio)
 * @category Pry
 * @package Form
 * @subpackage Form_Element
 * @abstract
 * @version 1.1.0 
 * @author Olivier ROGER <oroger.fr>
 */
abstract class Multi extends Field
{

    /**
     * Liste des choix possible
     *
     * @var array
     * @access protected
     */
    protected $choix;

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
     * Enregistre les choix possibles
     *
     * @param array $choix La clé = valeur html du code html , la valeur = valeur afficher à l'utilisateur
     * @access public
     * @return Form_Element_Multi
     */
    public function choices(array $choix)
    {
        if (is_array($choix))
            $this->choix = $choix;
        else
            throw new \InvalidArgumentException('Le/les choix doivent être un array');
        return $this;
    }

    public function setAttributes($nom, $valeur)
    {
        if (!isset($this->attrs[$nom]))
            $this->attrs[$nom] = $valeur;
    }

}

?>