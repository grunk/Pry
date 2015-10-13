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
require('../Pry/Pry.php');
Pry::register();

/*$chaine = Pry\Util\Strings::generate(10);
echo 'Chaine : '.$chaine.'<br >';
echo 'Force  : '.Pry\Auth\Util::passwordComplexity($chaine);

echo Pry\Util\Strings::date2Mysql("12/01/2011", "d/m/Y");
echo '<br />';
echo Pry\Util\Strings::date2Mysql("01/12/2011", "m/d/Y");
echo '<br />';

echo Pry\Util\Strings::clean("Ceci était une chaine comPlexe ! l'est modifié par le script ? *ù$^éèçà@");

var_dump(Pry\Util\Strings::isMail('email@domaincom',false));

$p = new Pry\Util\Pagination(1000,2);
var_dump($p->create());*/

$CB = new Pry\Util\CommandLineBuilder();
/*
$CB->setCommand('svn');
$CB->addParameter('log');
$CB->addParameter('http://192.168.1.1/test/trunk');
$CB->addOption('test');
$CB->addOption('test2','data');
$CB->addLongOption('username','oroger');*/
	
$CB = new \Pry\Util\CommandLineBuilder();
$CB->setCommand("PsExec.exe \\\srverp");
$CB->addParameter("");
$CB->addOption("i");
$CB->addOption("s");
$CB->addParameter("D:\\CEGID\PrynODC\\TEB\\SOFTWAREs\\TIERS_CREA.bat");

echo $CB->get('OP');

//var_dump(Pry\Util\Environment::getIP());

//var_dump(instanceof(new UnexpectedValueException));

//echo Pry\Util\Strings::geekize("Bonjour monsieur");
 ?>