<?php

define("CONF_MODE", 'dev');
ini_set('display_errors', 'on');
define("INC_VENDOR_PATH", realpath(dirname(__FILE__)) . '../../vendor');
set_include_path(INC_VENDOR_PATH . PATH_SEPARATOR . get_include_path());

use Pry\Util\Registry;

require_once "../Pry/Pry.php";
//Autoload
Pry::register();

//Config
try {
    $configIni = new Pry\Config\Ini('includes/config/config.ini', CONF_MODE);
    Registry::set('Config', $configIni);
    define('ROOT_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
} catch (Exception $e) {
    echo $e->getMessage();
}

//BDD
/*require('Zend/Loader/Autoloader.php');
Zend_Loader_Autoloader::getInstance();
try {
    $options = $configIni->database->params->toArray()
            + array('driver_options' =>
        array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8;',
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

    $sql = Zend_Db::factory($configIni->database->adapter, $options);
    $sql->getConnection();

    Registry::set('Db', $sql);
} catch (Zend_Db_Adapter_Exception $e) {
    echo $e->getError();
}*/

//Réglage horaire
date_default_timezone_set('Europe/Paris');

//Template
$myView = new Pry\View\View();
$myView->setViewBase(ROOT_PATH.'includes/view/');

//Session
$session = Pry\Session\Session::getInstance('PrynWeb3Session');

/*
$auth = new Pry\Auth\Auth($session, $sql);
$auth->setUserTable('users');
$auth->setUserField('login');
$auth->setPwdField('pass');
$auth->setHashRounds(10);
$auth->setAutoLogin(true);*/
//$auth->setOnAutoLoginEvent(new Login($sql,$auth));


$router = Pry\Controller\Router::getInstance();
$router->setPath(ROOT_PATH . 'includes/controllers/');
$router->setView($myView);

Registry::set('router', $router);
//Registry::set('auth', $auth);
$router->load();
?>