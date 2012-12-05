<?php
define("CONF_MODE",'dev');
ini_set('display_errors', 'on');

require_once "../../Pry/Pry.php";
require_once '../../Pry/Twig/Autoloader.php';

//Autoload
Pry::register();

//Config
try
{
	$configIni = new Config_Ini('../includes/config/config.ini',CONF_MODE);
	Util_Registry::set('Config',$configIni);
	define('ROOT_PATH',$configIni->root.DIRECTORY_SEPARATOR);
}
catch(Exception $e){
	echo $e->getMessage();
}


//Réglage horaire
date_default_timezone_set('Europe/Paris');

//Vue avec Twig
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem(ROOT_PATH.'includes/view/');
$twig 	= new Twig_Environment($loader, array(
  'cache' 		=> false,
  'auto_reload'	=> false,
  'trim_blocks' => true
));
$view = array();

//Vue avec View_View
/*$view = new View_View();
$view->setViewBase(ROOT_PATH.'includes/view/');*/

$router = Controller_Router::getInstance();
$router->setPath(ROOT_PATH.'includes/controllers/');
$router->setView(/*$view*/$twig);
$router->load();

?>