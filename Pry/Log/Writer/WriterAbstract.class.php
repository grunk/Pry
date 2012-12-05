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

namespace Pry\Log\Writer;

/**
 *
 * @package Log
 * @subpackage Log_Writer
 * @abstract 
 * @version 1.1.1
 * @author Olivier ROGER <oroger.fr>
 *       
 */
abstract class WriterAbstract
{

    const EMERGENCY = 0;
    const ALERT     = 1;
    const CRITICAL  = 2;
    const ERROR     = 3;
    const WARN      = 4;
    const NOTICE    = 5;
    const INFO      = 6;
    const DEBUG     = 7;
    const MODE_MINI = 0; // Uniquement le message
    const MODE_FULL = 1; // Lvl,date,message
    const DAILY     = 1;
    const MONTHLY   = 2;

    /**
     * Durée des fichiers logs
     *
     * @var int
     * @access protected
     */
    protected $duree = self::MONTHLY;

    /**
     * Correspondance textuel des niveaux de sévérité
     * @var array
     */
    protected $txtSeverity = array('emergency', 'alert', 'critical', 'error', 'warn', 'notice', 'info', 'debug');

    /**
     * Type des messages
     * mini = juste le message , full = message + date + level
     * @var int
     * @access protected
     */
    protected $mode = self::MODE_FULL;

    /**
     * Ecriture du message
     *
     * @param string $message
     * @param int $level
     * @access public
     */
    public function write($message, $level = self::INFO)
    {
        $this->_write($message, $level);
    }

    /**
     * Défini la durée des fichiers de logs
     *
     * @param int $duree
     */
    public function setDuration($duree)
    {
        $this->duree = intval($duree);
    }

    /**
     * Défini le mode de message
     *
     * @param int $mode
     */
    public function setMode($mode)
    {
        $this->mode = intval($mode);
    }

    /**
     * Défini le préfixe des fichiers
     *
     * @return string
     */
    protected function getPrefixe()
    {
        if ($this->duree == self::DAILY)
            return date("d-m-Y") . '_';
        elseif ($this->duree == self::MONTHLY)
            return date("m-Y") . '_';
        else
            return '';
    }

    abstract protected function _write($message, $level);
}