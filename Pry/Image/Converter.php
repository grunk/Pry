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

namespace Pry\Image;

/**
 * Convertisseur de type d'image
 * @category Pry
 * @package Image
 * @version 1.1.0 
 * @author Olivier ROGER <oroger.fr>
 *       
 */
class Converter extends Image
{

    /**
     * Nouvelle image créee
     *
     * @var ressource
     */
    private $newimg;

    private $copie;

    /**
     * Constructeur
     *
     * @param image $image Flux ou fichier
     */
    public function __construct($img)
    {
        $info         = getimagesize($img);
        $this->width  = $info[0];
        $this->height = $info[1];
        $this->type   = $info[2]; // 1:gif; 2:jpg; 3:png; 4:swf; 5:psd; 6:bmp; 7:tiff
        $this->copie  = null;
        $this->source = $this->createFromType($img);
    }

    /**
     * Convertion de l'image de base dans le type choisi
     *
     * @param int $typeOut Type de sortie
     */
    public function convert($typeOut)
    {
        $this->type   = $typeOut;
        $this->newimg = imagecreatetruecolor($this->width, $this->height);
        imagecopy($this->newimg, $this->source, 0, 0, 0, 0, $this->width, $this->height);
        $this->source = $this->newimg;
    }

}