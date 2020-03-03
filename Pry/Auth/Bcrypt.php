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
 * @version 2.0
 * @author Olivier ROGER <oroger.fr>
 */
class Bcrypt
{
    /** Logarithme base 2 du compteur d'itération */
    private $options;


    /**
     * Initialise le cryptage
     * @param int $rounds Nombre d'itération pour l'algo de hashage entre 4 et 31. 
     * Plus ce paramètre est élevé plus le temps de hashage sera long. 12 par défaut
     * @throws \RuntimeException Si le cryptage BlowFish n'est pas supporté sur l'installation
     * @throws \InvalidArgumentException Le paramètres n'est pas compris entre 4 et 31.
     */
    public function __construct($rounds = 12)
    {
        if (!defined('PASSWORD_BCRYPT'))
            throw new \RuntimeException('Bcrypt not available on your PHP installation');

        if ($rounds < 4 || $rounds > 31)
            throw new \InvalidArgumentException(' The number of rounds have to be between 4 and 31');

        $this->options = array('cost' => $rounds);
    }

    /**
     * Hash la chaine donnée
     * @param string $str Chaine en clair
     * @return string Un hash de la chaine de 60 caractères
     */
    public function hash($str)
    {
        return password_hash($str,PASSWORD_BCRYPT,$this->options);
    }

    /**
     * Vérifie une chaine avec un hash
     * @param string $str Chaine en claire
     * @param string $hashedStr Un hash
     * @return boolean True si les deux correspondent , false sinon 
     */
    public static function check($str, $hashedStr)
    {
        return password_verify($str,$hashedStr);
    }

    /**
     * Vérifie si un hash à besoin d'être regénéré
     * @param $hashedStr string hash à vérifier
     * @return bool true si regénération nécessaire
     */
    public function needsRehash($hashedStr)
    {
        return password_needs_rehash($hashedStr,PASSWORD_BCRYPT,$this->options);
    }
}