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

namespace Pry\File\Decorator;

use Pry\File\Util;

/**
 * Decorator permettant de filtrer les éléments listés
 * @category Pry
 * @package File
 * @subpackage File_Decorator
 * @version 1.0.0
 * @author Olivier ROGER <oroger.fr>
 */
class Filter extends \FilterIterator
{

    /**
     * Liste des extensions à autoriser. Toutes si vide
     * @var array
     */
    private $extensions = array();

    /**
     * Vérifie qu'un élément peut être listé
     * @return boolean
     */
    public function accept()
    {
        if (empty($this->extensions))
            return true;
        else
        {
            if (in_array(Util::getExtension($this->current()), $this->extensions))
                return true;
        }
        return false;
    }

    /**
     * Défini une ou plusieurs extsions à autoriser
     * @param string|array $filter  
     */
    public function setExtension($filter)
    {
        if (!is_array($filter))
            $this->extensions[] = strtolower($filter);
        else
            $this->extensions   = array_merge($this->extensions, $filter);
    }

}

?>
