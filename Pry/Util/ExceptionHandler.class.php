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
 * Gestion des exceptions personnalisée
 * @category Pry
 * @package Util
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr>
 * @copyright  2007-2012 Prynel
 * <code>
 * try{
 * 	//code
 * }
 * catch(ExceptionHandler e){
 * 	$e->getError()
 * }      
 * </code>
 */
class ExceptionHandler extends \Exception
{

    protected $image = 'data:image/png;base64,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%3D%3D';

    public function __construct($msg)
    {
        parent::__construct($msg);
    }

    /**
     * Retourne la date et l'heure au format Fr
     *
     * @return string
     */
    public function getTime()
    {
        return date("d/m/Y H:i:s");
    }

    /**
     * Retourne l'erreur
     *
     * @param boolean $detail Info détaillée
     * @return string
     */
    public function getError($detail = false)
    {
        header('Content-Type: text/html; charset=utf-8');
        $retour = '<div style="background:url(' . $this->image . ') 10px #FBE3E4 no-repeat; color: #8a1f11; border:1px solid #FBC2C4;">';
        $retour.= $this->getTime() . ' : Une exception à été détectée :<br />';
        $retour.= '<strong style="color:red">' . $this->getMessage() . '</strong><br />';
        if ($detail)
        {
            $retour.= '<strong>Fichier : </strong> ' . $this->getFile() . '<br />';
            $retour.= '<strong>Ligne : </strong>' . $this->getLine() . '<br />';
        }
        $retour.= '</div>';
        return $retour;
    }

}

?>