<?php

/**
 * Pry Framework
 * @copyright 2007-2011 Prynel
 * @author Olivier ROGER <oroger.fr>
 * @category Pry
 * @package Net
 */

namespace Pry\Net\Exception;

/**
 * Transformation des erreurs de socket en exception pour une meilleure gestion.
 * @category Pry
 * @package Net
 * @subpackage Net_Exception
 * @version 1.0
 * @author Olivier ROGER <oroger.fr>
 * @copyright  2007-2012 Prynel
 *
 */
abstract class Handler
{

    /**
     * Initialise le gestionnaire d'erreur sur la classe d'exception 
     */
    public static function initialize()
    {
        set_error_handler(array("Pry\Net\Exception\Handler", "handleError"));
    }

    /**
     * Restaure le gestionnaire d'erreur par défaut
     */
    public static function uninitialize()
    {
        restore_error_handler();
    }

    /**
     * Transforme l'erreur en exception
     * @param int $errno Numéro d'erreur
     * @param string $errstr Message de l'erreur
     * @param string $errfile Fichier concerné
     * @param int $errline Numéro de ligne
     * @param array $errcontext Variable présente dans le contexte
     * @throws Net_Exception_Socket 
     */
    public static function handleError($errno, $errstr, $errfile, $errline, $errcontext)
    {
        throw new Socket($errstr, $errno, $errfile, $errline, $errcontext);
    }

}

?>
