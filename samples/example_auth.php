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

 //Initialisation
define("INC_VENDOR_PATH", realpath(dirname(__FILE__).'../../../Pry/vendor'));
set_include_path(INC_VENDOR_PATH . PATH_SEPARATOR .  get_include_path());

use Pry\Config\Ini;
use Pry\Session\Session;
use Pry\Auth\Auth;
use Pry\Auth\Bcrypt;

require_once('../Pry/Pry.php');
Pry::register();

//Lecture fichier de config
try{
	$configIni = new Ini('config.ini','dev');
}
catch(Exception $e){
	echo $e->getError();
}


//Initialisation BDD
$sql = null;
try {
    $options = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    );
    $dbParam = $configIni->database->params->toArray();
    $sql = new PDO('mysql:host='.$dbParam['host'].';dbname='.$dbParam['dbname'].';charset=utf8',$dbParam['username'],$dbParam['password'],$options);
    $st = $sql->query('SELECT * FROM user');
    var_dump($st->fetchAll());
} catch (Exception $e) {
    echo $e->getMessage();
    echo 'error';
}

$sess 	= Session::getInstance();
$auth 	= new Auth($sess,$sql);
//On défini les paramètres 
$auth->setUserTable('user'); //Table des utilisateur
$auth->setUserField('nom'); // Champs de login
$auth->setPwdField('pwd'); // Champs de password
$auth->setAutologTokenField('tokencookie');
$auth->setHashRounds(10);
$auth->setAutoLogin(false);
$auth->setTimeOutSession(3600);

$login='oroger';
$pass = 'olivier';
//Authentification
$auth->login($login,$pass);
if(!$auth->error)
{
	echo 'identification réussie';
	var_dump($_SESSION);
}
else
{
	echo $auth->getErrorType();
}

//Méthode utile une fois logué : 
if($auth->isLogged())
{
	echo'Utilisateur connecté';
	var_dump($_SESSION);
}
else
{
	echo 'non connecté';
}

//Déconnexion
$auth->logout();


$bcrypt = new Bcrypt(10);
echo $bcrypt->hash('jgagnepain');
var_dump($bcrypt->check('olivier','$2a$10$mFMp2uVPL5WpGdNbMZpS3ur6NpY8I3vwW7yqcFrR956s.vCi1UnCC'));

?>