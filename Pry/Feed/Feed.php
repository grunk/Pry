<?php

/**
 * Pry Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * 
 */

namespace Pry\Feed;

use Pry\Feed\Abstracts\Feed as AbstractFeed;

/**
 * Gestion de la création de flux.
 * <code>
 * $feed = new Feed();
 * $feed->setTitle('Flux base de connaissance')
 * 		->setDate('Fri, 29 Jan 2010 09:27:22 +0100')
 * 		->setAuthor(array('name'=>'OR','email'=>'oroger@prynel.com'))
 * 		->setDescription('Derniers éléments de la base de connaissance Prynel')
 * 		->setCopyright('prynel')
 * 		->setLink('http://172.16.12.227/prybug/');
 *
 * 	$entry = $feed->createEntry();
 * 	//$entry->setAuthor(array('name'=>'OR-'.$i,'email'=>'oroger@prynel.com'));
 * 	$entry->setTitle('[Digipryn v5] Carré blanc sur les images');
 * 	//$entry->setDate('2010-01-28');
 * 	$entry->setContent('UN carré blanc apparait sur l\'interface');
 * 	//$entry->setId($i);
 * 	$entry->setLink('http://172.16.12.227/prynbug/base/8/voir.html');
 * 	$feed->setEntry($entry);
 * 	
 * 
 *  header('Content-Type : application/xml; charset=utf-8');
 *  // $feed->build('Rss','file.xml');
 *  echo $feed->build('Rss');
 * </code>
 * @category Pry
 * @package Feed
 * @version 1.1.0 
 * @author Olivier ROGER <oroger.fr>
 *       
 */
class Feed extends AbstractFeed
{

    /**
     * Tableau des items du flux
     * @access private
     * @var array
     */
    private $entries;

    public function __construct()
    {
        $this->author = array();
        $this->entries = array();
        $this->date = time();
        $this->lang = 'fr-Fr';
    }

    /**
     * Créer un élément de flux
     * @return Feed_Entry
     */
    public function createEntry()
    {
        return new Entry();
    }

    /**
     * Enregistre l'élément créé dans le flux
     * @param Feed_Entry $entry
     * @return void
     */
    public function setEntry($entry)
    {
        $this->entries[] = $entry;
    }

    /**
     * Récupère tous les éléments du flux
     * @return array
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * Construit le flux avec les infos et les éléments
     * @param string $type Type de flux (Rss, Atom ...)
     * @param string $file Chemin vers le fichier (optionnel)
     * @return string|int
     */
    public function build($type, $file = null)
    {
        $class = 'Pry\Feed\Writers\\' . $type;
        if (class_exists($class))
        {
            $build = new $class($this, $file);
            return $build->finalize();
        }

        throw new \InvalidArgumentException('Le type ' . $type . ' ne semble pas supporté');
    }

}