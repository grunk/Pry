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

/**
 * Classe représentant un élément de flux.
 * @category Pry
 * @package Feed
 * @version 1.1.0 
 * @author Olivier ROGER <oroger.fr>
 *       
 */
class Entry
{

    /**
     * Id de l'élément
     * @access private
     * @var string
     */
    private $id;

    /**
     * Titre de l'élément
     * @access private
     * @var string
     */
    private $title;

    /**
     * Date de l'élément
     * @access private
     * @var string
     */
    private $date;

    /**
     * Auteur de l'élément
     * @access private
     * @var array
     */
    private $author;

    /**
     * Contenu de l'élément
     * @access private
     * @var string
     */
    private $content;

    /**
     * Lien de l'élément
     * @access private
     * @var string
     */
    private $link;

    /**
     * Liens vers les commentaires
     * @access private
     * @var string
     */
    private $comments;

    public function __construct()
    {
        $this->author = array();
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    public function setAuthor(array $author)
    {
        if (!is_array($author) || !key_exists('name', $author))
            throw new \InvalidArgumentException('Author doit être un Array');
        $this->author = $author;
        return $this;
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setComments($com)
    {
        $this->comments = $com;
    }

    //Getter

    public function getTitle()
    {
        if (isset($this->title))
            return $this->title;
        return null;
    }

    public function getDate()
    {
        if (isset($this->date))
            return $this->date;
        return null;
    }

    public function getAuthor($key = null)
    {
        if (isset($this->author))
            if (!empty($key) && isset($this->author[$key]))
                return $this->author[$key];
            else
                return $this->author;
        return null;
    }

    public function getContent()
    {
        if (isset($this->content))
            return $this->content;
        return null;
    }

    public function getLink()
    {
        if (isset($this->link))
            return $this->link;
        return null;
    }

    public function getId()
    {
        if (isset($this->id))
            return $this->id;
        return null;
    }

    public function getComments()
    {
        if (isset($this->comments))
            return $this->comments;
        return null;
    }

}