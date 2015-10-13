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
use Pry\Auth\Util;

require('../Pry/Pry.php');
Pry::register();
//Instanciation , $sql = objet sql

echo Util::passwordComplexity('m0!d3P4$sCoMpleX');


?>