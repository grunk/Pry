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
 * Application d'effet sur une image
 * 
 * @category Pry
 * @package Image
 * @version 1.0.0 
 * @author Olivier ROGER <oroger.fr>
 */
class Traitement
{

    /**
     * Objet représentant l'image
     *
     * @var Image
     */
    private $objet;

    /**
     * Ressource de l'image à modifier
     *
     * @var ressource
     */
    private $sourceImage;

    /**
     * Dimension X de l'image
     *
     * @var int
     */
    private $largeur;

    /**
     * Dimension Y de l'image
     *
     * @var int
     */
    private $hauteur;

    /**
     * Constructeur
     * 
     * @param Image $img Objet de l'image
     * @throws \Exception
     */
    public function __construct(Image &$img)
    {
        if (!is_object($img))
        {
            throw new \Exception('Objet attendu');
        }

        $this->objet       = $img;
        $this->sourceImage = $this->objet->getSource();
        $data              = $this->objet->getInfo();
        $this->largeur     = $data['width'];
        $this->hauteur     = $data['height'];
    }

    /**
     * Convertit l'image en niveau de gris. Imagefilter remplace les calcul via matrice. 10x plus rapide
     * @access public
     * @throws \Exception
     */
    public function greyScale()
    {
        if (imagefilter($this->sourceImage, IMG_FILTER_GRAYSCALE))
            $this->objet->setSource($this->sourceImage);
    }

    /**
     * Ajoute du flou à l'image. Imagefilter remplace les calcul via matrice. 10x plus rapide
     * @param int $factor Facteur de flou
     * @throws \Exception
     */
    public function blur($factor)
    {
        if (imagefilter($this->sourceImage, IMG_FILTER_GAUSSIAN_BLUR, $factor))
            $this->objet->setSource($this->sourceImage);
    }

    /**
     * Ajoute du bruit à l'image. (relativement long a executer puisque traitement px par px)
     * @param int $factor paramètre de bruit (0-255)
     * @throws \Exception
     */
    public function addNoise($factor)
    {
        for ($x = 0; $x < $this->largeur; $x++) {
            for ($y = 0; $y < $this->hauteur; $y++) {
                $rand = mt_rand(-$factor, $factor);
                $rgb  = imagecolorat($this->sourceImage, $x, $y);
                $r    = (($rgb >> 16) & 0xFF) + $rand;
                $g    = (($rgb >> 8) & 0xFF) + $rand;
                $b    = ($rgb & 0xFF) + $rand;

                $color = imagecolorallocate($this->sourceImage, $r, $g, $b);
                imagesetpixel($this->sourceImage, $x, $y, $color);
            }
        }
        $this->objet->setSource($this->sourceImage);
    }

    /**
     * Netteté
     * @throws \Exception
     */
    public function sharppen()
    {
        if (imagefilter($this->sourceImage, IMG_FILTER_MEAN_REMOVAL))
            $this->objet->setSource($this->sourceImage);
    }

    /**
     * Modifie le contraste de l'image. Imagefilter remplace les calcul via matrice. 10x plus rapide
     * @access public
     * @param int $factor Facteur de flou
     * @throws \Exception
     */
    public function contrast($factor)
    {
        if (imagefilter($this->sourceImage, IMG_FILTER_CONTRAST, $factor))
            $this->objet->setSource($this->sourceImage);
    }

    /**
     * Modifie la luminosité. Imagefilter remplace les calcul via matrice. 10x plus rapide
     * @access private
     * @param int $factor Facteur de flou
     * @throws \Exception
     */
    public function brightness($factor)
    {
        if (imagefilter($this->sourceImage, IMG_FILTER_BRIGHTNESS, $factor))
            $this->objet->setSource($this->sourceImage);
    }

}