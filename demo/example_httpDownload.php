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

$file			= 'fichier.pdf';
$racine     = ROOT_PATH.'pub/upload/fichier/';
$fichier    = $racine.$file;

$http       = new HTTPDownload($fichier);
$http->setName('monfichier.pdf'); // Change le nom du fichie rque recevra l'utilisateur. fichier.pdf sur le serveur , il arrivera renommé monfichier.pdf chez le client
$http->download(); // Lance le téléchargement
?>