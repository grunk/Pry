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
use Pry\Controller\Router;

require('../Pry/Pry.php');
Pry::register();

$router = Router::getInstance();
$router->setPath(ROOT_PATH.'includes/controllers/'); // Dossier où sont situés les controllers

//Ecriture de règle de routage spécifique , en plus de la règle par défaut /controller/action/param1/param2/...
$router->addRule('actualites/archives/:annee/:mois/:categorie',array('controller'=>'actualites','action'=>'index'));
$router->addRule('actualites/archives/:annee/:mois',array('controller'=>'actualites','action'=>'index'));
$router->addRule('actualites/archives/:annee/:mois/:categorie/:p',array('controller'=>'actualites','action'=>'index'));
$router->addRule('actualites/pages/:p',array('controller'=>'actualites','action'=>'index'));
$router->addRule('actualites/categorie/:categorie',array('controller'=>'actualites','action'=>'index'));
$router->addRule('actualites/categorie/:categorie/:p',array('controller'=>'actualites','action'=>'index'));

$router->load();
?>