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

$ua = new Pry\Util\UserAgent($_SERVER['HTTP_USER_AGENT']);
if($ua->isMobile()) { 
	echo 'Navigateur mobile<br />';
	echo $ua->showMobile();	
}

if($ua->isDesktop()) {
	echo 'isDesktop<br />';
	echo $ua->showDesktop();
}
var_dump($ua->isAndroid());
var_dump($_SERVER['HTTP_USER_AGENT']);
//var_dump(instanceof(new UnexpectedValueException));
 ?>