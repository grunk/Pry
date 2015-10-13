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
use Pry\File\FolderManager;

require('../Pry/Pry.php');
Pry::register();

//Listage de fichier avec ajout de filtre
$folder = new FolderManager('test');
//Liste unique les .txt
//$folder->setFilter(array('avi','mkv'));

var_dump(array_reverse($folder->getLastFiles(3)));


FolderManager::create('testcreate');
//Liste récursivement
//var_dump($folder->listRecursive());
//Calcul la taille du dossier et de son arborescence
//var_dump($folder->getSize());
 ?>