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

namespace Pry\Log\Writer;

/**
 * Class d'écriture de log dans les fichiers
 * 
 * @package Log
 * @subpackage Log_Writer
 * @version 1.2.0
 * @author Olivier ROGER <oroger.fr>
 *       
 */
class File extends WriterAbstract
{

    private $folder;
    private $lineLimit;

    public function __construct($path,$create = false)
    {
        if(!file_exists($path) && $create)
            mkdir($path, 0777, true);
        
        if (!is_dir($path) || !is_writable($path))
            throw new \InvalidArgumentException('Le dossier spécifié n\'existe pas ou n\'est pas ouvert en écriture');
        else
        {
            if ($path[strlen($path) - 1] != '/')
                $path.='/';
        }
        $this->folder    = $path;
        $this->lineLimit = 50;
    }

    /**
     * Défini le nombre de ligne que contiendra le fichier de log. 0 pour aucune limite
     * @since 1.1.0
     * @param int $lines Nombre limite de ligne
     */
    public function setLineLimit($lines)
    {
        $this->lineLimit = intval($lines);
    }

    /**
     * Log du message
     *
     * @param string $message
     * @param string $level
     * @access public
     */
    protected function _write($message, $level)
    {
        $prefixe = ($this->mode == self::MODE_MINI) ? '' : 
            '[' . date("d/m/Y - H:i:s"). ' - '. $_SERVER['REMOTE_ADDR'] . '] (' . $this->txtSeverity[$level] . ') ';

        $file         = $this->txtSeverity[$level] . '.log';
        $prefixe_file = $this->getPrefixe();
        $filepath     = $this->folder . $prefixe_file . $file;

        if (!is_file($filepath))
            touch($filepath);

        if ($this->lineLimit > 0)
        {
            $logsContent = file($filepath);
            if (count($logsContent) >= $this->lineLimit)
            {
                $logsContent[] = $prefixe . $message . "\n";
                array_shift($logsContent);
                file_put_contents($filepath, $logsContent);
            }
            else
            {
                $h = fopen($filepath, 'a');
                fwrite($h, $prefixe . $message . "\n");
                fclose($h);
            }
        }
        else
        {
            $h = fopen($filepath, 'a');
            fwrite($h, $prefixe . $message . "\n");
            fclose($h);
        }
    }
}