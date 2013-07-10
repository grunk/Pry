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

use Pry\Util\Strings;
use Pry\File\Util;

/**
 * Classe d'envoi de multiples fichiers.
 * En option utilisation de finfo ou mimemagic
 *
 * <code>
 * $up = new File_Upload('dossier/uplaod/fichier','nomchamps',modeType);
 * $up->setMaxFileSize('5M')->setWriteMode(2);
 * $error = $up->upload();
 * if(!empty($error)) $up->getSummary(); else foreach(errors as $key=>$err) echo $err;
 * </code>
 * 
 * @category Pry
 * @package File
 * @version 2.1.1
 * @author Olivier ROGER <oroger.fr>
 */
class Upload
{

    const MIME_CHECK_BROWSER  = 1;
    const MIME_CHECK_FINFO    = 2;
    const MIME_CHECK_MIMETYPE = 3;
    const MIME_CHECK_NONE     = 4;
    const REQUIRE_ALL = 1;
    const REQUIRE_YES = 2;
    const REQUIRE_NO  = 3;
    const WMODE_OVERWRITE = 1;
    const WMODE_COPY      = 2;
    const WMODE_CANCEL    = 3;

    /**
     * Taille max en octet du fichier
     * @var int
     */
    private $maxFileSize;

    /**
     * Dossier de destination des fichiers
     * @var string
     */
    private $uploadDir;

    /**
     * Chemin vers le fichier magicmime
     * @var string
     */
    private $magicFile;

    /**
     * Type de vérification mime
     * @var int
     */
    private $mimeCheck;

    /**
     * Mode d'écriture pour les fichiers envoyés
     * @var int
     */
    private $writeMode;

    /**
     * Mode sécurisé ou non
     * @var boolean
     */
    private $secureMode;

    /**
     * Fichier requis ou non
     * @var int
     */
    private $required;

    /**
     * Liste des extensions autorisées
     * @var array
     */
    private $extensions;

    /**
     * Liste des types mime autorisés
     * @var array
     */
    private $mime;

    /**
     * Nom des champs d'upload
     * @var string
     */
    private $fieldName;

    /**
     * Nom des fichiers
     * @var string
     */
    private $fileName;

    /**
     * Nom de fichier nettoyé ou non
     * @var boolean
     */
    private $cleanName;

    /**
     * Préfix du nom de fichier
     * @var string
     */
    private $prefix;

    /**
     * Suffix du nom de fichier
     * @var <string
     */
    private $suffix;

    /**
     * Résumé des fichiers envoyés
     * @var array
     */
    private $uploadedFiles;

    /**
     * Erreur rencontrée
     * @var mixed
     */
    private $errors;

    /**
     * Constructeur
     * @param string $dir Dossier de destination des fichiers
     * @param string $fieldName Nom des champs d'upload
     * @param int [$mimeCheck] Mode de v�rification du type (d�faut navigateur)
     */
    public function __construct($dir, $fieldName, $mimeCheck = 1)
    {
        $this->maxFileSize = str_replace('M', '', ini_get('upload_max_filesize')) * 1024 * 1024;
        $this->uploadDir   = $this->checkPath($dir);
        $this->magicFile   = '';
        $this->mimeCheck   = intval($mimeCheck);
        $this->writeMode   = self::WMODE_OVERWRITE;
        $this->required    = self::REQUIRE_NO;
        $this->extensions  = array('jpg', 'png', 'gif', 'zip', 'rar', 'avi', 'wmv', 'mpg', 'pdf', 'doc', 'docx', 'xls', 'txt');
        $this->mime = array('image/gif', 'image/jpeg', 'image/png', 'text/plain', 'application/pdf');
        $this->fieldName     = $fieldName;
        $this->fileName      = '';
        $this->fileNameTmp   = '';
        $this->prefix        = '';
        $this->suffix        = '';
        $this->uploadedFiles = array();
        $this->secureMode = false;
        $this->cleanName  = false;
        $this->errors     = array();
    }

    /**
     * Déclenche l'envoi de fichier
     * @return array
     */
    public function upload()
    {
        if (!isset($_FILES[$this->fieldName]['tmp_name']))
            throw new \UnexpectedValueException('Erreur : Aucune données de fichier');

        $nbFile = count($_FILES[$this->fieldName]['tmp_name']);

        for ($i = 0; $i < $nbFile; $i++) {
            $this->fileNameTmp = '';
            $this->_taille     = $_FILES[$this->fieldName]['size'][$i];
            $this->_nom        = $_FILES[$this->fieldName]['name'][$i];
            $this->_temp       = $_FILES[$this->fieldName]['tmp_name'][$i];
            $this->_mime       = $_FILES[$this->fieldName]['type'][$i];
            if (!empty($this->_nom))
                $this->_ext        = Util::getExtension($this->_nom);
            $this->_error      = $_FILES[$this->fieldName]['error'][$i];

            if ($this->_error == UPLOAD_ERR_OK && is_uploaded_file($_FILES[$this->fieldName]['tmp_name'][$i]))
            {
                if ($this->mimeCheck == self::MIME_CHECK_FINFO)
                {
                    $finfo       = new finfo(FILEINFO_MIME, $this->magicFile);
                    $this->_mime = @$finfo->file(realpath($this->_temp));
                    // Peut retourner des mime du style : "text/plain; charset=utf-8"
                    $posVirgule  = strpos($this->_mime, ';');
                    if ($posVirgule !== false)
                        $this->_mime = substr($this->_mime, 0, $posVirgule);
                }
                elseif ($this->mimeCheck == self::MIME_CHECK_MIMETYPE)
                    $this->_mime = mime_content_type(realpath($this->_temp));
                elseif ($this->mimeCheck == self::MIME_CHECK_NONE)
                    $this->_mime = false;

                if ($this->checkSize())
                {
                    if ($this->checkExtension())
                    {
                        if ($this->checkMime())
                        {
                            $this->buildName();
                            if (!$this->write())
                            {
                                $this->errors[$i] = "Impossible d'�crire sur le disque";
                            }
                        }
                        else
                        {
                            $this->errors[$i] = "Ce type de fichier n'est pas autoris�";
                        }
                    }
                    else
                    {
                        $this->errors[$i] = "L'extension du fichier n'est pas autoris�e";
                    }
                }
                else
                {
                    $this->errors[$i] = "Le fichier d�passe la limite de taille autoris�e";
                }
            }
            else
            {
                if ($this->required == self::REQUIRE_ALL || $this->required == self::REQUIRE_YES && $i == 0)
                    $this->errors[$i] = "Erreur pendant l'upload. Fichier trop volumineux ?";
            }
        }
        return $this->getError();
    }

    /**
     * Défini la taille maximal du fichier.
     * Accepte un int ou une notation du type 500K, 2M
     * @param mixed $size
     * @return File_Upload
     */
    public function setMaxFileSize($size)
    {
        $this->maxFileSize = Util::getOctalSize($size);
        return $this;
    }

    /**
     * Défini le dossier contenant les fichier magicmime
     * @param string $dir
     * @return File_Upload
     */
    public function setMagicFile($path)
    {
        $this->magicFile = $path;
        return $this;
    }

    /**
     * Défini le mode d'�criture
     * @param int $mode
     * @return File_Upload
     */
    public function setWriteMode($mode)
    {
        $this->writeMode = intval($mode);
        return $this;
    }

    /**
     * Défini si les fichiers sont requis ou non
     * @param int $mode
     * @return File_Upload
     */
    public function setRequired($mode)
    {
        $this->required = intval($mode);
        return $this;
    }

    /**
     * Défini une (string) ou plusieurs (array) extensions autorisées
     * @param mixed $newExt
     * @return File_Upload
     */
    public function setAllowedExtensions($newExt)
    {
        if (is_array($newExt))
            foreach ($newExt as $value)
                $this->extensions[] = $value;
        else
            $this->extensions[] = $newExt;

        return $this;
    }

    /**
     * retourne les extensions autorisées
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Supprime une extension
     * @param string $extToRm
     * @return File_Upload
     */
    public function removeExtension($extToRm)
    {
        $cle = array_search($extToRm, $this->extensions);
        if ($cle)
            unset($this->extensions[$cle]);

        return $this;
    }

    /**
     * Supprime toutes les extensions
     * @return File_Upload
     */
    public function flushAllowedExtensions()
    {
        $this->extensions = array();
        return $this;
    }

    /**
     * Défini un (string) ou plusieurs (array) type mime autorisé
     * @param mixed $newMime
     * @return File_Upload
     */
    public function setAllowedMime($newMime)
    {
        if (is_array($newMime))
            foreach ($newMime as $value)
                $this->mime[] = $value;
        else
            $this->mime[] = $newMime;

        return $this;
    }

    /**
     * Retourne les type mime autorisés
     * @return array
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * Supprime un type mime
     * @param string $mimeToRm
     * @return File_Upload
     */
    public function removeMime($mimeToRm)
    {
        $cle = array_search($mimeToRm, $this->mime);
        if ($cle)
            unset($this->mime[$cle]);

        return $this;
    }

    /**
     * Supprime tous les types mime
     * @return File_Upload
     */
    public function flushAllowedMime()
    {
        $this->mime = array();
        return $this;
    }

    /**
     * Défini le nom des fichiers une fois envoyés
     * @param string $name
     * @return File_Upload
     */
    public function setFileName($name)
    {
        $this->fileName = trim($name);
        return $this;
    }

    /**
     * Défini le préfix du nom de fichier
     * @param string $prefix
     * @return File_Upload
     */
    public function setPrefix($prefix)
    {
        $this->prefix = trim($prefix);
        return $this;
    }

    /**
     * Défini le suffix du nom de fichier
     * @param string $suffix
     * @return File_Upload
     */
    public function setSuffix($suffix)
    {
        $this->suffix = trim($suffix);
        return $this;
    }

    /**
     * Défini ou non le mode sécurisé qui n'accepte aucun fichier de type application ou jugé dangereux
     * @param boolean $mode
     * @return File_Upload
     */
    public function setSecureMode($mode)
    {
        $this->secureMode($mode);
        return $this;
    }

    /**
     * Active ou non le nettoyage du nom de fichier
     * @param boolean $bool
     * @return File_Upload
     */
    public function cleanName($bool)
    {
        $this->cleanName = $bool;
        return $this;
    }

    /**
     * Retourne le tableau d'erreur
     * @return array
     */
    public function getError()
    {
        if (!empty($this->errors))
            return $this->errors;
        else
            return false;
    }

    /**
     * Retourne le tableau des fichiers envoyés
     * @return array
     */
    public function getSummary()
    {
        return $this->uploadedFiles;
    }

    /**
     * Vérifie qu'un chemin est correct
     * @param string $path
     * @return boolean
     */
    private function checkPath($path)
    {
        $path = trim($path);
        if ($path[0] == '/')
            $path = substr($path, 1);
        if (!is_dir($path))
            throw new \InvalidArgumentException($path . ' n\'est pas un répertoire valide');
        if (!is_writable($path))
            throw new \RuntimeException($path . ' n\'est pas ouvert en écriture');

        return $path;
    }

    /**
     * Ecrit le fichier sur le disque à l'emplacement souhaité
     * @return boolean
     */
    private function write()
    {

        if ($this->writeMode == self::WMODE_OVERWRITE)
        {
            if ($this->exist())
                unlink($this->uploadDir . $this->fileNameTmp);
            $uploaded = move_uploaded_file($this->_temp, $this->uploadDir . $this->fileNameTmp);
        }
        elseif ($this->writeMode == self::WMODE_COPY)
        {
            if ($this->exist())
                $this->fileName = 'Copie_de_' . $this->fileNameTmp;
            $uploaded       = move_uploaded_file($this->_temp, $this->uploadDir . $this->fileNameTmp);
        }
        else
        {
            if (!$this->exist())
                $uploaded = move_uploaded_file($this->_temp, $this->uploadDir . $this->fileNameTmp);
            else
                $uploaded = false;
        }

        if ($uploaded)
        {
            $this->uploadedFiles[] = array(
                'nom' => $this->fileNameTmp,
                'nom_base' => $this->_nom,
                'ext' => $this->_ext,
                'mime' => $this->_mime,
                'path' => $this->uploadDir,
                'octet' => $this->_taille,
                'size' => number_format($this->_taille / 1024, 2, '.', '')
            );
            return true;
        }
        return false;
    }

    /**
     * Vérifie la taille du fichier
     * @return boolean
     */
    private function checkSize()
    {
        if ($this->maxFileSize > $this->_taille)
            return true;
        return false;
    }

    /**
     * Vérifie l'extension du fichier
     * @return boolean
     */
    private function checkExtension()
    {
        if (in_array($this->_ext, $this->extensions))
            return true;
        return false;
    }

    /**
     * Vérifie le type mime du fichier
     * @return boolean
     */
    private function checkMime()
    {
        if ($this->mimeCheck != self::MIME_CHECK_NONE)
        {
            if ($this->secureMode)
            {
                if (!in_array($this->_mime, $this->mime) || strpos($this->_mime, 'application') || preg_match("/.php$|.php3$|.php5$|.inc$|.js$|.exe$/i", $this->_ext))
                    return false;
            } else
            {
                if (!in_array($this->_mime, $this->mime))
                    return false;
            }
        }
        return true;
    }

    /**
     * Construit le nom de fichier
     * @return void
     */
    private function buildName()
    {
        if ($this->fileName == '')
            $this->fileNameTmp = substr($this->_nom, 0, strrpos($this->_nom, '.'));
        else
            $this->fileNameTmp = $this->fileName;

        if ($this->cleanName)
            $this->fileNameTmp = Strings::clean($this->fileNameTmp);
        $this->fileNameTmp = $this->prefix . $this->fileNameTmp . $this->suffix . '.' . $this->_ext;
    }

    /**
     * Vérifie l'existance d'un fichier
     * @access private
     * @return boolean
     */
    private function exist()
    {
        if (file_exists($this->uploadDir . $this->fileNameTmp))
            return true;
        return false;
    }

}

?>