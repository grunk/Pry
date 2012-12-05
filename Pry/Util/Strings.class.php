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

namespace Pry\Util;

/**
 * Classe String
 *
 * Class de gestion de string
 *
 * @category Pry
 * @package Util
 * @version 1.9.0
 * @author Olivier ROGER <oroger.fr>
 *  
 */
class Strings
{

    /**
     * Ajout de slashe. Ajoute des slashes si magiquotes desactivé
     * @access public
     * @param string $chaine Chaine à traiter
     * @static
     * @return string Chaine complétée par des /
     */
    public static function slashes($chaine)
    {
        if (!get_magic_quotes_gpc())
            $chaine = addslashes($chaine);

        return $chaine;
    }

    /**
     * Nettoyage de chaine. Reécrit une chaine pour supprimer espace et caractère spéciaux, accentués ...
     * @access public
     * @param string $titre Chaine à traiter
     * @param string $delimiter Charactère délimiteur
     * @static
     * @return string chaine modifiée
     * */
    public static function clean($string, $delimiter = "_")
    {
        //Transformation des apostrophe en espace pour avoir :
        // c'est = "c-est" et non pas "cest"
        $string = str_replace("'", ' ', $string);

        $cleanStr = iconv('UTF-8', 'ASCII//TRANSLIT', $string); //Suppression accent
        $cleanStr = trim(strtolower($cleanStr));
        $cleanStr = preg_replace("/[^a-z0-9\/_|+ -]/", '', $cleanStr);
        $cleanStr = preg_replace("/[\/_|+ -]+/", $delimiter, $cleanStr);

        return $cleanStr;
    }

    /**
     * Découpe de chaine. Découpe une chaine au nombre de mot souhaité
     * @access public
     * @param string $chaine Chaine à traiter
     * @param int $taillemax Nombre de caractère maxi
     * @param string $end Caractère affiché en cas  de césure (... par défaut)
     * @static
     * @return string Chaine tronquée
     */
    public static function cut($chaine, $taillemax, $end = "...")
    {
        if (strlen($chaine) >= $taillemax)
        {
            $chaine = substr($chaine, 0, $taillemax);
            $espace = strrpos($chaine, " ");
            $chaine = trim(substr($chaine, 0, $espace) . $end);
        }
        return $chaine;
    }

    /**
     * Génération "aléatoire". Génère une string de longeur $taille.
     * La génération favorise les chaines facilement mémorisable
     * @access public
     * @param int $taille Taille de la chaine désirée
     * @static
     * @return string
     */
    public static function generate($taille)
    {

        //Consonnes
        $cons   = 'bBcCdDfFgGhHjJkKlLmMnNoOpPqQrRsStTvVwWxXzZ@!#$%123465789';
        //Voyelles
        $voy    = 'aAeEuUyY123465789'; // pas de o et i pour éviter confusion
        $genere = '';
        $genere.= $cons[rand(0, 41)]; // On commence forcément par une lettre
        for ($i = 1; $i <= ($taille - 1); $i++) {
            if ($i % 2 == 0)
                $genere.=$cons[(rand(0, strlen($cons) - 1))];
            else
                $genere.=$voy[(rand(0, strlen($voy) - 1))];
        }
        return $genere;
    }

    /**
     * Retourne une chaine sous le format camelCase
     *
     * @param string $string
     * @return string
     */
    public static function camelize($string)
    {
        return preg_replace("/[_|\s]([a-z0-9])/e", "strtoupper('\\1')", strtolower($string));
    }

    /**
     * Fonction de geekiserie pour des propos plus intelligents
     *
     * @param string $string
     * @return string
     */
    public static function geekize($string)
    {
        $string = strtolower($string);
        $normal = array('a', 'e', 't', 'l', 's', 'o');
        $geek = array('4', '3', '7', '1', '$', '0');
        return str_replace($normal, $geek, $string);
    }

    /**
     * Anti majuscule. Vérifie que la chaine ne comporte pas trop de majuscule (50%)
     * @access public
     * @param string $string Chaine à vérifier
     * @static
     * @return La chaine modifié si trop de maj ou la chaine original si ok
     */
    public static function hasTooMuchCaps($string)
    {
        $seuil          = strlen($string) / 2;
        $correspondance = similar_text($string, strtolower($string));
        if ($correspondance < $seuil)
            return strtolower($string);

        return $string;
    }

    /**
     * Vérifie si une chaine est en majuscule
     *
     * @param string $string Chaine d'entrée
     * @return boolean
     */
    public static function isUpper($string)
    {
        if (preg_match("/[a-z]/", $string) > 0)
            return false;
        return true;
    }

    /**
     * Vérifie si une chaine est en minuscule
     *
     * @param string $string Chaine d'entrée
     * @return boolean
     */
    public static function isLower($string)
    {
        if (preg_match("/[A-Z]/", $string) > 0)
            return false;
        return true;
    }

    /**
     * Vérification IP. Vérifie que la chaine est une ip valide
     * @access public
     * @param string $ip Adresse Ip à vérifier
     * @static
     * @return boolean
     */
    public static function isIp($ip)
    {
        $motif = '`^([0-9]{1,3}\.){3}[0-9]{1,3}$`';
        if (preg_match($motif, $ip))
        {
            $ipArray = explode(".", $ip);
            for ($i = 0; $i < 4; $i++)
                if ($ipArray[$i] > 255)
                    return false;

            return true;
        }
        else
            return false;
    }

    /**
     * Vérification MAC. Vérifie que la chaine est une adresse MAC valide
     * @access public
     * @param string $mac Adresse MAC à vérifier
     * @static
     * @return boolean
     */
    public static function isMac($mac, $separator = '-')
    {
        $motif = '`^([[:xdigit:]]{2}\\' . $separator . '){5}[[:xdigit:]]{2}$`';
        if (preg_match($motif, $mac))
            return true;

        return false;
    }

    /**
     * Vérifie la syntaxe d'un mail.
     * Gère également les mail locaux avec domaine simple
     *
     * @param string $mail Adresse email
     * @param boolean $dot Un point obligatoire dans le domaine ?
     * @return boolean
     */
    public static function isMail($mail, $dot = true)
    {
        if (function_exists('filter_var'))
        {
            if (filter_var($mail, FILTER_VALIDATE_EMAIL))
            {
                if ($dot)
                {
                    $chaine = explode('@', $mail);
                    $domain = $chaine[1];
                    if (strpos($domain, '.'))
                        return true;

                    return false;
                }

                return true;
            }
            else
            {
                if (!$dot)
                    return self::checkmail($mail, false);
            }
        }
        else
        {
            return self::checkmail($mail);
        }

        return false;
    }

    private static function checkmail($mail, $dot = true)
    {
        $atom       = '[-a-z0-9!#$%&\'*+\\/=?^_`{|}~]';   // caractères autorisés avant l'arobase
        $domain     = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)'; // nom de domaine
        $regex      = '/^' . $atom . '+(\.' . $atom . '+)*@(' . $domain . '{1,63}\.)+' . $domain . '{2,63}$/i';
        $regexNoDot = '/^' . $atom . '+(\.' . $atom . '+)*@(' . $domain . '{1,63})+' . $domain . '{2,63}$/i';
        if (preg_match($regex, $mail))
            return true;
        if (!$dot && preg_match($regexNoDot, $mail))
            return true;
    }

    /**
     * Vérifie si une chaine est complexe.
     * Est considérée comme complexe une chaine d'au moins 6 caractères,
     * une minuscule, une maj , un chiffre et un caractère spécial
     * @param string $string
     * @return boolean
     */
    public static function isComplex($string)
    {
        if (preg_match("`^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9]).{6,}$`", $string))
            return true;

        return false;
    }

    /**
     * Conversion de date au format Mysql
     * @param string $date Date
     * @param string $format Format de la date fournie
     * @since 1.7.8
     * @return string Date au format mysql Y-m-d ou false en cas d'erreur
     */
    public static function date2Mysql($date, $format)
    {
        $dt = \DateTime::createFromFormat($format, $date);
        if ($dt)
            return $dt->format("Y-m-d");

        return false;
    }

    /**
     * Convertit un datetime en format fr ou en
     * @param string $datetime
     * @param string $format Format de langue fr ou en
     * @param boolean $short Date raccourcie (jjmm hhii) ou non
     * @return array
     */
    public static function dateTime2Array($datetime, $format = 'fr', $short = false)
    {
        list($date, $heure) = explode(' ', $datetime);
        list($y, $m, $d) = explode('-', $date);
        list($h, $min, $s) = explode(':', $heure);
        $tabRet = array();
        switch ($format)
        {
            case 'fr' :
                {
                    if ($short)
                    {
                        $tabRet[] = $d . '/' . $m;
                        $tabRet[] = $h . 'h' . $min;
                    }
                    else
                    {
                        $tabRet[] = $d . '/' . $m . '/' . $y;
                        $tabRet[] = $h . ':' . $min . ':' . $s;
                    }
                    break;
                }

            case 'en' :
                {
                    if ($short)
                    {
                        $tabRet[] = $m . '/' . $d;
                        $tabRet[] = $h . 'h' . $min;
                    }
                    else
                    {
                        $tabRet[] = $m . '/' . $d . '/' . $y;
                        $tabRet[] = $h . ':' . $min . ':' . $s;
                    }
                    break;
                }
        }
        return $tabRet;
    }

    /**
     * reduceDoubleSlashes
     * Transforme les // en / sauf sur http://
     * @param string $chaine
     * @return string
     */
    public static function reduceDoubleSlashes($chaine)
    {
        return preg_replace("#(^|[^:])//+#", "\\1/", $chaine);
    }

    /**
     * Convertit une chaine de caractère en sa représentation hexadecimal
     * @param string $str
     * @return string 
     */
    public function str2hex($str)
    {
        $retval = '';
        $length = strlen($str);

        for ($idx = 0; $idx < $length; $idx++)
            $retval .= str_pad(base_convert(ord($str[$idx]), 10, 16), 2, '0', STR_PAD_LEFT);

        return $retval;
    }

    /**
     * Retourne une chaine en UTF8
     * @param string $str Chaine à convertir
     * @return string Chaine en UTF8
     * @since 1.8.6 
     */
    public function toUTF8($str)
    {
        $encoding = mb_detect_encoding($str, mb_detect_order(), true);
        if ($encoding != 'UTF-8')
        {
            if ($encoding)
                $str = mb_convert_encoding($str, 'UTF-8', $encoding);
            else
                $str = mb_convert_encoding($str, 'UTF-8');
        }
        return $str;
    }

}