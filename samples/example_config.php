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
use Pry\Config\Ini;
use Pry\Config\Config;

require('../Pry/Pry.php');
Pry::register();


// Config de base
try {
	$ini = new Ini('config.ini','prod');
	echo $ini->db->host;
	
} catch(Exception $e) {
	echo $e->getError();
}


// Rechargement de config depuis un export.
// Utile pour mise en cache par exemple
$array = $ini->toArray();
$ini2 = new Config($array);
var_dump($ini2->db->host);
