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

namespace Pry\Config;

/**
 * Classe générique pour la configuration. Basée sur Zend_Config
 * @package Config
 * @version 1.0.0
 * @author Olivier ROGER <oroger.fr>
 * @copyright  2007-2012 Prynel
 * @see Zend_Config
 *
 */
class Config implements \Countable, \Iterator
{

    /**
     * Index d'itération
     * @var int 
     */
    protected $index = 0;

    /**
     * Nombre d'élément dans le tableau de config
     * @var int 
     */
    protected $count;

    /**
     * Flag pour éviter de sauter un élément après un unset
     * @var boolean 
     */
    private $skipNextIteration = false;

    /**
     * Tableau des données de config
     * @var array 
     */
    protected $datas = array();

    /**
     * Chaine contenant une erreur
     * @var string 
     */
    protected $errorStr = null;

    /**
     * This is used to track section inheritance. The keys are names of sections that
     * extend other sections, and the values are the extended sections.
     *
     * @var array
     */
    protected $_extends = array();

    public function __construct(array $array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value))
            {
                $this->datas[$key] = new self($value);
            }
            else
            {
                $this->datas[$key] = $value;
            }

            $this->count++;
        }
    }

    /**
     * Gère les erreurs pouvant survenir avec parse_ini_file
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline 
     */
    protected function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if ($this->errorStr === null)
        {
            $this->errorStr = $errno . ':' . $errstr . ' on ' . $errfile . ' at ' . $errline;
        }
        else
        {
            $this->errorStr .= (PHP_EOL . $errstr);
        }
    }

    /**
     * Throws an exception if $extendingSection may not extend $extendedSection,
     * and tracks the section extension if it is valid.
     *
     * @param  string $extendingSection
     * @param  string $extendedSection
     * @throws Zend_Config_Exception
     * @return void
     */
    protected function checkForCircularInheritance($extendingSection, $extendedSection)
    {
        // detect circular section inheritance
        $extendedSectionCurrent = $extendedSection;
        while (array_key_exists($extendedSectionCurrent, $this->_extends)) {
            if ($this->_extends[$extendedSectionCurrent] == $extendingSection)
            {
                throw new \Exception('Illegal circular inheritance detected');
            }
            $extendedSectionCurrent            = $this->_extends[$extendedSectionCurrent];
        }
        // remember that this section extends another section
        $this->_extends[$extendingSection] = $extendedSection;
    }

    /**
     * Convertit l'objet de config en tableau associatif
     * @return array 
     */
    public function toArray()
    {
        $array = array();
        foreach ($this->datas as $key => $value) {
            if ($value instanceof Config_Config)
                $array[$key] = $value->toArray();
            else
                $array[$key] = $value;
        }
        return $array;
    }

    /**
     * Merge two arrays recursively, overwriting keys of the same name
     * in $firstArray with the value in $secondArray.
     *
     * @param  mixed $firstArray  First array
     * @param  mixed $secondArray Second array to merge into first array
     * @return array
     */
    protected function _arrayMergeRecursive($firstArray, $secondArray)
    {
        if (is_array($firstArray) && is_array($secondArray))
        {
            foreach ($secondArray as $key => $value) {
                if (isset($firstArray[$key]))
                {
                    $firstArray[$key] = $this->_arrayMergeRecursive($firstArray[$key], $value);
                }
                else
                {
                    if ($key === 0)
                    {
                        $firstArray = array(0 => $this->_arrayMergeRecursive($firstArray, $value));
                    }
                    else
                    {
                        $firstArray[$key] = $value;
                    }
                }
            }
        }
        else
        {
            $firstArray = $secondArray;
        }

        return $firstArray;
    }

    private function get($name, $default = null)
    {
        $result = $default;
        if (array_key_exists($name, $this->datas))
        {
            $result = $this->datas[$name];
        }
        return $result;
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __isset($name)
    {
        return isset($this->datas[$name]);
    }

    public function __unset($name)
    {
        unset($this->datas[$name]);
        $this->count             = count($this->datas);
        $this->skipNextIteration = true;
    }

    public function count()
    {
        return count($this->datas);
    }

    public function current()
    {
        $this->skipNextIteration = false;
        return current($this->datas);
    }

    public function key()
    {
        return key($this->datas);
    }

    public function next()
    {
        if ($this->skipNextIteration)
        {
            $this->skipNextIteration = false;
            return;
        }
        next($this->datas);
        $this->index++;
    }

    public function rewind()
    {
        $this->_skipNextIteration = false;
        reset($this->datas);
        $this->index              = 0;
    }

    public function valid()
    {
        return $this->index < count($this->datas);
    }

}