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

namespace Pry\File;

use Pry\File\Util;

/**
 * Gestion d'accès au fichier
 * @category Pry
 * @package File
 * @version 1.2.0
 * @author Olivier ROGER <oroger.fr>      
 *
 */
class FileManager
{

    const READ             = 'r';
    const READ_BYTE        = 'rb';
    const READ_WRITE       = 'r+';
    const WRITE            = 'w';
    const WRITE_BYTE       = 'wb';
    const READ_WRITE_RESET = 'w+';
    const ADD              = 'a';
    const READ_WRITE_ADD   = 'a+';

    /**
     * Ressource de fichier
     * @access private
     * @var SplFileObject
     */
    private $fileHandler = null;

    /**
     * Information de fichier
     * @var SplFileInfo
     */
    private $fileInfo;

    /**
     * Chemin vers le fichier d'origine
     * @access protected
     * @var string
     */
    protected $pathToFile;

    /**
     * Détermine is un fichier doit être réecris ou non
     * @access public
     * @var boolean
     */
    public $overwrite;

    /**
     * Constructeur
     *
     * @param string $file chemin vers le fichier
     */
    public function __construct($file)
    {
        $this->pathToFile = $file;
        $this->fileInfo   = new \SplFileInfo($this->pathToFile);
        $this->overwrite  = true;
    }

    /**
     * Vérifie si le fichier est un fichier
     *
     * @return boolean
     */
    public function isFile()
    {
        if ($this->fileInfo->isFile())
            return true;
        else
            return false;
    }

    /**
     * Récupère les information du fichier
     *
     * @return array
     */
    public function getInfo()
    {
        $arrInfo = array();
        if ($this->fileInfo->isFile())
        {
            $arrInfo['extension'] = Util::getExtension($this->pathToFile);
            $arrInfo['name']      = Util::getNameFromPath($this->pathToFile);
            $arrInfo['size']      = $this->fileInfo->getSize();
            $arrInfo['type']      = 'file';
        }

        return $arrInfo;
    }

    /**
     * Retourne l'objet file info
     * @return SplFileInfo
     */
    public function getFileInfo()
    {
        return $this->fileInfo;
    }

    /**
     * Ouvre le fichier demandé
     *
     * @param string $mode Mode d'ouverture
     * @return boolean
     */
    public function open($mode)
    {
        if (file_exists($this->pathToFile) || ($mode != self::READ && $mode != self::READ_WRITE))
        {
            if ($this->isFile() || ($mode != self::READ && $mode != self::READ_WRITE))
            {
                try {
                    $this->fileHandler = $this->fileInfo->openFile($mode);
                    if ($this->fileHandler != null)
                        return true;
                } catch (\RuntimeException $e) {
                    return false;
                }

                return false;
            }
            else
            {
                throw new \InvalidArgumentException($this->pathToFile . ' n\'est pas un fichier valide');
            }
        }
        else
        {
            throw new \InvalidArgumentException('Le fichier ' . $this->pathToFile . ' n\'existe pas');
        }
    }

    /**
     * Ferme le fichier
     *
     * @return boolean
     */
    public function close()
    {
        if ($this->fileHandler != null)
        {
            $this->fileHandler = null;
            return true;
        }
        return false;
    }

    /**
     * Lit le contenu d'un fichier
     *
     * @return mixed
     */
    public function read()
    {
        $datas = "";
        if ($this->fileHandler == null)
            $this->open(self::READ);
        if ($this->fileInfo->isReadable())
        {
            $this->fileHandler->rewind();
            while (!$this->fileHandler->eof())
                $datas .= $this->fileHandler->fgets();

            return $datas;
        }

        return false;
    }

    /**
     * Lit un fichier ligne par ligne
     *
     * @param int $line2read Ligne à lire dans le fichier
     * @return mixed
     */
    public function readLine($line2read = null)
    {
        if ($this->fileHandler == null)
            $this->open(self::READ);

        $this->fileHandler->rewind();

        if ($line2read == null)
        {
            $arrLine = array();
            foreach ($this->fileHandler as $lineNumber => $content)
                $arrLine[$lineNumber] = $content;

            return $arrLine;
        }
        else
        {
            $line2read -= 1; //ITérateur commence à 0;
            $this->fileHandler->seek($line2read);
            return $this->fileHandler->current();
        }
        return false;
    }

    /**
     * Recherche si une chaine est présente dans un fichier
     *
     * @param string $toSearch
     * @return mixed
     */
    public function search($toSearch)
    {
        $search = $this->read();
        return stripos($search, $toSearch);
    }

    /**
     * Ecrit dans le fichier
     *
     * @param string $contenu
     * @return mixed False/null si erreur , nombre d'octet sinon
     */
    public function write($contenu)
    {
        if ($this->fileHandler != null && $this->fileInfo->isWritable())
        {
            return $this->fileHandler->fwrite($contenu);
        }
        return false;
    }

    /**
     * Ecrit une ligne
     *
     * @param string $ligne Ligne à écrire
     * @param string $endLine caractère de fin de ligne
     * @return mixed False si erreur , nombre d'octec sinon
     */
    public function writeLine($line, $endLine = "\n")
    {
        if ($this->fileHandler != null && $this->fileInfo->isWritable())
        {
            return $this->fileHandler->fwrite($line . $endLine);
        }
        return false;
    }

    /**
     * Insert une ligne dans un fichier à l'endroit voulu
     *
     * @param string $line Ligne à écrire
     * @param int $numLineInsert Indice de ligne pour insérer la nouvelle ligne
     */
    public function insertLine($line, $numLineInsert)
    {
        $lignes = $this->readLine();

        if ($lignes && $this->fileHandler != null && $this->fileInfo->isWritable())
        {
            array_splice($lignes, $numLineInsert, 0, $line . "\n");
            file_put_contents($this->pathToFile, join("", $lignes));
        }
    }

    /**
     * Copie un fichier vers un dossier
     *
     * @param string $pathToCopy Chemin complet du fichier de copié
     * @param boolean $ecrase
     * @return unknown
     */
    public function copy($pathToCopy)
    {
        $pathToCopy = $this->preparePath($pathToCopy, 'copy');
        if (!$this->overwrite)
        {
            if (!file_exists($pathToCopy))
            {
                return false;
            }
            else
            {
                return copy($this->pathToFile, $pathToCopy);
            }
        }
        else
        {
            return copy($this->pathToFile, $pathToCopy);
        }
    }

    /**
     * Déplace un fichier vers un dossier
     *
     * @param string $pathToMove Chemin complet du fichier une fois déplacé
     * @return boolean
     */
    public function move($pathToMove)
    {
        $pathToMove = $this->preparePath($pathToMove, 'copy');
        if (!$this->overwrite)
        {
            if (file_exists($pathToMove))
            {
                return false;
            }
            else
            {
                return rename($this->pathToFile, $pathToMove);
            }
        }
        else
        {
            if (file_exists($pathToMove))
                unlink($pathToMove);
            return rename($this->pathToFile, $pathToMove);
        }
    }

    /**
     * Supprime le fichier
     */
    public function delete()
    {
        $this->close();
        @unlink($this->pathToFile);
    }

    /**
     * Prépare le path donné pour être correct (slash, espace ...)
     *
     * @param string $path
     * @param string $mode Mode de préparation (copy,move ...)
     * @return string
     */
    private function preparePath($path, $mode)
    {
        $path = trim($path);
        switch ($mode)
        {
            case 'copy' :
                if ($path[0] == '/')
                {
                    echo $path = substr($path, 1);
                }
                break;
        }
        return $path;
    }

    /**
     * Ajoute des données à un fichier existant
     * @since 1.0.6
     * @param string $data
     */
    public function append($data)
    {
        if ($this->fileHandler == null)
            return file_put_contents($this->pathToFile, $data, FILE_APPEND);
        else
            throw new \Exception('Fichier déjà ouvert, utilisez write()');
    }

    /**
     * Change les permissions d'un fichier
     * @since 1.0.6
     * @param octal $octal Droits en octal : 0644; 0777 ...
     */
    public function changePermission($octal)
    {
        chmod($this->pathToFile, $octal);
    }

    /**
     * Retourne la date de dernière modification
     * @since 1.0.6
     * @param string $dateType Format de la date retour. Timestamp si null
     * @return mixed
     */
    public function getLMTime($dateType = null)
    {
        $time = $this->fileInfo->getMTime();
        if ($time)
        {
            if ($dateType)
                return date($dateType, $time);
            return $time;
        }
        return false;
    }

    public function __destruct()
    {
        $this->close();
    }

}

?>