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

use UnexpectedValueException;
use Exception;
/**
 * Generate token to prevent CSRF attack
 *
 * @author Olivier ROGER <oroger.fr>
 *       
 */
class Token
{

    public const MISSING = 1;
    public const DOES_NOT_MATCH = 2;
    public const EXPIRED = 3;
    /**
     * Possible error type returned
     * @var int
     */
    static public $error = 0;

    /**
     * Generate a token and store it in session with.
     * Will start a session if none available
     * @param int $ttl TTL in minutes for the token
     * @return string
     */
    static public function genToken(int $ttl = 15): string
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
     * Get the token
     *
     * @throws UnexpectedValueException If no token available
     * @return string
     */
    static public function getToken(): string
    {
        if (isset($_SESSION['csrf_protect']) && !empty($_SESSION['csrf_protect']))
            return $_SESSION['csrf_protect']['token'];
        else
            throw new UnexpectedValueException('No token available');
    }

    /**
     * Get the TTL timestamp
     *
     * @throws UnexpectedValueException If no token available
     * @return int
     */
    static public function getTTL(): int
    {
        if (isset($_SESSION['csrf_protect']) && !empty($_SESSION['csrf_protect']))
            return $_SESSION['csrf_protect']['ttl'];
        else
            throw new UnexpectedValueException('No token available');
    }

    /**
     * Check the token's validity
     *
     * @return boolean
     * @throws Exception
     */
    static public function checkToken(): bool
    {
        if (!isset($_SESSION))
            throw new Exception('Can\'t check token if there is no session available');
        
        $method = $_SERVER['REQUEST_METHOD'];
        $param = (isset($_REQUEST['csrf_protect'])) ? $_REQUEST['csrf_protect'] : null;
        
        if($method == 'PUT' || $method == 'DELETE')
        {
            $datas = array();
            parse_str(file_get_contents('php://input'),$datas);
            $param = (isset($datas['csrf_protect'])) ? $datas['csrf_protect'] : null;
        }
        
        
        if (isset($param) && !empty($param))
        {
            if ($param == $_SESSION['csrf_protect']['token'])
            {
                if ($_SESSION['csrf_protect']['ttl'] - time() > 0)
                {
                    return true;
                }
                else
                {
                    self::$error = Token::EXPIRED;
                }
            }
            else
            {
                self::$error = Token::DOES_NOT_MATCH;
            }
        }
        else
        {
            self::$error = Token::MISSING;
        }
        return false;
    }

    /**
     * Get error code.
     * See available const to get the different type of error
     * @return int
     */
    static public function getError(): int
    {
        return self::$error;
    }

}