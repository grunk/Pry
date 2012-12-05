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

namespace Pry\Config;

/**
 * Classe permettant la lecture de fichier de config au format INI. Basée sur Zend_Config_Ini
 * Il est possible de créer un héritage entre les section à l'aide du caractère ":"
 * Si une clé contient un "." cela sera considérer comme un séparateur pour créer une sous propriété.
 * Par exemple :
 * <code>
 * [prod]
 * db.host = mysite.com
 * db.user = root
 * [dev : prod]
 * db.host = localhost
 * </code>
 * @package Config
 * @version 1.0.0
 * @author Olivier ROGER <oroger.fr>
 *
 */
class Ini extends Config
{

    /**
     * Caractère représentant l'héritage
     * @var string 
     */
    private $inheritanceSeparator = ':';

    /**
     * Caractère représentant les sous propriété
     * @var string 
     */
    private $separator = '.';

    /**
     * Initialise la lecture d'un fichier de config au format ini
     * @param string $file Chemin vers le fichier à lire
     * @param string $section Section du fichier à lire. Null pour tout lire
     * @throws RuntimeException 
     */
    public function __construct($file, $section = null)
    {
        if (empty($file))
            throw new \RuntimeException("Filename is not set");

        $arrayData = array();

        $iniData = $this->load($file);

        if ($section == null)
        {
            //Chargement du fichier complet
            foreach ($iniData as $sectionName => $data) {
                if (!is_array($data))
                {
                    $arrayData = $this->_arrayMergeRecursive($arrayData, $this->parseKey($sectionName, $data, array()));
                }
                else
                {
                    $arrayData[$sectionName] = $this->parseSection($iniData, $sectionName);
                }
            }

            parent::__construct($arrayData);
        }
        else
        {
            //Chargement d'une section en particulier
            if (!is_array($section))
                $section = array($section);

            foreach ($section as $sectionName) {
                if (!isset($iniData[$sectionName]))
                    throw new \RuntimeException("Section $sectionName can't be found");

                $arrayData = $this->_arrayMergeRecursive($this->parseSection($iniData, $sectionName), $arrayData);
            }

            parent::__construct($arrayData);
        }
    }

    /**
     * Charge le fichier
     * @param string $file Fichier à charger
     * @return array Données chargée
     * @throws RuntimeException 
     */
    private function load($file)
    {
        $rawArray = $this->parse($file);
        $iniArray = array();

        //Recherche d'héritage entre les sections
        foreach ($rawArray as $key => $data) {
            $sections       = explode($this->inheritanceSeparator, $key);
            $currentSection = trim($sections[0]);
            $nbSection      = count($sections);
            switch ($nbSection)
            {
                // Section simple
                case 1 :
                    $iniArray[$currentSection] = $data;
                    break;

                // Section avec héritage
                case 2 :
                    $inheritedSection          = trim($sections[1]);
                    // On ajoute une clé $inherit pour définir de qui hérite la section
                    $iniArray[$currentSection] = array_merge(array('$inherit' => $inheritedSection), $data);
                    break;
                default:
                    throw new \RuntimeException("Section $currentSection can't inherit from multiple section");
            }
        }

        return $iniArray;
    }

    /**
     * Parse un fichier ini
     * @param string $file Fichier à charger
     * @return array Données parsée
     * @throws Exception 
     */
    private function parse($file)
    {
        //Supprime les erreurs et warning pour les transformer en exception
        set_error_handler(array($this, 'errorHandler'));
        $data = parse_ini_file($file, true);
        restore_error_handler();
        if ($this->errorStr !== null)
            throw new \Exception("Can't parse Ini file : " . $this->errorStr);

        return $data;
    }

    /**
     * Parse chaque élément de la section et gère la clé '$inherit' qui permet de détecter un
     * héritage entre section. Passe ensuite les données à parseKey pour gérer les sous propriété
     * @param array $iniArray Tableau des données
     * @param strng $section Nom de la section
     * @param array $config
     * @return array
     * @throws Exception 
     */
    private function parseSection($iniArray, $section, $config = array())
    {
        $currentSection = $iniArray[$section];

        foreach ($currentSection as $key => $value) {
            if ($key == '$inherit')
            {
                //Si la section courante hérite d'une autre section
                if (isset($iniArray[$value]))
                {
                    $this->checkForCircularInheritance($section, $value);
                    $config = $this->parseSection($iniArray, $value, $config);
                }
                else
                {
                    throw new \Exception("Can't found the inherited section of " . $section);
                }
            }
            else
            {
                //Si la section est indépendante
                $config = $this->parseKey($key, $value, $config);
            }
        }

        return $config;
    }

    /**
     * Assigne les valeurs de clés aux propriété. Gère le séparateur de propriété pour créer des 
     * sous propriété
     * @param string $key
     * @param string $value
     * @param array $config
     * @return array
     * @throws Exception 
     */
    private function parseKey($key, $value, $config)
    {
        if (strpos($key, $this->separator) !== false)
        {
            $properties = explode($this->separator, $key, 2);
            if (strlen($properties[0]) && strlen($properties[1]))
            {
                if (!isset($config[$properties[0]]))
                {
                    if ($properties[0] === '0' && !empty($config))
                    {
                        $config = array($properties[0] => $config);
                    }
                    else
                    {
                        $config[$properties[0]] = array();
                    }
                }
                elseif (!is_array($config[$properties[0]]))
                {
                    throw new \Exception("Cannot create sub-key for '{$properties[0]}' as key already exists");
                }
                $config[$properties[0]] = $this->parseKey($properties[1], $value, $config[$properties[0]]);
            }
            else
            {
                throw new \Exception("Invalid Key : " . $key);
            }
        }
        else
        {
            $config[$key] = $value;
        }

        return $config;
    }

}