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
 * Get environement information
 * @author Olivier ROGER <oroger.fr>
 *  
 */
class Environment
{
    /**
     * Are we on 64bit
     * @return boolean
     */
    public static function is64Bits() : bool
    {
        return PHP_INT_SIZE == 8;
    }
    
    /**
     * Are we on 32bits
     * @return boolean
     */
    public static function is32Bits() :bool
    {
        return PHP_INT_SIZE == 4;
    }
    
    /**
     * Are we on Windows
     * @return boolean
     */
    public static function isWindows() : bool
    {
        return self::getOS() == 'Windows';
    }
    
    /**
     * Are we on linux
     * @return boolean
     */
    public static function isLinux() : bool
    {
        return self::getOS() == 'Linux';
    }
    
    /**
     * Are we on freebsd
     * @return boolean
     */
    public static function isFreeBSD() : bool
    {
        return self::getOS() == 'FreeBSD';
    }
    
    /**
     * Are we on MACOS
     * @return boolean
     */
    public static function isMacOS()
    {
        return self::getOS() == 'Mac OS X';
    }
    
    /**
     * Get Server IP
     * @return string IP
     */
    public static function getIP() : ?string
    {
        return $_SERVER['SERVER_ADDR'];
    }
    
    /**
     * Get current OS
     * @return string Windows|Linux|FreeBSD|Mac OS X| Unknown
     */
    public static function getOS() : string
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
