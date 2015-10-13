<?php
/**
 *
 * @package Demo
 * @version 1.0.0 
 * @author Olivier ROGER <roger.olivier@gmail.com>
 * @copyright  2007-2010 Prynel
 *       
 *
 */
//Inclusion minimale et indispensable
define("INC_CORE_PATH", realpath(dirname(__FILE__).'/../vendor'));
set_include_path(INC_CORE_PATH . PATH_SEPARATOR . get_include_path());

require('Zend/Loader/Autoloader.php');
Zend_Loader_Autoloader::getInstance();

use Pry\Config\Ini;
require('../Pry/Pry.php');
Pry::register();

try{
	$configIni = new Ini('config.ini','dev');
}
catch(Exception $e){
	echo $e->getError();
}

//BDD
try{
	//var_dump($configIni->database->toArray());
	$sql = Zend_Db::factory($configIni->database->adapter,$configIni->database->params->toArray());
	$sql->getConnection();
	
	var_dump($sql->fetchAll('SELECT * FROM user'));
}
catch(Zend_Db_Adapter_Exception $e){
	echo $e->getError();
}
 ?>