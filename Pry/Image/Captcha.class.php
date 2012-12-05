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
 * Génère une image de vérification (CAPTCHA).
 * <code>
 * $captcha = new Image_Captcha(300,80);
 * $captcha->setLength(4);
 * $captcha->setType(Image_Captcha::IMG_JPG);
 * $captcha->setBorderSize(2);
 * $captcha->addBorder('#000000');
 * $captcha->createCaptcha();
 * $captcha->display();
 * $_SESSION['captcha'] = $captcha->getRandomString();
 * </code> 
 * @category Pry
 * @package Image
 * @version 1.2.0
 * @author Olivier ROGER <oroger.fr>
 *
 */
class Captcha extends Image
{

    private $randString;
    private $randStringLength;
    private $borderWidth;
    private $border;
    private $shadow;
    private $tricky;
    private $char;
    private $maxFontSize;
    private $minFontSize;
    public $arrayOfColor;

    /**
     * Cosntructeur
     *
     * @param int $width
     * @param int $height
     * @access public
     */
    public function __construct($width, $height)
    {
        parent::__construct(null, $width, $height);
        $this->width  = $width;
        $this->height = $height;
        if ($width <= 0 || $height <= 0)
            throw new \RangeException('Une image doit avoir des dimension valides');

        $this->randStringLength = 10;
        $this->borderWidth      = 3;
        $this->maxFontSize      = 30;
        $this->minFontSize      = 20;
        $this->tricky           = false;
        $this->char             = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ12346789';
        $this->randString       = array();

        $this->setbgColor(255, 255, 255);
        $this->setFont('Acidic');
        $this->initTextColor();
    }

    /**
     * Défini la longeur de la chaine. 4 ou 5 semble un bon compromis
     * 
     * @access public
     * @param int $length Longeur de la chaine
     */
    public function setLength($length)
    {
        $this->randStringLength = $length;
    }

    /**
     * Défini la taille de la bordure de l'image
     *
     * @access public
     * @param int $size Taille en pixels
     */
    public function setBorderSize($size)
    {
        $this->borderWidth = $size;
    }

    /**
     * Renvoi la chaine générée pour enregistrment en session
     *
     * @access public
     * @return string
     */
    public function getRandomString()
    {
        return implode('', $this->randString);
    }

    /**
     * Génère la chaine aléatoire.
     * 
     * @access private
     */
    private function randomize()
    {
        for ($i = 0; $i < $this->randStringLength; $i++) {
            $this->randString[$i] = $this->char[mt_rand(0, 33)];
        }
    }

    /**
     * Défini des couleurs de base pour les lettres
     * 
     * @access private
     */
    private function initTextColor()
    {
        $this->arrayOfColor[0]['r'] = 215;
        $this->arrayOfColor[0]['v'] = 0;
        $this->arrayOfColor[0]['b'] = 0;

        $this->arrayOfColor[1]['r'] = 0;
        $this->arrayOfColor[1]['v'] = 128;
        $this->arrayOfColor[1]['b'] = 255;

        $this->arrayOfColor[2]['r'] = 0;
        $this->arrayOfColor[2]['v'] = 128;
        $this->arrayOfColor[2]['b'] = 0;

        $this->arrayOfColor[3]['r'] = 255;
        $this->arrayOfColor[3]['v'] = 0;
        $this->arrayOfColor[3]['b'] = 128;

        $this->arrayOfColor[4]['r'] = 128;
        $this->arrayOfColor[4]['v'] = 0;
        $this->arrayOfColor[4]['b'] = 128;

        $this->arrayOfColor[5]['r'] = 0;
        $this->arrayOfColor[5]['v'] = 255;
        $this->arrayOfColor[5]['b'] = 128;
    }

    /**
     * Ajoute du "bruit" à l'image pour la rendre difficile à lire par les robots
     *
     * @access private
     */
    private function moreNoise()
    {
        // Ajout d'ellipse et de courbe
        $max        = ceil($this->height / 20);
        if ($max < 1)
            $max        = 2;
        $maxElement = mt_rand(1, $max);
        for ($i = 0; $i < $maxElement; $i++) {
            $cx = mt_rand(0, $this->width);
            $cy = mt_rand(0, $this->height);

            $largeur = mt_rand(5, $this->width);
            $hauteur = mt_rand(5, $this->height);

            imageellipse($this->source, $cx, $cy, $largeur, $hauteur, $this->couleur);
            imageline($this->source, $cx, $cy, $largeur, $hauteur, $this->couleur);
        }
    }

    /**
     * Ajoute une bordure à l'image
     * 
     * @access public
     * @param string $color Couleur en hexa (#000000 , 0xFFFFFF)
     */
    public function addBorder($color)
    {
        $this->setBorder($this->borderWidth, $color);
    }

    /**
     * Active le bruit sur l'image
     * 
     * @access public
     */
    public function setTricky()
    {
        $this->tricky = true;
    }

    /**
     * Génère le captcha
     *
     * @access public
     */
    public function createCaptcha()
    {
        $this->randomize();
        $totalCol = count($this->arrayOfColor);
        $posX     = $this->width / 4;
        $posY     = ($this->height / 2) + ($this->maxFontSize - $this->minFontSize) + 5;
        for ($i = 0; $i < $this->randStringLength; $i++) {
            $rand         = mt_rand(0, $totalCol - 1);
            $randFontsize = mt_rand($this->minFontSize, $this->maxFontSize);
            $randAngle    = mt_rand(-40, 40);
            $this->setColor($this->arrayOfColor[$rand]['r'], $this->arrayOfColor[$rand]['v'], $this->arrayOfColor[$rand]['b']);
            $this->setText($this->randString[$i], $randFontsize, $posX, $posY, $randAngle);
            $posX += $randFontsize - 3;
            if ($this->tricky)
                $this->moreNoise();
        }
    }

}

?>