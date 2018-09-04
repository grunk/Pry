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
 * Class d'écriture de log sur un serveur Syslog
 *
 * @package Log
 * @subpackage Log_Writer
 * @version 1.0.0
 * @author Olivier ROGER <oroger.fr>
 *
 */
class Syslog extends WriterAbstract
{

    const FACILITY_KERNEL          = 0;
    const FACILITY_USER_LEVEL      = 1;
    const FACILITY_MAIL_SYSTEM     = 2;
    const FACILITY_SYSTEM_DEAMON   = 3;
    const FACILITY_SECURITY_MSG    = 4;
    const FACILITY_INTERNAL_SYSLOG = 5;
    const FACILITY_LINE_PRINTER    = 6;
    const FACILITY_NETWK_NEWS      = 7;
    const FACILITY_UUCP            = 8;
    const FACILITY_CLOCK_DEAMON    = 9;
    const FACILITY_AUTH_MSG        = 10;
    const FACILITY_FTP_DEAMON      = 11;
    const FACILITY_NTP             = 12;
    const FACILITY_LOG_AUDIT       = 13;
    const FACILITY_LOG_ALERT       = 14;
    const FACILITY_LOCAL_USE0      = 16;
    const FACILITY_LOCAL_USE1      = 17;
    const FACILITY_LOCAL_USE2      = 18;
    const FACILITY_LOCAL_USE3      = 19;
    const FACILITY_LOCAL_USE4      = 20;
    const FACILITY_LOCAL_USE5      = 21;
    const FACILITY_LOCAL_USE6      = 22;
    const FACILITY_LOCAL_USE7      = 23;

    /**
     * Serveur syslog
     * @var string
     */
    private $syslogServer;

    /**
     * Port de communication
     * @var int
     */
    private $port;

    /**
     * Facility
     * @var int
     */
    private $facility = 1;

    /**
     * Niveau de sévérité
     * @var int
     */
    private $severity = null;

    /**
     * Application émettant le message
     * @var string
     */
    private $app = '';

    /**
     * Constructeur
     * @param string $host IP du serveur
     * @param string $port Port de communication. defaut 514
     */
    public function __construct($host, $port = 514)
    {
        $this->syslogServer = 'udp://' . $host;
        $this->port         = intval($port);
    }

    /**
     * Défini le niveau de "Facility" 0- 23
     * @see http://fr.wikipedia.org/wiki/Syslog
     * @param int $facility
     */
    public function setFacility($facility)
    {
        $facility       = intval($facility);
        if ($facility < 24)
            $this->facility = $facility;
        else
            throw new \RangeException('Facility doit être compris entre 0 et 23');
    }

    /**
     * Défini la sévérité du message
     * @param int $severity
     */
    public function setSeverity($severity)
    {
        $severity       = intval($severity);
        if ($severity < 8)
            $this->severity = $severity;
        else
            throw new \RangeException('Severity doit être compris entre 0 et 7');
    }

    /**
     * Défini l'application envoyant le message
     * @param string $app
     */
    public function setApp($app)
    {
        $this->app = $app;
    }

    /**
     * Ecriture du message vers le serveur syslog
     * @param string $message
     * @param int $level Niveau d'importance
     * @throws \Exception
     */
    protected function _write($message, $level)
    {
        $content        = $this->app . ':' . $message;
        if (empty($this->severity))
            $this->severity = $level;

        $pri       = '<' . ($this->facility * 8 + $this->severity) . '>';
        $header    = $_SERVER['REMOTE_ADDR'] . ' ';
        $syslogMsg = $pri . $header . $content;
        $socket    = fsockopen($this->syslogServer, $this->port);

        if ($socket)
        {
            fwrite($socket, $syslogMsg);
            fclose($socket);
        }
        else
        {
            throw new \Exception('Impossible de contacter le serveur Syslog');
        }
    }

}