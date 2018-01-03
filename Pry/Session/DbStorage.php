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

namespace Pry\Session;

/**
 *  Gestion de session via BDD.
 *
 * Permet de gérer les session PHP via une bdd plutôt que par cookie
 * @category Pry
 * @package Session
 * @version 0.9
 * @author Olivier ROGER <oroger.fr>
 */
class DbStorage extends Session
{

    /**
     * @var Zend_Db_Adapter_Abstract 
     */
    private $dbh;

    /** Tableau d'option pour la configuration de la table */
    private $options;

    /** TTL de la session */
    private $ttl;
    static private $instance;

    private function __construct($dbh, $opts, $ttl)
    {
        $this->dbh     = $dbh;
        $this->ttl     = $ttl;
        $this->options = array_merge(array(
            'db_table' => 'php_session',
            'db_id_col' => 'sess_id',
            'db_data_col' => 'sess_data',
            'db_time_col' => 'sess_time'
                ), $opts);

        //Redéfinition des handler de session PHP
        session_set_save_handler(
                array($this, 'sessionOpen'), array($this, 'sessionClose'), array($this, 'sessionRead'), array($this, 'sessionWrite'), array($this, 'sessionDestroy'), array($this, 'sessionGC')
        );
    }

    /**
     * Singleton
     * @param Zend_Db_Adapter_Abstract $dbh Objet bdd
     * @param array $opts Option de config de la table
     * @param int $ttl Durée de vie de la session en seconde
     * @return Session_DbStorage 
     */
    public static function getInstance($dbh, array $opts, $ttl = null)
    {
        if (!isset(self::$instance))
            self::$instance = new DbStorage($dbh, $opts, $ttl);

        return self::$instance;
    }

    public function sessionClose()
    {
        return true;
    }

    public function sessionOpen($path = null, $name = null)
    {
        
    }

    public function sessionDestroy($id)
    {
        $db_table  = $this->options['db_table'];
        $db_id_col = $this->options['db_id_col'];

        $sql = 'DELETE FROM ' . $db_table . ' WHERE ' . $db_id_col . '= ?';

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(1, $id, \PDO::PARAM_STR);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \Exception(sprintf('Unable to destroy the session. Message: %s', $e->getMessage()));
        }

        return true;
    }

    public function sessionGC($lifetime)
    {
        $db_table    = $this->options['db_table'];
        $db_time_col = $this->options['db_time_col'];

        // delete the record associated with this id
        $sql = 'DELETE FROM ' . $db_table . ' WHERE ' . $db_time_col . ' < \'' . date("Y-m-d H:i:s") . '\'';

        try {
            $this->dbh->query($sql);
        } catch (\PDOException $e) {
            throw new \Exception(sprintf('Unable to clean expired sessions. Message: %s', $e->getMessage()));
        }

        return true;
    }

    public function sessionRead($id)
    {
        // get table/columns
        $db_table    = $this->options['db_table'];
        $db_data_col = $this->options['db_data_col'];
        $db_id_col   = $this->options['db_id_col'];

        try {
            $sql = 'SELECT ' . $db_data_col . ' FROM ' . $db_table . ' WHERE ' . $db_id_col . '=?';

            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(1, $id, \PDO::PARAM_STR, 255);

            $stmt->execute();
            $sessionRows = $stmt->fetchAll(\PDO::FETCH_NUM);

            if (1 === count($sessionRows))
            {
                return $sessionRows[0][0];
            }
            else
            {
                // session does not exist, create it
                $this->createSession($id, '');
                return false;
            }
        } catch (\PDOException $e) {
            throw new \Exception(sprintf('Unable to read session data. Message: %s', $e->getMessage()));
        }
    }

    public function sessionWrite($id, $data)
    {
        // get table/column
        $db_table    = $this->options['db_table'];
        $db_data_col = $this->options['db_data_col'];
        $db_id_col   = $this->options['db_id_col'];
        $db_time_col = $this->options['db_time_col'];

        $sessionExist = $this->dbh->fetchCol('SELECT COUNT(*) FROM ' . $db_table . ' WHERE ' . $db_id_col . ' = ?', $id);

        if ($sessionExist)
        {
            try {
                $dt   = new \DateTime('now');
                $dt->modify('+' . $this->ttl . ' seconds');
                $date = $dt->format("Y-m-d H:i:s");

                $this->dbh->update($db_table, array(
                    $db_id_col => $id,
                    $db_data_col => $data,
                    $db_time_col => $date
                        ), "$db_id_col = '$id'");
            } catch (\PDOException $e) {
                throw new \Exception(sprintf('Unable to write session data. Message: %s', $e->getMessage()));
            }
        }
        else
        {
            $this->createSession($id, $data);
        }

        return true;
    }

    private function createSession($id, $data)
    {
        $dt   = new \DateTime('now');
        $dt->modify('+' . $this->ttl . ' seconds');
        $date = $dt->format("Y-m-d H:i:s");
        $this->dbh->insert($this->options['db_table'], array(
            $this->options['db_id_col'] => $id,
            $this->options['db_data_col'] => $data,
            $this->options['db_time_col'] => $date
        ));
    }

}