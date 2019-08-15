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

use BadMethodCallException;
use InvalidArgumentException;

/**
 * Simple view template class
 * @category Pry
 * @author Olivier ROGER <roger.olivier@gmail.com>
 */
class View
{
    /** Do not escape variable */
    public const NO_ESCAPE = 1;

    /**
     * All variables available in view
     * @var array
     */
    protected $variables = [];

    /**
     * Folder where the views are
     * @var string 
     */
    protected $viewFolder;

    /**
     * File to load
     * @var string 
     */
    protected $view;

    public function __construct(){}

    /**
     * Set the base folder containing the views
     * @param string $path
     * @throws InvalidArgumentException If folder does not exists
     */
    public function setViewBase($path) : void
    {
        if (!file_exists($path))
            throw new InvalidArgumentException('Can\'t find ' . $path . 'folder');
        $this->viewFolder = $path;
    }

    /**
     * Set the view to load
     * @param string $filePath Path of the view
     * @throws BadMethodCallException If the base folder does not exists
     * @throws InvalidArgumentException If the view does not exists
     */
    public function load($filePath) : void
    {
        if (empty($this->viewFolder))
            throw new BadMethodCallException('No view base defined.You should call setViewBase First.');

        $this->view = $filePath;

        if (!file_exists($this->viewFolder . $this->view))
            throw new InvalidArgumentException('Can\'t find the ' . $this->view . 'view');
    }

    /**
     * Set a view variable. The variable will then be available in the view
     * @param string $key Key to use the variable in the view
     * @param mixed $value Value
     * @param int $option Set to View::NO_ESCAPE to avoid escaping value.
     */
    public function set(string $key, $value, ?int $option = null) : void
    {
        if ($option === self::NO_ESCAPE)
            $this->variables[$key] = $value;
        else
            $this->variables[$key] = htmlspecialchars($value, ENT_QUOTES);
    }

    /**
     * Get a view variable
     * @param string $key
     * @return mixed
     * @throws InvalidArgumentException Si la clé n'existe pas
     */
    public function get(string $key)
    {
        if (!array_key_exists($key, $this->variables))
            throw new InvalidArgumentException('No value for this key');

        return $this->variables[$key];
    }

    /**
     * Shortcut to define view variable
     * Will automatically be escaped
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value) : void
    {
        $this->set($name, $value);
    }

    /**
     * Shortcut to get a variable
     * @param string $name
     */
    public function __get(string $name)
    {
        $this->get($name);
    }

    /**
     * Render the view
     * @throws BadMethodCallException If the view has not be loaded
     */
    public function render() : void
    {
        if (empty($this->viewFolder))
            throw new BadMethodCallException('PLease call load() before render()');
        extract($this->variables);
        include_once $this->viewFolder . $this->view;
    }

}
