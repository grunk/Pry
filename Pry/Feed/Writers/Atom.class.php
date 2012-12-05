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
 * Writer de flux au format Atom.
 * @category Pry
 * @package Feed
 * @subpackage Feed_Writers
 * @version 1.1.1
 * @author Olivier ROGER <oroger.fr>
 *       
 */
class Atom implements Interfaces
{

    /**
     * Objet feed
     * @access private
     * @var Feed
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

        $atom = $this->DOM->createElement('feed');
        $atom->setAttribute('xmlns', 'http://www.w3.org/2005/Atom');
        $atom->setAttribute('xml:lang', $feed->getLang());
        $this->DOM->appendChild($atom);

        $this->buildFeedInfo($atom);
        $this->buildItems($atom);
    }

    /**
     * Construit le début du flux avec les informations qui lui sont propres
     * @param DOMElement $atom
     * @access private
     * @return void
     */
    private function buildFeedInfo($atom)
    {
        $id    = $this->DOM->createElement('id', $this->feed->getId());
        $title = $this->DOM->createElement('title', $this->feed->getTitle());
        $date  = $this->DOM->createElement('updated', $this->convertDate($this->feed->getDate()));

        $atom->appendChild($id);
        $atom->appendChild($title);
        $atom->appendChild($date);

        $dLink = $this->feed->getLink();
        if (!empty($dLink))
        {
            $link = $this->DOM->createElement('link');
            $link->setAttribute('rel', 'alternate');
            $link->setAttribute('href', $dLink);
            $atom->appendChild($link);
        }

        $eAuthor = $this->feed->getAuthor();
        if (!empty($eAuthor))
        {
            $author = $this->DOM->createElement('author');
            $atom->appendChild($author);
            if (key_exists('name', $eAuthor))
            {
                $name = $this->DOM->createElement('name', $eAuthor['name']);
                $author->appendChild($name);
            }

            if (key_exists('email', $eAuthor))
            {
                $email = $this->DOM->createElement('email', $eAuthor['email']);
                $author->appendChild($email);
            }

            if (key_exists('uri', $eAuthor))
            {
                $uri = $this->DOM->createElement('uri', $eAuthor['uri']);
                $author->appendChild($uri);
            }
        }
    }

    /**
     * Construit les éléments du flux
     * @param DOMElement $channel
     * @access private
     * @return void
     */
    private function buildItems($atom)
    {
        if (!empty($this->entries))
        {
            foreach ($this->entries as $entry) {
                $item = $this->DOM->createElement('entry');
                $atom->appendChild($item);

                $id    = $this->DOM->createElement('id', $entry->getId());
                $title = $this->DOM->createElement('title', $entry->getTitle());
                $date  = $this->DOM->createElement('updated', $this->convertDate($entry->getDate()));

                $item->appendChild($id);
                $item->appendChild($title);
                $item->appendChild($date);

                $eAuthor = $entry->getAuthor();
                if (!empty($eAuthor))
                {
                    $author = $this->DOM->createElement('author', $eAuthor);
                    $item->appendChild($author);
                    if (key_exists('name', $eAuthor))
                    {
                        $name = $this->DOM->createElement('name', $eAuthor['name']);
                        $author->appendChild($name);
                    }

                    if (key_exists('email', $eAuthor))
                    {
                        $email = $this->DOM->createElement('email', $eAuthor['email']);
                        $author->appendChild($email);
                    }

                    if (key_exists('uri', $eAuthor))
                    {
                        $uri = $this->DOM->createElement('uri', $eAuthor['uri']);
                        $author->appendChild($uri);
                    }
                }

                $dContent = $entry->getContent();
                if (!empty($dContent))
                {
                    //$content = $this->DOM->createElement('content',);
                    $content = $this->DOM->createElement('content');
                    $cdata   = $this->DOM->createCDATASection($dContent);
                    $content->appendChild($cdata);
                    $item->appendChild($content);
                }

                $dLink = $entry->getLink();
                if (!empty($dLink))
                {
                    $link = $this->DOM->createElement('link');
                    $link->setAttribute('href', $dLink);
                    $item->appendChild($link);
                }
            }
        }
    }

    /**
     * Conversion d'un timestamp ou d'une date mysql au format RFC 3339
     * @param mixed $date Date au format timestamp mysql ou RFC 3339
     * @return string
     */
    private function convertDate($date)
    {
        if (is_int($date))
        { //Timestamp
            return date('c', $date);
        }
        elseif (preg_match('`^(\d{4}|\d{2})-\d{1,2}-\d{1,2} \d{1,2}:\d{1,2}:\d{1,2}$`', $date))
        { //Date mysql
            $datetime = explode(' ', $date);
            list($annee, $mois, $jour) = explode('/', $datetime[0]);
            list($heure, $min, $sec) = explode(':', $datetime[1]);
            return date('c', mktime($heure, $min, $sec, $mois, $jour, $annee));
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

?>