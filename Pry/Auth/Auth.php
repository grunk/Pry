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

namespace Pry\Auth;

use Pry\Auth\Bcrypt;
use Pry\Auth\Interfaces\OnAutoLoginEvent;
use Pry\Session\Session;

/**
 * Identification d'utilisateur via BDD
 * 
 * <code>
 * // Exemple d'identification
 * $sess = Session::getInstance('nomSession',10);
 * $auth = new Auth($sess);
 * $auth->setUserTable('user');
 * $auth->setUserField('login');
 * $auth->setPwdField('pwd');
 * $auth->setHashRounds(10);
 * $auth->login($_POST['login'],$_POST['mdp']);
 * if(!$auth->error)
 * {
 *   if($auth->isLogged())
 *   {
 *     echo 'Connnecté';
 *     print_r($_SESSION);
 *   }
 *   else
 *     echo 'pas Connecté';
 *   }
 * else
 * {
 *   echo $auth->displayError();
 * }
 * </code>
 * 
 * @category Pry
 * @package Auth
 * @version 1.4.2 
 * @author Olivier ROGER <oroger.fr>
 * @see http://stackoverflow.com/questions/549/the-definitive-guide-to-forms-based-website-authentication#477579  
 * 
 */
class Auth
{

    const NO_ERROR    = 0;
    const ERROR_LOG   = 1;
    const ERROR_PASS  = 2;
    const ERROR_TABLE = 3;
    const ERROR_FIELD = 4;

    /**
     * Objet pour accès bdd
     * @access private
     * @var Zend_Db_Adapter_Abstract
     */
    private $oDB;

    /**
     * Type d'erreur rencontrée
     * @access private
     * @var int $errorType
     */
    private $errorType;

    /**
     * Message d'erreur
     * @access private
     * @var string $errorMsg
     */
    private $errorMsg;

    /**
     * Table des utilisateurs
     * @access public
     * @var string $userTable
     */
    private $userTable;

    /**
     * Champs du nom d'utilisateur
     * @access public
     * @var string $userField
     */
    private $userField;

    /**
     * Champs du mot de passe
     * @access public
     * @var string $pwdField
     */
    private $pwdField;

    /** Champs contenant le token d'autologin */
    private $autologTokenField;

    /** Nombre d'itération pour l'algo bcrypt */
    private $hashRounds;

    /**
     * Connexion automatique
     * @access public
     * @var boolean $autoLogin
     */
    private $autoLogin;

    /**
     * Option des cookies
     * @access public
     * @var array $cookieOption
     */
    private $cookieOption;

    /**
     * Durée de vie de l'authentification
     * @var int 
     */
    private $timeOutSession;

    /**
     * Erreur lors de l'identification
     *
     * @var boolean $error
     */
    public $error;

    /**
     * Session
     *
     * @var Session_Session
     */
    private $session;

    /**
     * Constructeur
     * 
     * @param PDO $db Objet Zend Db
     * @param Session_Session $session
     * @access public
     */
    public function __construct(Session $session, \PDO $db)
    {
        $this->oDB               = $db;
        $this->userTable         = 'user';
        $this->userField         = 'login';
        $this->pwdField          = 'password';
        $this->autologTokenField = 'autologKey';
        $this->autoLogin         = false;

        $this->cookieOption = array(
            'name' => 'loginCookie',
            'ttl' => time() + (365 * 24 * 3600)
        );
        $this->timeOutSession = 0;
        $this->error          = false;
        $this->errorType      = self::NO_ERROR;
        $this->errorMsg       = '';
        $this->session        = $session;

        if (!isset($session->AC_lastAct))
        {
            $session->AC_connected = 0;
            $session->AC_lastAct   = 0;
        }
    }

    /**
     * Identification classique via formulaire
     *
     * @param string $login
     * @param string $pass
     */
    public function login($login, $pass)
    {
        if ($this->checkUser($login))
            if ($this->checkPass($login, $pass))
            {
                $this->startSession();
                $this->session->AC_connected = true;
                $this->session->AC_lastAct   = time();
                if ($this->autoLogin)
                {
                    $this->createCookie($login);
                }
            }
            else
                $this->displayError();
        else
            $this->displayError();
    }

    public function logout()
    {
        $this->destroySession();
        $this->destroyCookie();
    }

    /**
     * Vérifie si l'utilisateur est identifié
     *
     * @return boolean
     */
    public function isLogged()
    {
        $lastActivity = time() - $this->session->AC_lastAct;
        // Cas 1 - Session existante illimité
        if ($this->session->AC_connected === true && $this->timeOutSession == 0)
        {
            $this->session->AC_lastAct = time();
            if ($lastActivity > 300)
                $this->session->refresh();
            return true;
        }
        // Cas 2 - Session limité mais existante et TTL non dépassé
        if ($this->session->AC_connected === true && $lastActivity < $this->timeOutSession && $this->timeOutSession != 0)
        {
            $this->session->AC_lastAct = time();
            if ($lastActivity > 300)
                $this->session->refresh();
            return true;
        }
        // Cas 3 - Session limité mais existante et TTL dépassé
        if ($this->session->AC_connected === true && $lastActivity > $this->timeOutSession && $this->timeOutSession != 0)
        {
            $this->logout();
            return false;
        }
        // Cas 4 - Session inexistante mais autologin
        if (isset($this->session->AC_connected) && !$this->session->AC_connected && $this->autoLogin)
        {
            if ($this->loginCookie())
                return true;
            else
                return false;
        }

        return false;
    }

    /**
     * Affichage des erreurs
     *
     * @return string
     */
    public function displayError()
    {
        switch ($this->errorType)
        {
            case self::ERROR_LOG:
                $this->errorMsg = 'Identifiant incorrect';
                break;
            case self::ERROR_PASS:
                $this->errorMsg = 'Mot de passe incorrect';
                break;
        }
        return $this->errorMsg;
    }

    /**
     * Identification via cookie
     *
     * @return boolean
     */
    private function loginCookie()
    {
        if (isset($_COOKIE['' . $this->cookieOption['name'] . '']))
        {
            $datas = $_COOKIE['' . $this->cookieOption['name'] . ''];
            $pos   = strripos($datas, '|');
            $login = substr($datas, 0, $pos);
            $token = substr($datas, $pos + 1);

            if ($this->checkUser($login))
            {

                $prepare = $this->oDB->prepare('SELECT ' . $this->autologTokenField . ' FROM ' . $this->userTable . ' WHERE ' . $this->userField . ' = :logCookie');
                $prepare->execute(array(':logCookie' => $login));
                $data        = $prepare->fetchColumn();

                if ($token == $data)
                {
                    $this->startSession();
                    $this->session->AC_connected = true;
                    $this->session->AC_lastAct   = time();
                    //Mise à jour du cookie et du token d'autologin
                    $this->destroyCookie();
                    $this->createCookie($login);

                    if (!empty($this->autoLoginEvent))
                    {
                        $this->autoLoginEvent->onAutoLogin($login);
                    }

                    return true;
                }
                else
                {
                    $this->error     = true;
                    $this->errorType = self::ERROR_PASS;
                    $this->destroyCookie();
                    $this->displayError();
                }
            }
            else
            {
                $this->destroyCookie();
                $this->displayError();
            }
        }
        return false;
    }

    /**
     * Démarrage session si innexistante
     *
     */
    public function startSession($name = 'acauth')
    {
        if (empty($this->session))
            $this->session = Session_Session::getInstance($name, $this->timeOutSession);
    }

    /**
     * Création du cookie avec salage du mdp avec le token
     *
     * @param string $login
     */
    private function createCookie($login)
    {
        $token = $this->generateRandomToken();
        $value = $login . '|' . $token;
        setcookie($this->cookieOption['name'], $value, $this->cookieOption['ttl'], '/');

        //Mise à jour du token dans la base
        $prep = $this->oDB->prepare('UPDATE ' . $this->userTable . ' SET ' . $this->autologTokenField . ' = :token WHERE ' . $this->userField . ' = :user');
        $prep->execute(array(
            ':token' => $token,
            ':user' => $login
        ));
    }

    private function generateRandomToken()
    {
        $token = '';
        $char  = '+-*$=)_!?./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        for ($i = 0; $i < 35; $i++)
            $token .= $char[mt_rand(0, 72)];

        return sha1($token);
    }

    /**
     * Destruction de la session et de toute les variables associées
     *
     */
    private function destroySession()
    {
        $this->session->destroy();
    }

    /**
     * Destruction du cookie
     *
     */
    private function destroyCookie()
    {
        setcookie($this->cookieOption['name'], NULL, time() - 10, '/');
    }

    /**
     * Vérification de l'identifiant
     *
     * @param string $user
     * @return boolean
     */
    private function checkUser($user)
    {
        $prepare = $this->oDB->prepare('SELECT ' . $this->userField . ' FROM ' . $this->userTable . ' WHERE ' . $this->userField . ' = :user');
        $prepare->execute(array(':user' => $user));
        if ($prepare->fetchColumn())
            return true;
        else
        {
            $this->error     = true;
            $this->errorType = self::ERROR_LOG;
            return false;
        }
    }

    /**
     * Vérification du mot de passe
     *
     * @param string $login Identifiant
     * @param string $pass
     * @access private
     * @return boolean
     */
    private function checkPass($login, $pass)
    {
        $prepare = $this->oDB->prepare('SELECT ' . $this->pwdField . ' FROM ' . $this->userTable . ' WHERE ' . $this->userField . ' = :login');
        $prepare->execute(array(':login' => $login));
        $hash    = $prepare->fetchColumn();

        if (Bcrypt::check($pass, $hash))
            return true;
        else
        {
            $this->error     = true;
            $this->errorType = self::ERROR_PASS;
            return false;
        }
    }

    /**
     * Hash le mot de pass dans l'algo souhaité
     *
     * @param string $pass
     * @return string
     */
    private function hashPass($pass)
    {
        $bCrypt = new Bcrypt($this->hashRounds);
        return $bCrypt->hash($pass);
    }

    /**
     * Retourne l'erreur si existante
     *
     * @since 1.0.5
     * @return int
     */
    public function getErrorType()
    {
        return $this->errorType;
    }

    /**
     * Défini le nom de la table contenant les comptes utilisateurs
     * @param string $userTable 
     */
    public function setUserTable($userTable)
    {
        $this->userTable = $userTable;
    }

    /**
     * Défini le champs contenant l'identifiant unique par utilisateur
     * @param string $userField 
     */
    public function setUserField($userField)
    {
        $this->userField = $userField;
    }

    /**
     * Défini le champs contenant le hash du mot de passe
     * @param string $pwdField 
     */
    public function setPwdField($pwdField)
    {
        $this->pwdField = $pwdField;
    }

    /**
     * Défini le champs contenant le token utilisé par le cookie d'autologin
     * @param string $autologTokenField 
     */
    public function setAutologTokenField($autologTokenField)
    {
        $this->autologTokenField = $autologTokenField;
    }

    /**
     * Défini le nombre d'itération utilisé dans le cryptage Bcrypt du mot de passe
     * @param int $hashRounds 
     */
    public function setHashRounds($hashRounds)
    {
        $this->hashRounds = $hashRounds;
    }

    /**
     * Active ou non l'autologin
     * @param boolean $autoLogin 
     */
    public function setAutoLogin($autoLogin)
    {
        $this->autoLogin = $autoLogin;
    }

    /**
     * Défini les options du cookie d'autologin.
     * 
     * @param array $cookieOption Doit contenir les clés "name" (string) et ttl (int) durée de vie en seconde
     */
    public function setCookieOption($cookieOption)
    {
        $this->cookieOption = $cookieOption;
    }

    /**
     * Défini en seconde la durée de vie de l'authentification
     * @param type $timeout 
     */
    public function setTimeoutSession($timeout)
    {
        $this->timeOutSession = $timeout;
    }

    /**
     * Défini une instance de classe implémentant 
     * Auth_Interfaces_OnAutoLoginEvent afin d'appeler la méthode onAutoLogin
     * après l'authentification via cookie
     * @param Auth_Interfaces_OnAutoLoginEvent $event
     */
    public function setOnAutoLoginEvent(OnAutoLoginEvent $event)
    {
        $this->autoLoginEvent = $event;
    }

}