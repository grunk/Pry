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

namespace Pry\Feed\Writers;

/**
 * Writer de flux au format RSS.
 * @category Pry
 * @package Feed
 * @subpackage Feed_Writers
 * @version 1.0.0
 * @author Olivier ROGER <oroger.fr>
 *       
 */
interface Interfaces
{

    /**
     * Finalise le flux et le retourne dans un fichier ou sous forme de chaine.
     * Retourne le nombre de byte écrit si utilisation de fichier
     * @return string|int
     */
    public function finalize();
}