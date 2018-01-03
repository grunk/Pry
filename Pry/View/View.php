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

namespace Pry\View;

/**
 * Classe pour une gestion simple des vues
 * @category Pry
 * @package View
 * @version 1.0
 * @author Olivier ROGER <roger.olivier@gmail.com>
 * @copyright  2007-2012 Prynel
 *
 */
class View
{
    /** Ne pas echapper la variable */

    const NO_ESCAPE = 1;

    /**
     * Ensemble des variables utilisable dans la vue
     * @var array
     */
    protected $variables = array();

    /**
     * Dossier où sont situer les vues
     * @var string 
     */
    protected $viewFolder;

    /**
     * Fichier vue à charger
     * @var string 
     */
    protected $view;

    public function __construct()
    {
        
    }

    /**
     * Défini le dossier de base contenant les vues
     * @param string $path
     * @throws InvalidArgumentException Si le dossier n'existe pas
     */
    public function setViewBase($path)
    {
        if (!file_exists($path))
            throw new \InvalidArgumentException('Can\'t find ' . $path . 'folder');
        $this->viewFolder = $path;
    }

    /**
     * Défini la vue à charger
     * @param string $filePath Chemin dans le dossier des vue
     * @throws BadMethodCallException Si le dossier de base des vues n'est pas défini
     * @throws InvalidArgumentException Si la vue n'existe pas
     */
    public function load($filePath)
    {
        if (empty($this->viewFolder))
            throw new \BadMethodCallException('No view base defined.You should call setViewBase First.');

        $this->view = $filePath;

        if (!file_exists($this->viewFolder . $this->view))
            throw new \InvalidArgumentException('Can\'t find the ' . $this->view . 'view');
    }

    /**
     * Défini une variable de vue. La variable sera ensuite utilisable dans la vue via sa clé
     * @param string $key
     * @param mixed $value
     * @param int $option
     */
    public function set($key, $value, $option = null)
    {
        if ($option === self::NO_ESCAPE)
            $this->variables[$key] = $value;
        else
            $this->variables[$key] = htmlspecialchars($value, ENT_QUOTES);
    }

    /**
     * Récupère une variable de vue
     * @param type $key
     * @return type
     * @throws InvalidArgumentException Si la clé n'existe pas
     */
    public function get($key)
    {
        if (!array_key_exists($key, $this->variables))
            throw new \InvalidArgumentException('No value for this key');

        return $this->variables[$key];
    }

    /**
     * Raccourcis pour définir une variable de vue.
     * Cette variable sera forcément échapée à l'affichage
     * @param type $name
     * @param type $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value, null);
    }

    /**
     * Raccourcis pour récupérer une variable de vue
     * @param type $name
     */
    public function __get($name)
    {
        $this->get($name);
    }

    /**
     * Affiche la vue
     * @throws BadMethodCallException Si la vue n'as pas été chargée
     */
    public function render()
    {
        if (empty($this->viewFolder))
            throw new \BadMethodCallException('PLease call load() before render()');
        extract($this->variables);
        include_once $this->viewFolder . $this->view;
    }

}
