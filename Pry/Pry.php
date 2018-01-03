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
     * @param string $class
     */
    static public function pryLoader($class)
    {
        // project-specific namespace prefix
        $prefix = 'Pry\\';

        // base directory for the namespace prefix
        $base_dir = __DIR__.'\\';

        // does the class use the namespace prefix?
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            // no, move to the next registered autoloader
            return;
        }

        // get the relative class name
        $relative_class = substr($class, $len);
        // replace the namespace prefix with the base directory, replace namespace
        // separators with directory separators in the relative class name, append
        // with .php
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        // if the file exists, require it
        if (file_exists($file)) {
            require $file;
        }
    }

}