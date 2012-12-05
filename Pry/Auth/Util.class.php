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
 * Outils utiles pour la gestion d'authentification
 * @category Pry
 * @package Auth
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr>
 *       
 *
 */
class Util
{

    /**
     * Calcul la complexité d'un mot de passe
     * Basé sur système d'ajout et suppression de point :
     *
     * minuscule = +(longeur-nbMinuscule)*nbMinuscule
     * majuscule = +((longeur-nbMajus)*nbMajus)+3
     * chiffre   = +nbChiffre*4
     * Autre     = +nbAutre*8
     *
     * Retrait de point :
     *
     * - nbMinusculeConsecutive*2
     * - nbMajConsecutive*2
     * - nbChiffreConsec*2
     * - nbAutreCharConsec
     * - nbCharIdentique*3
     *
     * On peut juger les mdp comme suit 0-33 : mauvais; 33-66 : moyen; 66-100 : bon; 100+ : Excellent
     *
     * @param string $pass Mot de passe
     * @return int
     */
    public static function passwordComplexity($pass)
    {

        $longeur            = mb_strlen($pass);
        $numMin             = 0;
        $numMaj             = 0;
        $numChiffre         = 0;
        $numOther           = 0;
        $consecMinLetter    = 0;
        $consecMajLetter    = 0;
        $consecNum          = 0;
        $consecChar         = 0;
        $sameChar           = 0;

        if ($longeur > 2)
        {
            for ($i = 0; $i < $longeur; $i++) {
                if ($pass[$i] >= 'a' && $pass[$i] <= 'z')
                {
                    $numMin++;
                    if ($i > 0 && ($pass[$i - 1] >= 'a' && $pass[$i - 1] <= 'z'))
                        $consecMinLetter++;
                }
                elseif ($pass[$i] >= 'A' && $pass[$i] <= 'Z')
                {
                    $numMaj++;
                    if ($i > 0 && ($pass[$i - 1] >= 'A' && $pass[$i - 1] <= 'Z'))
                        $consecMajLetter++;
                }
                elseif ($pass[$i] >= 0 && $pass[$i] <= 9)
                {
                    $numChiffre++;
                    if ($i > 0 && ($pass[$i - 1] >= 0 && $pass[$i - 1] <= 9))
                        $consecNum++;
                }
                else
                {
                    $numOther++;
                    if ($i > 0 && !preg_match('/^([a-zA-Z0-9]+)$/', $pass[$i - 1]))
                        $consecChar++;
                }

                if ($i > 0 && $pass[$i] == $pass[$i - 1])
                    $sameChar++;
            }
            $positiveScore = ($longeur * 2) + (($longeur - $numMin) * $numMin) + ((($longeur - $numMaj) * $numMaj) + 3) +
                    ($numChiffre * 4) + ($numOther * 8);
            $score = $positiveScore - (($consecMinLetter * 2) + ($consecMajLetter * 2) + ($consecNum * 2) + $consecChar +
                    ($sameChar * 3));
            return $score;
        }
        else
        {
            return 0;
        }
    }
}

?>