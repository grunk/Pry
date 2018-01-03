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

/**
 * Gestion d'écriture de fichier csv
 * @category Pry
 * @package File
 * @version 1.1.0 
 * @author Olivier ROGER <oroger.fr> 
 *
 */
class FileCSV extends FileManager
{

    /**
     * Caractère de séparation
     * @var string
     */
    private $glue;

    /**
     * Nobre de colonne du fichier
     * @var unknown_type
     */
    private $nbCols;

    /**
     * Constructeur
     * @param string $file Chemin vers le fichier
     * @param string $glue Caractère de séparation
     * 
     */
    public function __construct($file, $glue = ';')
    {
        parent::__construct($file);
        $this->open(FileManager::WRITE);
        $this->glue = $glue;
    }

    /**
     * Ajoute les colonnes du fichiers
     * @param array $cols
     * @return void
     */
    public function addColumns(array $cols)
    {
        if (!is_array($cols))
            throw new \InvalidArgumentException('Argument de type Array attendu pour les colonnes');

        $this->nbCols = count($cols);

        $this->writeLine(implode($this->glue, $cols));
    }

    /**
     * Ajoute une ligne vide
     * @return void
     */
    public function addBlankLine()
    {
        $line = str_repeat(';', $this->nbCols - 1);
        $this->writeLine($line);
    }

    /**
     * Ajoute une ligne
     * @param mixed $data
     * @return void
     */
    public function addLine($data)
    {
        if (!is_array($data))
            $data = explode($this->glue, $data);

        if (count($data) != $this->nbCols)
            throw new \UnexpectedValueException('Le nombre de colonnes ne correspond pas au nombre d\'entête');

        $this->writeLine(implode($this->glue, $data));
    }

}