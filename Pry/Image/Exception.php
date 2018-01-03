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

namespace Pry\Image;

use Pry\Util\ExceptionHandler;

/**
 *
 * @category Pry
 * @package Image
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr>
 *
 */
class Exception extends ExceptionHandler
{
    public function __construct($msg)
    {
        parent::__construct($msg);
    }
    
    public function getError($detail = false)
    {
        header('Content-Type: text/html; charset=utf-8');
        $retour = '<div style="background:url('.$this->image.') right no-repeat;background-color: #FBE3E4; color: #8a1f11; border-color: #FBC2C4;">';
        $retour.= $this->getTime().' : Impossible d\'afficher l\'image, exception détectée :<br />';
        $retour.= '<strong style="color:red">'.$this->getMessage().'</strong><br />';
        $retour.= '<strong>Fichier : </strong> '.$this->getFile().'<br />';
        $retour.= '<strong>Ligne : </strong>' .$this->getLine().'<br />';
        $retour.= '</div>';
        return $retour;
    }
}