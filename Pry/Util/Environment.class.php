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
 * Récupération d'information sur l'environnement d'execution de PHP
 *
 * @category Pry
 * @package Util
 * @version 1.0.0
 * @author Olivier ROGER <oroger.fr>
 *  
 */
class Environment
{
    /**
     * PHP est il en 64bits
     * @return boolean
     */
    public static function is64Bits()
    {
        if(PHP_INT_SIZE == 8)
            return true;
        return false;
    }
    
    /**
     * PHP est il 32 bits
     * @return boolean
     */
    public static function is32Bits()
    {
        if(PHP_INT_SIZE == 4)
            return true;
        return false;
    }
    
    /**
     * PHP tourne il sur Windows
     * @return boolean
     */
    public static function isWindows()
    {
        if(self::getOS() == 'Windows')
            return true;
        return false;
    }
    
    /**
     * PHP tourne il sur Linux
     * @return boolean
     */
    public static function isLinux()
    {
        if(self::getOS() == 'Linux')
            return true;
        return false;
    }
    
    /**
     * PHP tourne il sur FreeBSD
     * @return boolean
     */
    public static function isFreeBSD()
    {
        if(self::getOS() == 'FreeBSD')
            return true;
        return false;
    }
    
    /**
     * PHP tourne il sur MACOS
     * @return boolean
     */
    public static function isMacOS()
    {
        if(self::getOS() == 'Mac OS X')
            return true;
        return false;
    }
    
    /**
     * Retourne l'IP du serveur
     * @return string IP du serveur
     */
    public static function getIP()
    {
        return $_SERVER['SERVER_ADDR'];
    }
    
    /**
     * Récupère le système hébergeant PHP
     * @return string Windows|Linux|FreeBSD|Mac OS X| Unknown
     */
    public static function getOS()
    {
        $osStr = strtoupper(PHP_OS);
        
        if(strpos($osStr, 'WIN') !== false)
            return 'Windows';
        elseif(strpos($osStr, 'LINUX') !== false)
            return 'Linux';
        elseif(strpos($osStr, 'BSD') !== false)
            return 'FreeBSD';
        elseif(strpos($osStr, 'DARWIN') !== false)
            return 'Mac OS X';
        else
            return 'Unknown';
    }
}
