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
 *  Wrapper de la gestion de session PHP.
 * 
 * Simplifie l'accès au variable de session via les methodes _get et _set
 * @category Pry
 * @package Session
 * @version 1.0.7
 * @author Olivier ROGER <oroger.fr>
 */
class Session
{

    /**
     * Nom de la session
     *
     * @var string
     */
    private $sessionName;

    /**
     * Durée de la session en seconde
     *
     * @var float
     */
    private $sessionTTL;

    /**
     * La session utilise t'elle un cookie ?
     * @var boolean
     */
    private $useCookie;

    /**
     * Force la regeneration de l'id automatiquement.
     * 
     * @var boolean 
     */
    private $regenerate;

    /**
     * Instance du singleton
     *
     * @var Session_Session
     */
    static private $instance;

    /**
     * Etat de la session
     * @static
     * @var boolean
     */
    static protected $started = false;

    /**
     * Initialisation de la session (Singleton)
     *
     * @param string $name
     * @param float $ttl
     * @param boolean $regenerate Force la génération de l'id. A eviter en mode BDD
     * @access private
     */
    private function __construct($name = 'PrySess', $ttl = null, $regenerate = true)
    {
        if (isset($_SESSION))
            throw new Exception('Une session existe déjà');

        $this->sessionName    = $name;
        $this->sessionTTL     = $ttl;
        $this->useCookie      = (boolean) ini_get('session.use_cookies');
        $this->autoRegenerate = $regenerate;

        session_name($this->sessionName);

        if (!self::$started)
        {
            session_start();
            if ($this->regenerate)
            {
                session_regenerate_id();
            }
            self::$started = true;
        }

        if (self::$started)
        {
            if (!is_null($this->sessionTTL))
                if (empty($_SESSION['ttl']))
                    $_SESSION['ttl'] = time() + $this->sessionTTL;
                else
                    $_SESSION['ttl'] = -1;
        }
    }

    /**
     * Récupère une instance de Session
     *
     * @static 
     * @param string $name
     * @param float $ttl
     * @return Session_Session
     */
    public static function getInstance($name = 'PrySess', $ttl = null)
    {
        if (!isset(self::$instance))
            self::$instance = new Session($name, $ttl);

        return self::$instance;
    }

    /**
     * Retourne une variable de session
     * Null si la variable n'existe pas
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return array_key_exists($name, $_SESSION) ? $_SESSION[$name] : null;
    }

    /**
     * Surcharge de isset
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($_SESSION[$name]);
    }

    /**
     * Défini une variable de session
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * Regénère une id de session ainsi que le ttl
     *
     * @param boolean $delete_old
     */
    public function refresh($delete_old = true)
    {
        session_regenerate_id($delete_old);
        if (!is_null($this->sessionTTL))
            $_SESSION['ttl'] = time() + $this->sessionTTL;
    }

    /**
     * Vérifie la validité de la session
     * 
     * @return boolean
     */
    public function check()
    {
        $now = time();
        if (is_null($this->sessionTTL) || $this->sessionTTL == -1)
            return true;
        elseif ($_SESSION['ttl'] - $now > 0)
        {
            $this->refresh();
            return true;
        }
        else
        {
            $this->destroy();
            return false;
        }
    }

    /**
     * Suppression d'une valeur dans la session
     * @param string $key Clé de la valeur
     */
    public function remove($key)
    {
        if (isset($_SESSION[$key]))
            unset($_SESSION[$key]);
    }

    /**
     * Destruction de la session
     *
     */
    public function destroy()
    {
        if (isset($_SESSION))
        {
            @session_unset();
            @session_destroy();
        }

        if ($this->useCookie)
        {
            $cookie             = session_get_cookie_params();
            $cookie['httponly'] = isset($cookie['httponly']) ? $cookie['httponly'] : false;
            setcookie($this->sessionName, '', time() - 42000, $cookie['path'], $cookie['domain'], $cookie['domain'], $cookie['httponly']);
        }
    }

}