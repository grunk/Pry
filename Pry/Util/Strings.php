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

use DateTime;

/**
 * Strings utility
 * @author Olivier ROGER <oroger.fr>
 *  
 */
class Strings
{

    /**
     * Add slashes if magic_quote is disable
     * @param string $chaine String to process
     * @return string String with added slashes
     */
    public static function slashes(string $chaine) : string
    {
        if (!get_magic_quotes_gpc())
            $chaine = addslashes($chaine);

        return $chaine;
    }

    /**
     * Clean a string of space , accent and special char ...
     * @param string $string String to modify
     * @param string $delimiter string to replace special char
     * @return string Cleaned string
     * */
    public static function clean(string $string, string $delimiter = "_") : string
    {
        //Transformation des apostrophe en espace pour avoir :
        // c'est = "c-est" et non pas "cest"
        $string = str_replace("'", ' ', $string);
        $cleanStr = '';
        $cleanStr = iconv('UTF-8', 'ASCII//TRANSLIT', $string); //Suppression accent
        $cleanStr = trim(strtolower($cleanStr));
        $cleanStr = preg_replace("/[^a-z0-9\/_|+ -]/", '', $cleanStr);
        $cleanStr = preg_replace("/[\/_|+ -]+/", $delimiter, $cleanStr);

        return $cleanStr;
    }

    /**
     * Trim a string to max number of char
     * @param string $chaine String to cut
     * @param int $taillemax Max char to keep
     * @param string $end Char to add at the end (... by default)
     * @return string Cutted string
     */
    public static function cut(string $chaine, int $taillemax, string $end = "...") : string
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
     * Generate a random string
     * @param int $taille Size of the random string
     * @return string
     */
    public static function generate(int $taille) : string
    {

        //Consonnes
        $cons   = 'bBcCdDfFgGhHjJkKlLmMnNoOpPqQrRsStTvVwWxXzZ@!#$%123465789';
        //Voyelles
        $voy    = 'aAeEuUyY123465789'; // pas de o et i pour éviter confusion
        $genere = '';
        $genere.= $cons[mt_rand(0, 41)]; // On commence forcément par une lettre
        for ($i = 1; $i <= ($taille - 1); $i++) {
            if ($i % 2 == 0)
                $genere.=$cons[(mt_rand(0, strlen($cons) - 1))];
            else
                $genere.=$voy[(mt_rand(0, strlen($voy) - 1))];
        }
        return $genere;
    }

    /**
     * camelCase a string
     *
     * @param string $string
     * @return string
     */
    public static function camelize(string $string) : string
    {
        return preg_replace("/[_|\s]([a-z0-9])/e", "strtoupper('\\1')", strtolower($string));
    }

    /**
     * Geekize a string
     *
     * @param string $string
     * @return string
     */
    public static function geekize(string $string) : string
    {
        $string = strtolower($string);
        $normal = array('a', 'e', 't', 'l', 's', 'o');
        $geek = array('4', '3', '7', '1', '$', '0');
        return str_replace($normal, $geek, $string);
    }

    /**
     * Check if a string as more than 50% of capital letter
     * @param string $string String to check
     * @return true if too much caps
     */
    public static function hasTooMuchCaps(string $string) : bool
    {
        $seuil          = strlen($string) / 2;
        $correspondance = similar_text($string, strtolower($string));
        return $correspondance < $seuil;
    }

    /**
     * Check if string is uppercase
     * @param string $string String to check
     * @return boolean
     */
    public static function isUpper(string $string) : bool
    {
        return preg_match("/[a-z]/", $string) == 0;
    }

    /**
     * Check if a string is lowercase
     *
     * @param string $string String to check
     * @return boolean
     */
    public static function isLower(string $string) : bool
    {
        return preg_match("/[A-Z]/", $string) == 0;
    }

    /**
     * Check that the string is a valid IP v4
     * @param string $ip IP to check
     * @return boolean
     */
    public static function isIp(string $ip) : bool
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

        return false;
    }

    /**
     * Check if a string is a valid MAC address
     * @param string $mac String to check
     * @param string $separator char used to separate each part of the address. Default to -
     * @return boolean
     */
    public static function isMac(string $mac, string $separator = '-') : bool
    {
        $motif = '`^([[:xdigit:]]{2}\\' . $separator . '){5}[[:xdigit:]]{2}$`';
        if (preg_match($motif, $mac))
            return true;

        return false;
    }

    /**
     * Check if string is a valid email
     * Also handle local email with simple domain
     *
     * @param string $mail Email
     * @param boolean $dot Does a dot is mandatory in the domain ?
     * @return boolean
     */
    public static function isMail(string $mail, bool $dot = true) : bool
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

        return false;
    }

    /**
     * Check if a string est complex
     * A complex string as at least 8 char , 1 upper case, un number and 1 special char
     * @param string $string
     * @return boolean
     */
    public static function isComplex(string $string) : bool
    {
        if (preg_match("`^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9]).{8,}$`", $string))
            return true;

        return false;
    }

    /**
     * Convert a date to sql format Y-m-d H:i:s
     * @param string $date Date
     * @param string $format Input date format
     * @return string Formatted date or null in case of error
     */
    public static function date2Mysql(string $date, string $format) : ?string
    {
        $result = false;
        $dt = DateTime::createFromFormat($format, $date);
        if ($dt)
            $result = $dt->format("Y-m-d");

        if($result != false)
            return $result;

        return null;
    }

    /**
     * Convert a date time to fr or en format
     * @param string $datetime
     * @param string $format Langue format (fr or en)
     * @param boolean $short Short date format (jjmm hhii)
     * @return array
     */
    public static function dateTime2Array(string $datetime, string $format = 'fr', bool $short = false) : array
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
     * Transform  // in / except in http://
     * @param string $chaine
     * @return string
     */
    public static function reduceDoubleSlashes(string $chaine) : string
    {
        return preg_replace("#(^|[^:])//+#", "\\1/", $chaine);
    }

    /**
     * convert a string to hexadecimal
     * @param string $str
     * @return string 
     */
    public function str2hex(string $str) : string
    {
        $retval = '';
        $length = strlen($str);

        for ($idx = 0; $idx < $length; $idx++)
            $retval .= str_pad(base_convert(ord($str[$idx]), 10, 16), 2, '0', STR_PAD_LEFT);

        return $retval;
    }

    /**
     * Convert to UTF8
     * @param string $str String to convert
     * @return string String in UTF8
     */
    public function toUTF8(string $str) : string
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