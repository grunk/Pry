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
// Construit une représentation intervallaire dans la base category puis l'affiche
use Pry\Db\NestedTree;

require('../Pry/Pry.php');
Pry::register();

$inter = new NestedTree('category');
/*$inter->addRootElement('Electronics');
$inter->setCurrent(1);
$inter->addChild('Television');
$inter->addChild('Portable electronics');

$inter->setCurrent(2);
$inter->addChild('tube');
$inter->addChild('LCD');
$inter->addChild('Plasma');

$inter->setCurrent(3);
$inter->addChild('Mp3 player');
$inter->addChild('Cd player');
$inter->addChild('Radio');

$inter->setCurrent(7);
$inter->addChild('Flash');
$inter->addChild('Hdd');

$inter->setCurrent(1);
$inter->addChild('Gadget');
$inter->addChild('Portable electronics 2');*/
 ?>