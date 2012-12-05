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
 * Utilitaire pour fichier / dossier
 * @category Pry
 * @package File
 * @version 1.1.0
 * @author Olivier ROGER <oroger.fr>
 *       
 *
 */
class Util
{

    /**
     * Retourne l'extension d'un fichier avec ou sans le point
     *
     * @param string $file Nom de fichier
     * @param bool $dot Afficher ou non le point dans l'extension
     * @static
     * @return string
     */
    public static function getExtension($file, $dot = false)
    {
        if (empty($file))
            throw new \InvalidArgumentException('Le nom de fichier ne peut être vide');

        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if ($dot)
            $ext = '.' . $ext;

        return $ext;
    }

    /**
     * Retourne le nom de fichier sans l'extension
     * @todo Gérer les doubles extensions type tar.gz tout en gérant les nom de ficheirs avec des .
     * @param string $file
     * @return string
     */
    public static function getName($file)
    {
        if (empty($file))
            throw new \InvalidArgumentException('Le nom de fichier ne peut être vide');
        else
            $name = basename($file, strrchr($file, '.'));

        return $name;
    }

    /**
     * Retourne le nom dufichier/dossier compris dans un chemin
     *
     * @param string $path
     * @return unknown
     */
    public static function getNameFromPath($path)
    {
        if (empty($path))
        {
            throw new \InvalidArgumentException('Le nom de fichier ne peut être vide');
        }
        else
        {
            if (strrchr($path, '/'))
                return strtolower(substr(strrchr($path, '/'), 1));
            else
                return $path;
        }
    }

    /**
     * Retourne la valeur en octet d'une taille courte (2K, 2M, 2G)
     *
     * @param string $size Taille courte. Accepte les unités K,M,G
     * @static 
     * @return int
     */
    public static function getOctalSize($size)
    {
        if (empty($size))
            return 0;
        if (is_numeric($size))
            return $size;
        if (!is_numeric($size))
        {
            $unit = substr($size, -1);
            if ($unit == 'K')
                $size *= 1 << 10;
            elseif ($unit == 'M')
                $size *= 1 << 20;
            elseif ($unit == 'G')
                $size *= 1 << 30;
        }
        else
            throw new \InvalidArgumentException('La taille doit être une chaine');
        return $size;
    }

    /**
     * Retourne une taille en octet sous forme lisible
     *
     * @param int $size
     * @since 1.0.1
     * @return string
     */
    public static function getHumanSize($size)
    {
        if (empty($size))
            return 0;

        if ($size < 0)
        {
            $taille = '>2Go';
        }
        else
        {
            if ($size < 1024)
                $taille = round($size, 3) . ' o';
            elseif ($size < 1048576)
                $taille = round($size / 1024, 3) . ' Ko';
            elseif ($size < 1073741824)
                $taille = round($size / 1024 / 1024, 3) . ' Mo';
            elseif ($size < 1099511627776)
                $taille = round($size / 1024 / 1024 / 1024, 3) . ' Go';
        }
        return $taille;
    }

}

?>