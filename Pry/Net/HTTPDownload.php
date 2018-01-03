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

namespace Pry\Net;

/**
 * Téléchargement de fichier via protocole HTTP
 * Pour des fichiers de taille importante préférer la solution X-SendFile header
 * @category Pry
 * @package Net
 * @see http://www.php.net/manual/en/function.fread.php#84115
 * @version 1.0.2
 * @author Olivier ROGER <oroger.fr>
 *
 */
class HTTPDownload
{

    /**
     * Chemin vers le fichier
     * @var string
     */
    protected $path;

    /**
     * Nom du fichier envoyé au navigateur.
     * Permet de renommer un fichier en l'envoyant.
     * @var string
     */
    protected $name;

    /**
     * Extension du fichier
     * @var string
     */
    protected $extension;

    /**
     * Type Mime du fichier
     * @var string
     */
    protected $mime;

    /**
     * Taille du fichier en octet
     * @var int
     */
    protected $size;

    /**
     * Active ou non la reprise de téléchargement
     * @var boolean
     */
    protected $resume;

    /**
     * Position de début du pointeur
     * @var int
     */
    protected $seekStart;

    /**
     * Position de fin du pointeur
     * @var int
     */
    protected $seekEnd;

    public function __construct($path, $resume = false)
    {
        if (file_exists($path))
        {
            $this->path      = $path;
            $this->extension = strtolower(substr(strrchr($path, '.'), 1));
            $this->getMime();
            $this->size      = filesize($path);
            $this->seekStart = 0;
            $this->seekEnd   = -1;
            $this->resume    = $resume;
            $this->name      = basename($path);
        }
        else
        {
            throw new \Exception('Fichier innexistant');
        }
    }

    /**
     * Lance le téléchargement ou la reprise du téléchargement
     */
    public function download()
    {
        header("Cache-Control: cache, must-revalidate");
        header("Pragma: public");
        header('Expires: 0');
        if ($this->resume)
        {
            $this->getRange();
            header('HTTP/1.0 206 Partial Content');
            header('Status: 206 Partial Content');
            header('Accept-Ranges: bytes');
            header('Content-Range: bytes ' . $this->seekStart . '-' . $this->seekEnd . '/' . $this->size);
            header('Content-Length:' . ($this->seekEnd - $this->seekStart + 1));
        }
        else
        {
            header('Content-Length:' . $this->size);
        }

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $this->mime);
        header('Content-Disposition: attachment;filename="' . $this->name . '"');
        header('Content-Transfer-Encoding: binary');

        $handle = fopen($this->path, 'rb');
        if (!$handle)
        {
            throw new \Exception('Erreur pendant le téléchargement');
        }
        else
        {
            fseek($handle, $this->seekStart);
            while (!feof($handle)) {
                set_time_limit(0);
                echo fread($handle, 1024 * 8); //Paquet de 8ko
                flush();
                ob_flush();
            }
            fclose($handle);
        }
    }

    /**
     * Défini le type mime du fichier
     * Par défaut le type octet-stream est utilisé
     * @param string $mime
     */
    public function setMimeType($mime)
    {
        $this->mime = $mime;
    }

    /**
     * Renomme le fichier à télécharger
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Récupère le range
     * @see http://tools.ietf.org/id/draft-ietf-http-range-retrieval-00.txt
     * @return void
     */
    private function getRange()
    {
        //Peut être de la forme Range: bytes=0-99,500-1499,4000-
        if (isset($_SERVER['HTTP_RANGE']))
        {
            list($unit, $ranges) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if ($unit == 'bytes')
            {
                list($range, $extraRange) = explode(',', $ranges, 2);
                //Seul le premier range est utilisé pour des raisons de simplicité
                list($seekStart, $seekEnd) = explode('-', $range, 2);

                if (!empty($seekStart) && $seekStart > 0)
                    $this->seekStart = intval($seekStart);

                if (!empty($seekEnd))
                    $this->seekEnd = min(abs(intval($seekEnd)), ($this->size) - 1);
            }
        }
    }

    /**
     * Tente de déterminer le type mime du fichier
     */
    private function getMime()
    {
        switch ($this->extension)
        {
            case 'txt': $this->mime = 'text/plain';
                break;
            case 'pdf': $this->mime = 'application/pdf';
                break;
            case 'rtf': $this->mime = 'application/rtf';
                break;
            case 'jpg': $this->mime = 'image/jpeg';
                break;
            case 'xls': $this->mime = 'application/vnd.ms-excel';
                break;
            case 'pps': $this->mime = 'application/vnd.ms-powerpoint';
                break;
            case 'doc': $this->mime = 'application/msword';
                break;
            case 'exe': $this->mime = 'application/octet-stream';
                break;
            case 'zip': $this->mime = 'application/zip';
                break;
            case 'mp3': $this->mime = 'audio/mpeg';
                break;
            case 'mpg': $this->mime = 'video/mpeg';
                break;
            case 'avi': $this->mime = 'video/x-msvideo';
                break;
            default: $this->mime = 'application/force-download';
        }
    }

}