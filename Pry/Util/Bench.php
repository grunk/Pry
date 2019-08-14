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
 * Classe bench. Benchmark script performance.
 * 
 * @category Pry
 * @package Util
 * @author Olivier ROGER <oroger.fr>
 * 
 */
class Bench
{

    /**
     * Start time.<br />Default value : 0
     * @var int
     * @access private
     */
    private $start;

    /**
     * Results array.
     * @var array
     * @access public
     */
    private $resultat;


    public function __construct()
    {
        $this->start    = 0;
        $this->resultat = array();
    }

    /**
     * Start the benchmarking
     * @return float starting µtime
     */
    public function start(): float
    {
        $this->start = $this->get_micro();
        return $this->start;
    }

    /**
     * Add an intermediate measure
     *
     * @param string $nom Name of the measure
     * @return float Delay between start and the measure
     */
    public function add_flag(string $nom) : float
    {
        $top                  = $this->get_micro() - $this->start;
        $this->resultat[$nom] = $top;
        
        return $top;
    }

    /**
     * Stop the benchmark
     * @return float µtime a the end
     */
    public function stop() : float
    {
        $end                     = $this->get_micro() ;
        $this->resultat['total'] = $end - $this->start;
        return $end;
    }

    /**
     * Give a array of result including all intermediates measurement
     *
     * @return array An array with a "total" key and each intermediate measure name.
     */
    public function result() : array
    {
        return $this->resultat;
    }

    /**
     * Get current time in milliseconds
     * @access private
     * @return Float time in millisecond
     */
    private function get_micro() : float
    {
        $temps = microtime();
        $temps = explode(' ', $temps);
        $temps = $temps[1] + $temps[0];
        return (float) $temps;
    }

}