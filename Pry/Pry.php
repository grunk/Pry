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
/**
 *
 * @category Pry
 * @version 1.2.2 
 * @author Olivier ROGER <oroger.fr>
 */
define('ROOT_LIB', dirname(__DIR__ . '../') . DIRECTORY_SEPARATOR);

class Pry
{

    private static $version = 'Pry';

    /**
     * Retourne la revision du framework
     *
     * @return int
     */
    public static function getVersion()
    {
        return self::$version;
    }

    /**
     * Enregistre une fonction dans la pile autoload
     *
     */
    static public function register()
    {
        spl_autoload_register(array(new self, 'pryLoader'));
    }

    /**
     * Fonction d'autoload
     *
     * @param string $class_name
     */
    static public function pryLoader($class_name)
    {
        $include = '';
        $include = ROOT_LIB . str_replace('\\', DIRECTORY_SEPARATOR, $class_name) . '.class.php';
        
        if (is_readable($include))
        {
            require($include);
            return true;
        }
    }

}