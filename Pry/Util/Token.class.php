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
 * Classe de génération de token pour se prémunire des attaques CSRF
 * 
 * @category Pry
 * @package Util
 * @version 1.1.0
 * @author Olivier ROGER <oroger.fr>
 *       
 */
class Token
{

    /**
     * Type d'erreur retournée à la vérification du token
     * 1 = Token non passé en paramètre
     * 2 = token recu != token généré
     * 3 = token expiré
     * @var int
     */
    static public $error = 0;

    /**
     * Génère un token et le stocke en session
     *
     * @param int $ttl Durée de vie du token en minute
     * @return string
     */
    static public function genToken($ttl = 15)
    {
        if (!isset($_SESSION))
            session_start();

        $token                    = hash('sha1', uniqid(rand(), true));
        $rand                     = rand(1, 20);
        //Sha1 	= 40 caractères => 20 de longeur max
        $token                    = substr($token, $rand, 20);
        $ttl *=60;
        $_SESSION['csrf_protect'] = array();
        $_SESSION['csrf_protect']['ttl']   = time() + $ttl;
        $_SESSION['csrf_protect']['token'] = $token;
        return $token;
    }

    /**
     * Récupère le token
     *
     * @throws UnexpectedValueException Si aucun token n'est disponible
     * @return string
     */
    static public function getToken()
    {
        if (isset($_SESSION['csrf_protect']) && !empty($_SESSION['csrf_protect']))
            return $_SESSION['csrf_protect']['token'];
        else
            throw new \UnexpectedValueException('No token available');
    }

    /**
     * Récupère le timestamp de durée de vie
     *
     * @throws UnexpectedValueException Si aucun token n'est disponible
     * @return int
     */
    static public function getTTL()
    {
        if (isset($_SESSION['csrf_protect']) && !empty($_SESSION['csrf_protect']))
            return $_SESSION['csrf_protect']['ttl'];
        else
            throw new \UnexpectedValueException('No token available');
    }

    /**
     * Vérifie la validité du token
     *
     * @return boolean
     */
    static public function checkToken()
    {
        if (!isset($_SESSION))
            throw new \Exception('Can\'t check token if there is no session available');

        if (isset($_REQUEST['csrf_protect']) && !empty($_REQUEST['csrf_protect']))
        {
            if ($_REQUEST['csrf_protect'] == $_SESSION['csrf_protect']['token'])
            {
                if ($_SESSION['csrf_protect']['ttl'] - time() > 0)
                {
                    return true;
                }
                else
                {
                    self::$error = 3;
                }
            }
            else
            {
                self::$error = 2;
            }
        }
        else
        {
            self::$error = 1;
        }
        return false;
    }

    /**
     * Retourn le code erreur
     *
     * @return int
     */
    static public function getError()
    {
        return self::$error;
    }

}