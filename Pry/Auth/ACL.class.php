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

/**
 * Classe permettant la gestion de roles et de permissions.
 * 
 * <code>
 * $ACL = new ACL();
 * $ACL->addRole('Writer',array('read','write'));
 * $ACL->addPermission('Writer','delete');
 * 
 * if($ACL->hasPermission('write'))
 * 		echo 'ok';
 * else
 * 		echo 'ko';
 * </code>
 * 
 * @category Pry
 * @package Auth
 * @version 2.0.0 
 * @author Olivier ROGER <oroger.fr>
 *       
 */
class ACL
{

    /** Liste des roles associé */
    private $roles = array();

    /** Liste des permissions triées par roles */
    private $permissions = array();

    public function __construct()
    {
        
    }

    /**
     * Ajout d'un role
     * @param string $roleName Nom du role
     * @param array $permissions Les permissions associées au role
     */
    public function addRole($roleName, array $permissions)
    {
        $this->roles[]                = $roleName;
        $this->permissions[$roleName] = array();
        foreach ($permissions as $perm)
            $this->permissions[$roleName][$perm] = true;
    }

    /**
     * Ajout d'une permission à un role spécifique
     * @param string $roleName Nom du role concerné
     * @param string $permission Nom de la permission 
     */
    public function addPermission($roleName, $permission)
    {
        if (!isset($this->permissions[$roleName]))
            $this->permissions[$roleName] = array();

        $this->permissions[$roleName][$permission] = true;
    }

    /**
     * Supprime un role et les permission associées
     * @param type $name
     * @return boolean 
     */
    public function deleteRole($name)
    {
        $keyRole = array_search($name, $this->roles);
        if ($keyRole !== false)
        {
            unset($this->roles[$keyRole]);
            unset($this->permissions[$name]);
            return true;
        }

        return false;
    }

    /**
     * Vérifie que la permission est autorisée
     * @param string $permName Nom de la permission à tester
     * @return boolean true si autorisé false sinon
     */
    public function hasPermission($permName)
    {
        foreach ($this->roles as $role) {
            if (isset($this->permissions[$role][$permName]) && $this->permissions[$role][$permName] == true)
                return true;
        }

        return false;
    }

    /**
     * Méthode magique pour sérialiser correctement les données
     * @return array 
     */
    public function __sleep()
    {
        return array('roles', 'permissions');
    }

}