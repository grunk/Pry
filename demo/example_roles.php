<?php

//Inclusion minimale et indispensable
define("INC_CORE_PATH", realpath(dirname(__FILE__).'/../PryNS/Pry'));
set_include_path(INC_CORE_PATH . PATH_SEPARATOR . get_include_path());

require('../Pry/Pry.php');
Pry::register();

// Classe utilisateur étendant Auth_WithRole
class User extends Pry\Auth\WithRole
{
	private  $nom;
	private  $prenom;
	private  $sql;
	
	public function __construct($nom,$prenom,$sql)
	{
		$this->sql = $sql;
	}
	
	public function whoami()
	{
		echo $this->prenom.' '.$this->nom;
	}
	
	public function setACL(Pry\Auth\ACL $acl)
	{
		$this->ACL = $acl;
	}
	
	public function populateACL()
	{
		$datas = $this->sql->query("SELECT r.name as rname, p.name as pname FROM roles_permissions rp INNER JOIN roles r ON r.id = rp.id_role INNER JOIN permissions p ON p.id = rp.id_permission WHERE rp.id_role = 1");
		$roleName = '';
		$perms = array();
		foreach($datas as $permrole)
		{
			$roleName 		= $permrole['rname'];
			$perms[] 	= $permrole['pname'];
		}

		
		$this->ACL->addRole($roleName,$perms);


		$this->ACL->addRole('Extras',array('existepas'));
		
		var_dump($this->ACL);
	}
}


try{
	$configIni = new Pry\Config\Ini('config.ini','dev');
}
catch(Exception $e){
	echo $e->getError();
}

//BDD
try{
	//var_dump($configIni->database->toArray());
	$sql = Zend_Db::factory($configIni->database);
	$sql->getConnection();
	
}
catch(Zend_Db_Adapter_Exception $e){
	echo $e->getError();
}

//Récupération des données du role

$user = new User('Roger','Olivier',$sql);
$user->setACL(new Pry\Auth\ACL);
$user->populateACL($sql);

var_dump($user->hasPermission('Lire'));
var_dump($user->hasPermission('Pwet'));