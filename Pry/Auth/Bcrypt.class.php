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

namespace Pry\Auth;

/**
 * Classe implémentant l'algorithm bcrypt pour le cryptage de mot de passe.
 * <code>
 * $bcrypt = new Bcrypt(11);
 * $hash = $bcrypt->hash('mypass');
 * var_dump($bcrypt->check('mypass',$hash);
 * </code>
 * @package Auth
 * @version 1.0
 * @author Olivier ROGER <oroger.fr>
 * @see http://en.wikipedia.org/wiki/Bcrypt
 * @see http://stackoverflow.com/questions/4795385/how-do-you-use-bcrypt-for-hashing-passwords-in-php
 */
class Bcrypt
{
    /** Base du grain de sel blowfish */

    const SALTBASE = '$2a$';

    /** Logarithme base 2 du compteur d'itération */
    private $iterations;

    /** Aléa pour le grain de sel */
    private $random;

    /**
     * Initialise le cryptage
     * @param int $rounds Nombre d'itération pour l'algo de hashage entre 4 et 31. 
     * Plus ce paramètre est élevé plus le temps de hashage sera long. 12 par défaut
     * @throws RuntimeException Si le cryptage BlowFish n'est pas supporté sur l'installation
     * @throws InvalidArgumentException Le paramètres n'est pas compris entre 4 et 31.
     */
    public function __construct($rounds = 12)
    {
        if (CRYPT_BLOWFISH != 1)
            throw new \RuntimeException('Blowfish is not available on your system, it is required for bcrypt');

        if ($rounds < 4 || $rounds > 31)
            throw new \InvalidArgumentException(' The number of rounds have to be between 4 and 31');

        $this->iterations = $rounds;
        $this->random     = microtime();

        if (function_exists('getmypid'))
            $this->random .= getmypid();
    }

    /**
     * Hash la chaine donnée
     * @param string $str Chaine en clair
     * @return string Un hash de la chaine de 60 caractères
     * @throws type 
     */
    public function hash($str)
    {
        $hash = crypt($str, $this->generateSalt());
        if (strlen($hash) != 60)
            throw \LengthException('Hash is ' . strlen($hash) . ' characters long , somethng went wrong');

        return $hash;
    }

    /**
     * Vérifie une chaine avec un hash
     * @param string $str Chaine en claire
     * @param string $hashedStr Un hash
     * @return boolean True si les deux correspondent , false sinon 
     */
    public static function check($str, $hashedStr)
    {
        $hash = crypt($str, $hashedStr);
        return $hash === $hashedStr;
    }

    /**
     * Génération d'un grain de sel pour le hashage
     * @return string 
     */
    private function generateSalt()
    {
        // Salt se forme aec : $2a$/2 digits/$/22 charactères parmis [./0-9a-Z]
        $salt  = Bcrypt::SALTBASE . $this->iterations . '$';
        $bytes = $this->getRandomBytes(16);
        $salt .= $this->getBlowfishSalt($bytes);

        return $salt;
    }

    /**
     * Génération de bytes aléatoires
     * @param int $size Nombre de byte à générer
     * @return bytes 
     */
    private function getRandomBytes($size)
    {
        $bytes = '';

        // /dev/urandom est la meilleur source d'aléa , on test sa disponibilité : 
        if (is_readable('/dev/urandom'))
        {
            $handle = @fopen('/dev/urandom', 'rb');
            if ($handle)
            {
                $bytes = fread($handle, $size);
            }
        }

        // Si pas de dev/urandom on essai avec openssl
        if ($bytes === '' && function_exists('openssl_random_pseudo_bytes'))
            $bytes = openssl_random_pseudo_bytes($size);

        //Si aucun des procédés dispo ou si la génération n'as pas la bonne taille
        if (strlen($bytes) < $size)
        {
            $bytes = '';

            $this->random = md5(microtime() . $this->random);
            $bytes .= md5($this->random, true); // Retour en byte et non string

            $bytes = substr($bytes, 0, $size);
        }

        return $bytes;
    }

    /**
     * Transformation des bytes générés.
     * Code original par phpass
     * @see http://www.openwall.com/phpass/
     * @param rawbytes $input
     * @return string 
     */
    private function getBlowfishSalt($input)
    {
        $itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $output = '';
        $i      = 0;
        do {
            $c1 = ord($input[$i++]);
            $output .= $itoa64[$c1 >> 2];
            $c1 = ($c1 & 0x03) << 4;
            if ($i >= 16)
            {
                $output .= $itoa64[$c1];
                break;
            }

            $c2 = ord($input[$i++]);
            $c1 |= $c2 >> 4;
            $output .= $itoa64[$c1];
            $c1 = ($c2 & 0x0f) << 2;

            $c2 = ord($input[$i++]);
            $c1 |= $c2 >> 6;
            $output .= $itoa64[$c1];
            $output .= $itoa64[$c2 & 0x3f];
        } while (1);

        return $output;
    }

}