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
use Pry\Net\HTTPDownload;

Pry::register();

$file			= 'test.csv';
$racine     = 'test/';
$fichier    = $racine.$file;

$http       = new HTTPDownload($fichier);
$http->setName('myfile.csv'); // Change the name of the downloaded file.
$http->download(); // Start download
?>