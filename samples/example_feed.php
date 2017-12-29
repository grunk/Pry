<?php
use Pry\Feed\Feed;

require('../Pry/Pry.php');
Pry::register();

$feed = new Feed();
$feed->setTitle('AFNIC - Actualites')
		->setDate('2010-01-28T15:39:00Z')
		->setAuthor(array('name'=>'Communication AFNIC','email'=>'relations.presse@afnic.fr'))
		->setLang('fr')
 		->setLink('http://www.afnic.fr/')
 		->setId('http://www.afnic.fr/');

	$entry = $feed->createEntry();
	//$entry->setAuthor(array('name'=>'OR-'.$i,'email'=>'demo@demo.com'));
	$entry->setTitle('[Digipryn v5] Carré blanc sur les images');
	//$entry->setDate('2010-01-28');
	$entry->setContent('UN carré blanc apparait sur l\'interface');
	//$entry->setId($i);
	$entry->setLink('http://172.16.12.227/prynbug/base/8/voir.html');
	$entry->setId('13-link-qsdf15');
	$entry->setDate('2010-02-01T10:19:00Z');
	$feed->setEntry($entry);
	
 
  header('Content-Type: text/xml');
  echo $feed->build('Atom');

 ?>