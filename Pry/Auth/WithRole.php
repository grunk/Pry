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
namespace Pry\Auth;

use Pry\Auth\ACL;

/**
 * Classe abstraite à étendre par toute classe utilisateur souhaitant utiliser les roles/permissions
 * @package Auth
 * @category Pry
 * @version 1.0.1
 * @author Olivier ROGER <oroger.fr>
 *
 */
abstract class WithRole
{
    /** @var \Pry\Auth\ACL**/
    protected $ACL;
    
    /**
     * Défini l'objet d'ACL gérant les roles et permissions
     * @param ACL $acl L'objet ACL
     */
    abstract public function setACL(ACL $acl);
    
    /**
     * Peuple l'objet ACL avec les roles et permissions associés à l'utilisateur 
     */
    abstract public function populateACL();


    /**
     * Vérifie si l'utilisateur possède la permission
     * @param string $perm Nom de la permission
     * @return boolean 
     */
    public function hasPermission($perm)
    {
        return $this->ACL->hasPermission($perm);
    }
}