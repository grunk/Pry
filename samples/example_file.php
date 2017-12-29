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

use Pry\File\FileManager;
use Pry\File\FileCSV;

require('../Pry/Pry.php');
Pry::register();

//Initialisation
/*
$file = new File_FileManager('test.txt');
//Retourne les infos du fichier
$fichier = $file->getInfo();
var_dump($fichier);

$file->open(File_FileManager::ADD); // Ouverture en mode ajout
$file->write('Ecriture de contenu');
$file->writeLine('Ecriture d\'une ligne');
$file->close();*/


/*$folder = new File_FolderManager('./');
$folder->setFilter('txt');
var_dump($folder->listFile());*/


$file = new FileManager('test/test.txt');
//Retourne les infos du fichier
$fichier = $file->getInfo();
var_dump($fichier);
/*
$file->open(File_FileManager::ADD); // Ouverture en mode ajout
for($i=0; $i < 10; $i++)
	$file->writeLine('Ligne '.$i);*/

$file->open(FileManager::READ_WRITE_ADD);
var_dump($file->insertLine('insertion',6));
var_dump($file->readLine());
$file->close();

//CSV
$csv = new FileCSV('test/test.csv');
$csv->addColumns(array('Col 1','Col2','Col3'));
$csv->addLine(array('Val11','Val12','Val13'));
$csv->addBlankLine();
$csv->addLine(array('Val21','Val22','Val23'));
$csv->addLine('Val11;Val12;Val13');
$csv->close();
 ?>