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
namespace Pry\Auth\Interfaces;
/**
 * Interface à implémenter pour ajouter un événement à la fin de l'autologin.
 *
 * @category Pry
 * @package Auth/Interfaces
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr>
 */
interface OnAutoLoginEvent 
{
    public function onAutoLogin($login);
}

?>
