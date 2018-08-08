<?php
require_once '../../vendor/autoload.php';

define("CONF_MODE", 'dev');
ini_set('display_errors', 'on');

use Pry\Util\Registry;

//Config
try {
	$configIni = new Pry\Config\Ini('../application/config/config.ini', CONF_MODE);
} catch (Exception $e) {
	echo $e->getMessage();
}

Registry::set('Config', $configIni);
define('ROOT_PATH', $configIni->root . DIRECTORY_SEPARATOR);

//Réglage horaire
date_default_timezone_set('Europe/Paris');

$router = Pry\Controller\Router::getInstance();
$router->setPath(ROOT_PATH . 'application/controllers/');
$router->setNamespace('application\\controllers');

Registry::set('router', $router);

//Autoload
spl_autoload_register(function($className) {
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';
    if (false !== ($lastNsPos = strripos($className,  '\\'))) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    require '../'.$fileName;
});

$router->load();