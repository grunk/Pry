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
use Pry\Log\Log;
use Pry\Log\Writer\File;
Pry::register();

//Defini le dossier des logs
$writer = new File('test');
//$writer2 = new Log_Writer_Bd('logs');
//Un log par jour ?
$writer->setDuration(File::MONTHLY);
//Défini un nombre de ligne max dans le fichier 0 pour illimité
$writer->setLineLimit(5);
//$writer2->setMode(Log_Writer_File::MODE_MINI);
//Ecriture du log
$log = new Log($writer);
//$log->write('message',  Log_Writer_File::CRITICAL);
$log->error('message '.date('H:i:s'));

/*$writer = new Log_Writer_Syslog('172.16.12.227');
$writer->setApp('PHP');
$writer->setFacility(1);
$writer->setSeverity(Log_Writer_Syslog::ERROR);
$log = new Log_Log($writer);
$log->write('Message test');*/
?>