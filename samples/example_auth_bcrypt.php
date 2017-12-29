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
use Pry\Auth\Bcrypt;

require('../Pry/Pry.php');
Pry::register();
//Instanciation , $sql = objet sql

$bc = new Bcrypt(13);
$hash = $bc->hash("MyPassw0rD");

echo 'HASH for MyPassw0rD =  '.$hash.'<br />';
echo 'check MyPassw0rD against '.$hash.' = ';
var_dump(Bcrypt::check("MyPassw0rD",$hash));
echo 'check MyPassw0rD against azerty = ';
var_dump(Bcrypt::check("MyPassw0rD","azerty"));