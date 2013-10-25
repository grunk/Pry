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

namespace Pry\Feed\Abstracts;

/**
 * Classe abstraite de flux.
 * @category Pry
 * @package Feed
 * @subpackage Feed_Abstract
 * @version 1.1.0
 * @abstract
 * @author Olivier ROGER <oroger.fr>
 *       
 */
abstract class Feed
{

    /**
     * Titre du flux
     * @access protected
     * @var string
     */
    protected $title;

    /**
     * Date du flux 
     * @access protected
     * @var string
     */
    protected $date;

    /**
     * Auteur du flux
     * @access protected
     * @var array
     */
    protected $author;

    /**
     * Langue du flux
     * @access protected
     * @var string
     */
    protected $lang;

    /**
     * Description du flux
     * @access protected
     * @var string
     */
    protected $description;

    /**
     * Copyright du flux
     * @access protected
     * @var string
     */
    protected $copyright;

    /**
     * Lien vers le flux
     * @access protected
     * @var string
     */
    protected $link;

    /**
     * Id du flux
     * @access protected
     * @var string
     */
    protected $id;

    public function setTitle($title)
    {
        if (empty($title) || !is_string($title))
            throw new \InvalidArgumentException('Titre de flux invalide');
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
            throw new \InvalidArgumentException('Author doit être un Array avec la clé name');

        $this->author = $author;
        return $this;
    }

    public function setLang($lang)
    {
        if (empty($lang) || !is_string($lang))
            throw new \InvalidArgumentException('Langue de flux invalide');

        $this->lang = $lang;
        return $this;
    }

    public function setDescription($desc)
    {
        if (empty($desc) || !is_string($desc))
            throw new \InvalidArgumentException('Description de flux invalide');

        $this->description = $desc;
        return $this;
    }

    public function setCopyright($cr)
    {
        if (empty($cr) || !is_string($cr))
            throw new \InvalidArgumentException('Copyright de flux invalide');

        $this->copyright = $cr;
        return $this;
    }

    public function setLink($link)
    {
        if (empty($link) || !is_string($link))
            throw new \InvalidArgumentException('Lien de flux invalide');

        $this->link = $link;
        return $this;
    }

    public function setId($id)
    {
        if (empty($id) || !is_string($id))
            throw new \InvalidArgumentException('Id de flux invalide');

        $this->id = $id;
        return $this;
    }

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
            if (!empty($key))
                return $this->author[$key];
            else
                return $this->author;
        return null;
    }

    public function getLang()
    {
        if (isset($this->lang))
            return $this->lang;
        return null;
    }

    public function getDescription()
    {
        if (isset($this->description))
            return $this->description;
        return null;
    }

    public function getCopyright()
    {
        if (isset($this->copyright))
            return $this->copyright;
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

}