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

namespace Pry\Net;

/**
 * Wrapper pour l'utilsation de socket
 * @category Pry
 * @package Net
 * @version 1.0
 * @author Olivier ROGER <oroger.fr>
 *
 */
class Socket
{
    /** Etat connecté */
    const CONNECTED    = 1;
    
    /** Etat non connecté */
    const DISCONNECTED = 0;

    /**
     * Hote vers lequel se connecter. Ip ou domaine
     * @var string 
     */
    protected $host = "";

    /**
     * Port de connexion
     * @var int 
     */
    protected $port = 0;

    /**
     * Timeout de connexion en seconde. Egalement utilisé pour les timeout de lecture/écriture
     * @var int 
     */
    protected $timeout = 5;

    /**
     * Socket
     * @var resource 
     */
    protected $socket = null;

    /**
     * Etat de la socket
     * @var int 
     */
    protected $state = 0;

    /**
     * Socket bloquante ou non
     * @var boolean 
     */
    protected $isBlocking = false;

    /**
     * Création de la socket. Initialise également l'error handler pour transformer les 
     * erreur de socket en exception.
     * @param type $blocking 
     */
    public function __construct($blocking = true)
    {
        $this->isBlocking = $blocking;
        \Pry\Net\Exception\Handler::initialize();
        $this->socket     = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    }

    /**
     * Connexion de la socket sur $host:$port avec un delay de $timeout
     * @param type $host
     * @param type $port
     * @param type $timeout
     * @return boolean
     * @throws RuntimeException 
     */
    public function connect($host, $port, $timeout = 15)
    {
        $this->host    = $host;
        $this->port    = $port;
        $this->timeout = $timeout;
        if (!empty($this->socket) && is_resource($this->socket))
        {
            //Timeout de lecture
            socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array(
                'sec' => $this->timeout,
                'usec' => 0)
            );
            //Timeout d'écriture
            socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, array(
                'sec' => $this->timeout,
                'usec' => 0)
            );

            $connect = socket_connect($this->socket, $this->host, $this->port);
            if (!$connect)
            {
                socket_close($this->socket);
                throw new \RuntimeException('Can\'t connect to ' . $this->host . ':' . $this->port . '. 
                    Reason : ' . socket_strerror(socket_last_error()));
            }
        }
        else
        {
            return false;
        }

        $this->state = self::CONNECTED;
        return true;
    }

    /**
     * Déconnecte la socket
     * @return boolean 
     */
    public function disconnect()
    {
        if ($this->isConnected())
        {
            socket_close($this->socket);
            $this->state = self::DISCONNECTED;
            return true;
        }

        return false;
    }

    /**
     * Ecrit sur la socket le contenu de $buffer
     * @param string $buffer
     * @return mixed Nombre d'octet ou false en cas d'erreur
     * @throws InvalidArgumentException 
     */
    public function write($buffer)
    {
        if ($this->isConnected())
        {
            return socket_write($this->socket, $buffer, strlen($buffer));
        }
        throw new \InvalidArgumentException('No active connection');
    }

    /**
     * Lecture sur la socket
     * @param int $size nombre d'octet à lire
     * @return boolean
     * @throws RuntimeException 
     */
    public function read($size = 2048)
    {
        if ($this->isConnected())
        {
            $datas = socket_read($this->socket, $size, PHP_BINARY_READ);
            if ($datas !== false)
            {
                return $datas;
            }
            else
            {
                Throw new \RuntimeException("Lecture impossible : " . socket_strerror(socket_last_error($this->socket)));
            }

            return false;
        }
    }

    /**
     * Vérifie si la socket est connecté
     * @return boolean 
     */
    public function isConnected()
    {
        return (is_resource($this->socket) && $this->state == self::CONNECTED);
    }

    public function __destruct()
    {
        \Pry\Net\Exception\Handler::uninitialize();
    }

}
