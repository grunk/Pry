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
 * Classe d'ecriture de log dans une base de données
 * 
 * @package Log
 * @subpackage Log_Writer
 * @version 1.2.0
 * @author Olivier ROGER <oroger.fr>
 *       
 */
class Bd extends WriterAbstract
{

    private $table;

    /**
     * Objet base de données
     * @var \PDO
     */
    private $oSql;

    /**
     * Constructeur
     *
     * @param string $table contenant les logs
     * @param \PDO $sql
     */
    public function __construct($table, $sql)
    {
        $this->table = $table;
        $this->oSql  = $sql;
        //var_dump($this->oSql);
    }

    /**
     * Log du message dans une base de données
     *
     * @param string $message
     * @param string $level
     * @access protected
     */
    protected function _write($message, $level)
    {
        $prefixe = ($this->mode == self::MODE_MINI) ? '' : '[' . date("d/m/Y - H:i:s") . '] (' . $this->txtSeverity[$level] . ') ';

        $prep = $this->oSql->prepare('INSERT INTO ' . $this->table . ' (level,date,message) VALUES(:level,:date,:message)');
        $prep->execute(array(':level' => $level, ':date' => date("Y-m-d H:i:s"), ':message' => $prefixe . $message));
    }

}