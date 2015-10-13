<?php
/**
 *
 * @package
 * @version
 * @author Olivier ROGER <roger.olivier@gmail.com>
 * @copyright  2007-2010 Prynel
 *
 */
define("INC_CORE_PATH", realpath(dirname(__FILE__).'/../vendor'));
set_include_path(INC_CORE_PATH . PATH_SEPARATOR . get_include_path());

require('Twig/Autoloader.php');

require('../Pry/Pry.php');
Pry::register();

//Config Standard
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('tpl/');
$twig 	= new Twig_Environment($loader, array(
  'cache' 		=> 'tpl/cache',
  'auto_reload'	=> false,
  'trim_blocks' => true
));

//Ajout d'extension

//$twig->addExtension(new Twig_Extensions_Extension_I18n);
//$twig->addExtension(new Twig_Extensions_Extension_Text);

//Variable + affichage
$view['hello'] = 'world';
$template = $twig->loadTemplate('test.html');
$template->display($view);


?>
