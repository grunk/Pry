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
Pry::register();

//Utilisatio nde la factory si plusieurs validateur
$factory = new Pry\Validate\Validate();
$factory ->addValidator('Date','fr','pasune date fr');
var_dump($factory ->isValid('2004-12-12 10:15:23'));

//Utilisation direct si validateur unique
$validator = new Pry\Validate\Validator\Date('fr');
var_dump($validator->isValid('27/08/09'));
?>