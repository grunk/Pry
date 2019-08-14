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
namespace Pry\Util;
use InvalidArgumentException;

/**
 * CommandLineBuilder.
 * Let you create some commandline string
 *
 * @author Olivier ROGER <oroger.fr>
 */
class CommandLineBuilder
{
    private const SPACE = ' ';
    /**
     * Name of the command
     * @var string 
     */
    protected $command;
    
    /**
     * Parameters list
     * @var array 
     */
    protected $params;
    
    /**
     * Options list with values
     * @var array 
     */
    protected $options;
    
    /**
     * Long options with values
     * @var array 
     */
    protected $longOptions;
    
    /**
     * Char to prepend to options.
     * default to : -
     * @var string 
     */
    protected $optionChar = '-';
    
    /**
     * Char to prepend to long options.
     * default to : --
     * @var string 
     */
    protected $longOptionChar = '--';
    
    public function __construct()
    {
        $this->clear();
    }
    
    /**
     * Reset command line
     */
    public function clear() : void
    {
        $this->command      = '';
        $this->params       = array();
        $this->options      = array();
        $this->longOptions  = array();
    }
    
    /**
     * Define option prefix
     * @param string $char
     */
    public function setOptionChar(string $char) : void
    {
        $this->optionChar = $char;
    }
    
    /**
     * Define long option prefix
     * @param string $longChar
     */
    public function setLongOptionChar(string $longChar) : void
    {
        $this->longOptionChar = $longChar;
    }
    
    /**
     * Define command name
     * @param string $cmd
     */
    public function setCommand(string $cmd) : void
    {
        $this->command = $cmd;
    }
    
    /**
     * Add a new parameter to the command
     * @param string $param
     * @throws InvalidArgumentException if parameter already exists
     */
    public function addParameter(string $param) : void
    {
        if( in_array($param, $this->params) )
            throw new InvalidArgumentException('This argument already exist');
                
        $this->params[] = $param; 
    }
    
    /**
     * Add an option an its optionnal value
     * @param string $name Option name
     * @param string $value (optionnal) option value
     */
    public function addOption(string $name, string $value='') : void
    {
        if(!empty($name))
            $this->options[$name] = $value;
    }

    /**
     * Add a long option an its optionnal value
     * @param string $name Option name
     * @param string $value (optionnal) option value
     */
    public function addLongOption(string $name, string $value='') : void
    {
        if(!empty($name))
            $this->longOptions[$name] = $value;
    }
    
    /**
     * Build command line and returns it
     * @param string $order Order of element in the commandline. P for parameter
     * O for option and L for long options. Default to POL
     * @return string Command line
     */
    public function get(string $order = 'POL') : string
    {
        $command = $this->command;
        
        foreach(str_split($order) as $letter)
        {
            if($letter == 'P')
                $command .= $this->getParams();
            
            if($letter == 'O')
                $command .= $this->getOptions();
            
            if($letter == 'L')
                $command .= $this->getLongOptions();
        }
        
        return $command;
    }
    
    private function getParams() : string
    {
        $p = '';
        foreach($this->params as $param)
            $p .= CommandLineBuilder::SPACE.$param;
        
        return $p;
    }
    
    private function getOptions() : string
    {
        $o = '';
        foreach ($this->options as $option => $value)
        {
            $o .= CommandLineBuilder::SPACE.$this->optionChar.$option;
            if(!empty($value))
                $o .= CommandLineBuilder::SPACE.$value;
        }
        
        return $o;
    }
    
    private function getLongOptions() : string
    {
        $lo = '';
        foreach($this->longOptions as $option => $value)
        {
            $lo .= CommandLineBuilder::SPACE.$this->longOptionChar.$option;
            if(!empty($value))
                $lo .= '='.$value;
        }
        
        return $lo;
    }
    
    
    public function __toString() : string
    {
        return $this->get();
    }
}