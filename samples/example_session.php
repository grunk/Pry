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
define("INC_CORE_PATH", realpath(dirname(__FILE__).'/../PryNS/Pry'));
set_include_path(INC_CORE_PATH . PATH_SEPARATOR . get_include_path());

require('../Pry/Pry.php');
Pry::register();

/*
$sess = Session_Session::getInstance('PrynSess',1);
$sess->login = "oroger";
var_dump($_SESSION);
sleep(3);
$sess->check();
var_dump($_SESSION);
*/

// Session géré par BDD
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

$sessionTable = array(
	'db_table'    => 'php_sessions',
	'db_id_col'   => 'id',
	'db_data_col' => 'data',
	'db_time_col' => 'ttl');
	
$sess = Pry\Session\DbStorage::getInstance($sql,$sessionTable, 3600);
Pry\Session\Session::getInstance('PrynSess',60,false);
$sess->login = 'oroger';
$sess->test = 123654;

var_dump($_SESSION);
 ?>