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

namespace Pry\Feed\Writers;

use Pry\Feed\Feed;

/**
 * Writer de flux au format RSS.
 * @category Pry
 * @package Feed
 * @subpackage Feed_Writers
 * @version 1.1.1
 * @author Olivier ROGER <oroger.fr>
 *       
 */
class Rss implements Interfaces
{

    /**
     * Objet feed
     * @access private
     * @var Feed_Feed
     */
    private $feed;

    /**
     * Différentes entrées
     * @access private
     * @var array
     */
    private $entries;

    /**
     * Objet Document DOM
     * @access private
     * @var DOMDocument
     */
    private $DOM;

    /**
     * Fichier de destination. Si null flux affiché directement
     * @access private
     * @var string
     */
    private $file;

    /**
     * Constructeur. Initialise la construction du flux
     * @param Feed_Feed $feed Objet Feed
     * @param string $fichier Fichier (optionnel)
     */
    public function __construct(Feed $feed, $fichier = null)
    {
        $this->feed    = $feed;
        $this->entries = $feed->getEntries();
        $this->DOM     = new \DOMDocument('1.0', 'UTF-8');
        $this->file    = $fichier;

        $rss = $this->DOM->createElement('rss');
        $rss->setAttribute('version', '2.0');
        $this->DOM->appendChild($rss);

        $channel = $this->DOM->createElement('channel');
        $rss->appendChild($channel);

        $this->buildFeedInfo($channel);
        $this->buildItems($channel);
    }

    /**
     * Construit le début du flux avec les informations qui lui sont propres
     * @param DOMElement $channel
     * @access private
     * @return void
     */
    private function buildFeedInfo($channel)
    {
        $title = $this->DOM->createElement('title', $this->feed->getTitle());
        $link  = $this->DOM->createElement('link', $this->feed->getLink());
        $desc  = $this->DOM->createElement('description');
        $cdata = $this->DOM->createCDATASection($this->feed->getDescription());
        $lang  = $this->DOM->createElement('language', $this->feed->getLang());
        $date  = $this->DOM->createElement('lastBuildDate', $this->feed->getDate());
        $copy  = $this->DOM->createElement('copyright', $this->feed->getCopyright());

        $channel->appendChild($title);
        $channel->appendChild($link);
        $desc->appendChild($cdata);
        $channel->appendChild($desc);
        $channel->appendChild($lang);
        $channel->appendChild($date);
        $channel->appendChild($copy);
    }

    /**
     * Construit les éléments du flux
     * @param DOMElement $channel
     * @access private
     * @return void
     */
    private function buildItems($channel)
    {
        if (!empty($this->entries))
        {
            foreach ($this->entries as $entry) {
                $item  = $this->DOM->createElement('item');
                $channel->appendChild($item);
                $title = $this->DOM->createElement('title', $entry->getTitle());
                $link  = $this->DOM->createElement('link', $entry->getLink());
                $desc  = $this->DOM->createElement('description');
                $cdata = $this->DOM->createCDATASection($entry->getContent());
                $desc->appendChild($cdata);
                $item->appendChild($title);
                $item->appendChild($link);
                $item->appendChild($desc);

                //Options
                $eAuthor = $entry->getAuthor('name');
                if (!empty($eAuthor))
                {
                    $author = $this->DOM->createElement('author', $eAuthor);
                    $item->appendChild($author);
                }

                $eDate = $entry->getDate();
                if (!empty($eDate))
                {
                    $date = $this->DOM->createElement('pubDate', $eDate);
                    $item->appendChild($date);
                }

                $eCom = $entry->getComments();
                if (!empty($eCom))
                {
                    $com = $this->DOM->createElement('comments', $eCom);
                    $item->appendChild($com);
                }

                $eId = $entry->getId();
                if (!empty($eId))
                {
                    $guid = $this->DOM->createElement('guid', $eId);
                    $item->appendChild($guid);
                }
            }
        }
    }

    /**
     * Conversion d'un timestamp ou d'une date mysql au format RFC 2822
     * @param mixed $date Date au format timestamp mysql ou RFC 2822
     * @return string
     */
    private function convertDate($date)
    {
        if (is_int($date))
        { //Timestamp
            return date('r', $date);
        }
        elseif (preg_match('`^(\d{4}|\d{2})-\d{1,2}-\d{1,2} \d{1,2}:\d{1,2}:\d{1,2}$`', $date))
        { //Date mysql
            $datetime = explode(' ', $date);
            list($annee, $mois, $jour) = explode('/', $datetime[0]);
            list($heure, $min, $sec) = explode(':', $datetime[1]);
            return date('r', mktime($heure, $min, $sec, $mois, $jour, $annee));
        }
        else
        {
            return $date;
        }
    }

    /**
     * (non-PHPdoc)
     * @see Feed/Writers/Feed_Writers_Interface#finalize()
     */
    public function finalize()
    {
        if (!empty($this->file))
        {
            if (file_exists($this->file) && is_writable($this->file))
                return $this->DOM->save($this->file);
            else
                throw new \Exception('Impossible d\'ecrire dans le fichier');
        }
        else
        {
            return $this->DOM->saveXML();
        }
    }

}