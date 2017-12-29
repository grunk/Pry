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

require('../Pry/Pry.php');
use Pry\Net\Socket;
Pry::register();

$s = new Socket();
$s->connect('localhost',81);
$s->write('hello');
$s->disconnect();


?>