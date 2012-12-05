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
 * Créer un dégradé de couleur à partir d'une couleur de début et une de fin.
 * 
 * <code>
 * $image = new DegradeCouleur(110,110);
 * $image->startColor = $image->hexToRgb('28abe2');
 * $image->endColor   = $image->hexToRgb('2d3192');
 * print_r($image->startColor);
 * $image->direction  = DegradeCouleur::DEGRADE_HORIZONTAL;
 * $image->degrade();
 * $image->setType(DegradeCouleur::IMG_JPG);
 * $image->save('test.jpg');      
 * </code>
 * 
 * @category Pry
 * @package Image
 * @version 1.2.0 
 * @author Olivier ROGER <oroger.fr>
 * 
 */
class DegradeCouleur extends Image
{

    const DEGRADE_HORIZONTAL = 0;
    const DEGRADE_VERTICAL   = 1;
    const DEGRADE_DIAGONAL   = 2;

    /**
     * Direction du dégradé
     * @access public
     * @var int
     */
    public $direction;

    /**
     * Couleur de début
     * @access public
     * @var array
     */
    public $startColor;

    /**
     * Couleur de fin
     * @access public
     * @var array
     */
    public $endColor;

    /**
     * Largeur de l'image
     * @access private
     * @var int
     */
    private $largeur;

    /**
     * Hauteur de l'image
     * @access private
     * @var int
     */
    private $hauteur;

    /**
     * Constructeur
     *
     * @param int $width
     * @param int $height
     */
    public function __construct($width, $height)
    {
        parent::__construct(null, $width, $height);
        $this->largeur = $width;
        $this->hauteur = $height;
        if ($width <= 0 || $height <= 0)
            throw new \RangeException('Une image doit avoir des dimension valides');
    }

    /**
     * Lance la construction du dégradé
     *
     */
    public function degrade()
    {
        $this->setColor(255, 255, 255);

        // On défini le nombre de lignes à tracer en fonction du type de dégradé
        if ($this->direction == self::DEGRADE_HORIZONTAL)
        {
            $nbLine = $this->largeur;
        }
        elseif ($this->direction == self::DEGRADE_VERTICAL)
        {
            $nbLine = $this->hauteur;
        }
        else
        {
            $nbLine = $this->largeur + $this->hauteur;
        }

        for ($i = 0; $i < $nbLine; $i++) {
            //Calcul de chaque composante en fonction du pas défini par : 
            // Ligne*(Composante de fin - Composante de début )/ Nombre de ligne
            $red   = $this->startColor[0] + $i * ($this->endColor[0] - $this->startColor[0]) / $nbLine;
            $green = $this->startColor[1] + $i * ($this->endColor[1] - $this->startColor[1]) / $nbLine;
            $blue  = $this->startColor[2] + $i * ($this->endColor[2] - $this->startColor[2]) / $nbLine;

            //Attribution de la nouvelle couleur
            $this->setColor($red, $green, $blue);

            //Dessin de la ligne
            if ($this->direction == self::DEGRADE_HORIZONTAL)
            {
                //Dégradé Horizontal => dessin de ligne vertical
                imageline($this->source, $i, 0, $i, $this->hauteur, $this->couleur);
            }
            elseif ($this->direction == self::DEGRADE_VERTICAL)
            {
                imageline($this->source, 0, $i, $this->largeur, $i, $this->couleur);
            }
            else
            {
                imageline($this->source, max(0, ($i - $this->hauteur)), min($i, $this->hauteur), min($i, $this->largeur), max(0, ($i - $this->largeur)), $this->couleur);
            }
        }
    }

}

?>