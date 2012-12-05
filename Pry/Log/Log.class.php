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

namespace Pry\Log;

/**
 *
 * Classe de gestion des Logs
 * @version 2.0.1
 * @author Olivier ROGER <oroger.fr>
 * @category Pry
 * @package Log
 */
class Log
{

    /**
     * Driver d'ecriture du log
     *
     * @var Log_Writer_Abstract
     */
    private $writer;
    private $severity = array();

    /**
     * Constructeur
     * @param Log_Writer_Abstract $writer
     */
    public function __construct($writer)
    {
        $this->writer   = $writer;
        $this->severity = array('emergency' => 0, 'alert' => 1, 'critical' => 2, 'error' => 3, 'warn' => 4, 'notice' => 5, 'info' => 6, 'debug' => 7);
    }

    /**
     * Surcharge de l'appel de fonction permettant de 
     * logguer des message avec les niveau en guise de methode.
     * Exemple $obj->info($message);
     *
     * @param string $method
     * @param array $param
     */
    public function __call($method, $param)
    {
        $method = strtolower($method);
        if (key_exists($method, $this->severity))
            $this->write(array_shift($param), $this->severity[$method]);
        else
            throw new \BadMethodCallException($method . ' est un niveau inconnu. Vous ne pouvez pas utiliser ' . $method . '()');
    }

    /**
     * Ecriture du log
     *
     * @param string $message
     * @param int $niveau
     */
    public function write($message, $niveau = 6)
    {
        $this->writer->write($message, $niveau);
    }

}