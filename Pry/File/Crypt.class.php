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

use Pry\File\FileManager;

/**
 * Cryptage / Décryptage de fichier
 * @category Pry
 * @package File
 * @version 0.9
 * @author Olivier ROGER <oroger.fr>
 */
class Crypt
{

    /**
     * Clé de chiffrement
     * @var string $key
     */
    private $key = null;

    /**
     * Type de chiffrement
     * @see Doc Mcrypt pour la liste
     * @var string $cipher
     */
    private $cipher;

    /**
     * Mode de chiffrement
     * @see Doc mcrypt pour liste
     * @var string $mode
     */
    private $mode;

    /**
     * Vecteur d'initialisation
     * @var <type> $iv
     */
    private $iv;

    /**
     * Constructeur
     * @param string $key Clé de chiffrage
     * @param string $cipher Type de chiffrment (défaut 3DES)
     * @param string $mode Mode de chiffrement (défaut nofb)
     */
    public function __construct($key, $cipher = MCRYPT_3DES, $mode = MCRYPT_MODE_NOFB)
    {
        if (!extension_loaded('mcrypt'))
            throw new \Exception('Extension mcrypt nécessaire');

        $this->cipher = $cipher;
        $this->mode   = $mode;
        $key_size     = mcrypt_module_get_algo_key_size($this->cipher);
        $this->key    = substr($this->key, 0, $key_size);
        $iv_size      = mcrypt_get_iv_size($this->cipher, $this->mode);
        $this->iv     = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    }

    /**
     * Crypte un fichier
     * @param File_FileManager $filein Fichier à crypter
     * @param File_FileManager $fileout Fichier crypté
     * @param boolean $deletein Supprime ou non le fichier non crypté à la fin du processus.
     */
    public function crypt(FileManager $filein, FileManager $fileout, $deletein = true)
    {
        $data2Crypt = $this->readFile($filein);
        if ($deletein)
            $filein->delete();
        unset($filein);

        $dataCrypted = mcrypt_encrypt($this->cipher, $this->key, $data2Crypt, $this->mode, $this->iv);
        $this->writeFile($fileout, $dataCrypted);
    }

    /**
     * Décrypte un fichier
     * @param File_FileManager $filein Fichier à décrypter
     * @param File_FileManager $fileout Fichier en clair
     * @param boolean $deletein Supprime ou non le fichier crypté à la fin du processus
     */
    public function decrypt(FileManager $filein, FileManager $fileout, $deletein = false)
    {
        $cryptedData = $this->readFile($filein);
        if ($deletein)
            $filein->delete();
        unset($filein);

        $uncryptedData = mcrypt_decrypt($this->cipher, $this->key, $cryptedData, $this->mode, $this->iv);
        $this->writeFile($fileout, $uncryptedData);
    }

    /**
     * Lit le contenu du fichier
     * @param File_FileManager $file
     * @return string
     */
    private function readFile(FileManager $file)
    {
        $file->open('rb');
        return $file->read();
    }

    /**
     * Ecrit le fichier
     * @param File_FileManager $file
     * @param string $data
     */
    private function writeFile(FileManager $file, $data)
    {
        $file->open('wb+');
        $file->write($data);
        $file->close();
    }

}