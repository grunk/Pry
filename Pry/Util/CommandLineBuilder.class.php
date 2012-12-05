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
/**
 * Classe CommandLineBuilder. 
 * Permet de créer simplement des lignes de commandes
 * 
 * @category Pry
 * @package Util
 * @version 1.0.0
 * @author Olivier ROGER <oroger.fr>
 *
 */

define('SPACE',' ');
class CommandLineBuilder
{

    /**
     * Nom de la commande à utiliser
     * @var string 
     */
    protected $command;
    
    /**
     * Liste des paramètres
     * @var array 
     */
    protected $params;
    
    /**
     * Liste des options et de leur valeur
     * @var array 
     */
    protected $options;
    
    /**
     * Liste des options longue et de leur valeur
     * @var array 
     */
    protected $longOptions;
    
    /**
     * Caractère à placer devant les options.
     * Par défaut : -
     * @var string 
     */
    protected $optionChar = '-';
    
    /**
     * Caractère à placer devant les options longues.
     * Par défaut : --
     * @var string 
     */
    protected $longOptionChar = '--';
    
    public function __construct()
    {
        $this->clear();
    }
    
    /**
     * Réinitialise la ligne de commande
     */
    public function clear()
    {
        $this->command      = '';
        $this->params       = array();
        $this->options      = array();
        $this->longOptions  = array();
    }
    
    /**
     * Défini le préfixe des options
     * @param string $char
     */
    public function setOptionChar($char)
    {
        $this->optionChar = $char;
    }
    
    /**
     * Défini le préfixe des options longues
     * @param string $longChar
     */
    public function setLongOptionChar($longChar)
    {
        $this->longOptionChar = $longChar;
    }
    
    /**
     * Défini la commande à utiliser
     * @param string $cmd
     */
    public function setCommand($cmd)
    {
        $this->command = $cmd;
    }
    
    /**
     * Ajoute un nouveau paramètre à la commande
     * @param string $param
     * @throws InvalidArgumentException si le paramètre existe déjà
     */
    public function addParameter($param)
    {
        if( in_array($param, $this->params) )
            throw new \InvalidArgumentException('This argument already exist');
                
        $this->params[] = $param; 
    }
    
    /**
     * Ajoute une option et sa valeur éventuelle
     * @param string $name nom de l'option
     * @param string $value (facultatif) Valeur de l'option
     */
    public function addOption($name,$value='')
    {
        if(!empty($name))
            $this->options[$name] = $value;
    }
    
    /**
     * Ajoute une option longue et sa valeur éventuelle
     * @param string $name Nom de l'option longue
     * @param string $value Nom de la valeur
     */
    public function addLongOption($name,$value='')
    {
        if(!empty($name))
            $this->longOptions[$name] = $value;
    }
    
    /**
     * Construit la ligne de commande et la retourne
     * @return string Ligne de commande
     */
    public function get()
    {
        $command = $this->command;
        
        foreach($this->params as $param)
            $command .= SPACE.$param;
        
        foreach ($this->options as $option => $value)
        {
            $command .= SPACE.$this->optionChar.$option;
            if(!empty($value))
                $command .= SPACE.$value;
        }
        
        foreach($this->longOptions as $option => $value)
        {
            $command .= SPACE.$this->longOptionChar.$option;
            if(!empty($value))
                $command .= '='.$value;
        }
        
        return $command;
    }
    
    public function __toString()
    {
        return $this->get();
    }
}