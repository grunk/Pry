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

use Pry\File\Decorator\Filter;
use Pry\File\Util;
use Pry\Util\Strings;

/**
 * Gestion de dossier
 * @category Pry
 * @package File
 * @version 1.5.0
 * @author Olivier ROGER <oroger.fr>
 *
 */
class FolderManager
{

    private $dossier;
    private $dossierRecur;
    private $listeFichier;
    private $listeDossier;
    private $filtre;

    /**
     * Constructeur
     *
     * @param string $path Dossier
     */
    public function __construct($path)
    {
        $this->dossier      = $path;
        if (!is_dir($this->dossier))
            throw new \InvalidArgumentException('Le dossier recherché ne semble pas exister');
        $this->dossierRecur = $this->dossier;
        $this->listeFichier = array();
        $this->folderTree = array();
        $this->fileTree = array();
        $this->tree   = '';
        $this->filtre = array();
    }

    /**
     * Liste les fichiers du dossier en fonctions des filtres
     *
     * @param boolean $humanSize Si true taille retournée sous forme lisible (5Mo) sinon en Ko
     * @return array
     */
    public function listFile($humanSize = false)
    {
        $folder   = new Filter(new \DirectoryIterator($this->dossier));
        $folder->setExtension($this->filtre);
        $compteur = 0;
        foreach ($folder as $file) {
            if (!$file->isDot() && !$file->isDir())
            {
                $this->listeFichier[$compteur]['name'] = $file->getFilename();
                if ($humanSize)
                {
                    $taille                                = $file->getSize();
                    $this->listeFichier[$compteur]['size'] = Util::getHumanSize($taille);
                }
                else
                    $this->listeFichier[$compteur]['size'] = round($file->getSize() / 1024, 3);
                $this->listeFichier[$compteur]['type'] = Util::getExtension($this->listeFichier[$compteur]['name']);
                $compteur++;
            }
        }
        sort($this->listeFichier);
        return $this->listeFichier;
    }

    /**
     * Liste récursivement un dossier
     *
     * @since 1.1.0
     * @return array
     */
    public function listRecursive($showFile = true, $showDot = true)
    {
        $folder = new Filter(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->dossier), true));
        $folder->setExtension($this->filtre);
        $liste  = array();
        $compteur = 0;
        foreach ($folder as $file) {
            if(($showFile || (!$showFile && $file->isDir()))) {
                    $name = $file->getFilename();
                    if($showDot || ($name != '.' && $name != '..')) {
                            $liste[$compteur]['name']  = $name;
                            $liste[$compteur]['size']  = round($file->getSize() / 1024, 3);
                            $liste[$compteur]['depth'] = $folder->getDepth();
                            $compteur++;
                    }
            }            
            //echo str_repeat('-',$folder->getDepth()).' '.$file.'<br />';
        }
        return $liste;
    }

    /**
     * Récupère les x derniers fichiers modifiés dans un dossier.
     * Attention à la consomation mémoire sur gros dossier
     *
     * @param int $nbFile Nombre de fichier à récupérer
     * @param boolean $humanSize Retourne la date en octet ou sous forme lisible
     * @since 1.1.9
     * @return array
     */
    public function getLastFiles($nbFile = 5, $humanSize = false)
    {
        $dossier = new Filter(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->dossier), true));
        $dossier->setExtension($this->filtre);
        $liste   = array();
        $compteur = 0;
        foreach ($dossier as $file) {
            if (!$file->isDir())
            {
                //tableau des premier fichier
                $liste[$compteur]['name'] = $file->getFilename();
                $liste[$compteur]['date'] = $file->getMTime();
                $liste[$compteur]['path'] = $file->getPathName();

                $size                     = $file->getSize();
                if ($humanSize)
                    $liste[$compteur]['size'] = Util::getHumanSize($size);
                else
                    $liste[$compteur]['size'] = round($size / 1024, 3);

                //echo str_repeat('-',$dossier->getDepth()).' '.$file.'<br />';
            }
            $compteur++;
        }
        usort($liste, array('Pry\File\FolderManager', 'triParDate'));
        array_splice($liste, $nbFile);
        return array_reverse($liste);
    }

    private static function triParDate($a, $b)
    {
        if ($a['date'] == $b['date'])
            return 0;
        return ($a['date'] > $b['date']) ? -1 : 1;
    }

    /**
     * Calcul la taille occupé par un dossier et tous ses sous dossiers
     *
     * @since 1.1.0
     * @return float
     */
    public function getSize()
    {
        $dossier = new Filter(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->dossier), true));
        $dossier->setExtension($this->filtre);
        $size    = 0;
        foreach ($dossier as $file)
            if (!$file->isDir())
                $size+=round($file->getSize() / 1024, 3);

        return $size;
    }

    /**
     * Calcul la taille occupé par un dossier et tous ses sous dossiers. Version static
     * Non soumis aux filtres
     *
     * @since 1.1.6
     * @param Dossier à mesurer
     * @static
     * @return float
     */
    public static function getSizeStatic($folder)
    {
        if (!is_dir($folder))
            throw new \InvalidArgumentException('Le dossier ne semble pas exister');
        $dossier = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folder), true);
        $size    = 0;
        foreach ($dossier as $file)
            if (!$file->isDir())
                $size+=round($file->getSize() / 1024, 3);

        return $size;
    }

    /**
     * Liste les dossiers présents
     * @since 1.0.5
     * @return unknown
     */
    public function liste()
    {
        $folder   = new \DirectoryIterator($this->dossier);
        $compteur = 0;
        foreach ($folder as $file) {
            if (!$file->isDot() && $file->isDir())
            {
                $this->listeDossier[$compteur] = $file->getFilename();
                $compteur++;
            }
        }
        return $this->listeDossier;
    }

    /**
     * Ajoute un ou plusieurs filtres
     *
     * @param mixed $filter Filtres à lister
     */
    public function setFilter($filter)
    {
        if (!is_array($filter))
            $this->filtre[] = $filter;
        else
            $this->filtre   = array_merge($this->filtre, $filter);
    }

    /**
     * Supprime un filtre
     *
     * @param string $filter
     */
    public function rmFilter($filter)
    {
        $cle = array_search($filter, $this->filtre);
        if ($cle != FALSE)
            unset($this->filtre[$cle]);
        else
            throw new \InvalidArgumentException('Le filtre demandé n\'existe pas');
    }

    /**
     * Crée un dossier
     *
     * @param string $newDir Chemin vers le dossier
     * @param int $chmod Chmod en octal
     */
    public static function create($newDir, $chmod = 0644)
    {
        if (!is_file($newDir))
            mkdir(Strings::clean($newDir), $chmod);
    }

    /**
     * Supprime un dossier et tout son contenu
     *
     * @param string $dir Dossier à supprimer
     */
    public function remove($dir = '')
    {
        if ($dir == '')
            $dir = $this->dossier;

        $folder = new \RecursiveDirectoryIterator($dir);
        while ($folder->valid()) {
            if ($folder->isDir() && !$folder->isDot())
            {
                if ($folder->hasChildren())
                    self::remove($folder->getPathName());
            }
            elseif ($folder->isFile() && !$folder->isDot())
            {
                unlink($folder->getPathname());
            }
            $folder->next();
        }
        $folder = null; // Libère l'itérateur sinon erreur d'accès lors de la suppression
        rmdir($dir);
        //echo "rmdir($dir)<br />";
    }

    /**
     * Supprime uniquement les fichiers présents dans un dossier
     * @since 1.2.0
     */
    public function removeFiles()
    {

        $folder = new Filter(new \DirectoryIterator($this->dossier));
        $folder->setExtension($this->filtre);
        foreach ($folder as $file) {
            if ($file->isFile() && !$file->isDot())
            {
                unlink($file->getPathname());
            }
        }
        $folder = null; // Libère l'itérateur sinon erreur d'accès lors de la suppression
    }
    
    /**
     * Delete file if older than $ageInSec
     * @param int $ageInSec age in sec
     * @since 1.5.0
     */
    public function removeFilesSince($ageInSec)
    {
        $folder = new Filter(new \DirectoryIterator($this->dossier));
        $folder->setExtension($this->filtre);
        foreach ($folder as $file) {
            if ($file->isFile() && !$file->isDot() && time() - $file->getCTime() > $ageInSec)
            {
                unlink($file->getPathname());
            }
        }
        $folder = null;
    }

    /**
     * Copie du contenu d'un dossier dans un autre
     *
     * @since 1.1.5
     * @todo Proposer en option la copie du dossier et non pas que du contenu
     * @param string $dest Dossier de destination
     */
    public function copy($dest, $dir = '')
    {
        if (empty($dest))
            throw new \UnexpectedValueException('Le dossier de destination ne peut être vide');
        if (!is_dir($dest))
            self::create($dest, 0777);

        if (empty($dir))
            $dir = $this->dossier;

        $folder = new \RecursiveDirectoryIterator($dir);
        while ($folder->valid()) {
            if ($folder->isDir() && !$folder->isDot())
            {
                if ($folder->hasChildren())
                {
                    $tmpPath = $dest . $folder->getFileName();
                    if (!is_dir($tmpPath))
                        self::create($tmpPath, 0777);
                    $this->copy($dest, $folder->getPathname());
                }
            }
            elseif ($folder->isFile() && !$folder->isDot())
            {
                $src = $folder->getPathname();
                //Suppression du nom du dossier source.
                $pos = stripos($src, DIRECTORY_SEPARATOR);
                copy($src, $dest . substr($src, $pos + 1));
            }
            $folder->next();
        }
    }

    /**
     * Zip tous les fichiers du dossier.
     * Prend en compte les éventuels filtres
     * @param string $fileName Nom de l'archive
     * @since 1.2.2
     */
    public function ZipFiles($fileName)
    {
        if (extension_loaded('zip'))
        {
            $this->listFile();
            $zip = new \ZipArchive;
            $res = $zip->open($this->dossier . $fileName, \ZipArchive::CREATE);
            if ($res === TRUE)
            {
                foreach ($this->listeFichier as $file) {
                    $zip->addFile($this->dossier . $file['name'], $file['name']);
                }
                $zip->close();
            }
            else
            {
                throw new \RuntimeException('Impossible de créer le zip');
            }
        }
        else
        {
            throw new \RuntimeException('Extension Zip non activée. Impossible de compresser les fichiers');
        }
    }

}