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
use Pry\Date\Ferie;
use Pry\Date\Weeks;

require('../Pry/Pry.php');
Pry::register();

$date = new Weeks(date('W'),2012);
var_dump($date->getBoundaries());
var_dump($date->computeDays());
var_dump($date->computeNextPrev());
var_dump($date->getMysqlDays());


$ferie = new Ferie(2012);
var_dump($ferie->isFerie('2012-12-25'));
var_dump($ferie->getDays());
 ?>