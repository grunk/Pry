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

use Pry\Auth\ACL;

require('../Pry/Pry.php');
Pry::register();

$ACL = new ACL();
$ACL->addRole('Writer',array('read','write'));
$ACL->addPermission('Writer','delete');

if($ACL->hasPermission('write'))
 	echo 'ok';
else
	echo 'ko';
 ?>