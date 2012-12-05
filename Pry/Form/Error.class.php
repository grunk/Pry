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

namespace Pry\Form;

/**
 *
 * @category Pry
 * @package Form
 * @abstract
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr>
 *       
 *
 */
abstract class Error
{

    const REQUIRED = 'Ce champs doit être rempli';
    const TOOSHORT = 'Contenu trop court, au moins :';
    const TOOLONG  = 'Contenu trop long, limite de :';
    const NOTIP    = 'Veuillez saisir une IP valide';
    const NOTMAC   = 'Veuillez saisir une adresse MAC valide';
    const NOTDATE  = 'Veuillez saisir une date valide';
    const TOOBIG   = 'Le poids du fichier est trop élevé';
    const EXT      = 'Extension de fichier non autorisée';
    const UPLOAD   = 'L\'envoi semble avoir échoué';
    const MAIL     = 'Adresse email invalide';
    const COLOR    = 'Format de couleur inconnu';
    const NUMERIC  = 'Ce champs requiert un format numérique';

}

?>