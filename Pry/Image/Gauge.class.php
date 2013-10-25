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

define('RESOURCEFOLDER', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'resource');

/**
 * Dessine une jauge type jauge à essence.
 * Inspiré de Stephen Powis PHPDialGauge
 * <code>
 * $gauge = new Image_gauge(50);
 * $gauge->draw();
 * $gauge->display();
 * </code>
 * @category Pry
 * @package Image
 * @version 1.1.0 
 * @author Olivier ROGER <oroger.fr>
 */
class Gauge extends Image
{

    private $gauge_needle;
    private $gauge;
    private $blank;
    private $value;
    private $max;
    private $min;

    /**
     * Constructeur. Définition des images de base
     *
     * @param int $value Valeur à afficher
     * @param int $min Valeur minimale
     * @param int $max Valeur maximale
     */
    public function __construct($value, $min = 0, $max = 100)
    {
        $this->value        = $value;
        $this->max          = $max;
        $this->min          = $min;
        $this->gauge_needle = RESOURCEFOLDER . DIRECTORY_SEPARATOR . 'gauge_needle.png';
        $this->gauge        = RESOURCEFOLDER . DIRECTORY_SEPARATOR . 'gauge_blank.png';
        $this->blank        = RESOURCEFOLDER . DIRECTORY_SEPARATOR . 'blank.png';
    }

    /**
     * Défini une image de jauge
     *
     * @param string $jauge chemin vers l image
     * @access public
     */
    public function setGauge($jauge)
    {
        if (file_exists($jauge))
            $this->gauge = $jauge;
        else
            throw new Exception('Le fichier ' . $jauge . ' image n\'existe pas');
    }

    /**
     * Défini l image de l'aiguille
     *
     * @param string $aiguille Chemin vers l'image de l'aiguille
     * @access public
     */
    public function setNeedle($aiguille)
    {
        if (file_exists($aiguille))
            $this->gauge_needle = $aiguille;
        else
            throw new Exception('Le fichier ' . $aiguille . ' image n\'existe pas');
    }

    /**
     * Dessine la jauge
     * @access public
     */
    public function draw()
    {
        parent::__construct($this->gauge);
        $this->setType(parent::IMG_PNG);

        $this->setFont('arial');
        $this->setColor(231, 34, 34);
        $this->setText($this->min, 8, 44, 117);
        $this->setColor(23, 140, 4);
        $this->getValueColor();
        $this->setText($this->max, 8, 105, 117);
        $this->setText($this->value . '%', 14, $this->computeXposValue(), 145);

        $angle        = $this->computeAngle();
        $needle       = new Image($this->gauge_needle);
        $tailleNeedle = $needle->getInfo();
        $needle->rotate(-$angle);

        $fond = $this->getSource();

        $aiguille         = $needle->getSource();
        $tailleNewNeedleX = imagesx($aiguille);
        $tailleNewNeedleY = imagesy($aiguille);


        $gauge = imagecreatefrompng($this->blank);
        imageAlphaBlending($gauge, true);
        imageSaveAlpha($gauge, true);
        //Crop de l aiguille
        imagecopy($gauge, $aiguille, 0, 0, round(($tailleNewNeedleX - $tailleNeedle['width']) / 2) + 33, round(($tailleNewNeedleY - $tailleNeedle['height']) / 2) + 33, $tailleNeedle['width'], $tailleNeedle['height']);
        //position au centre
        imagecopy($fond, $gauge, 0, 0, 0, 0, 165, 165);
        $this->setSource($fond);
    }

    /**
     * Calcul l'angle de rotation
     *
     * @return int
     * @access private
     */
    private function computeAngle()
    {
        return (($this->value - $this->min) * 260) / ($this->max - $this->min);
    }

    /**
     * Attribue une couleur au texte de la valeur
     * @access private;
     *
     */
    private function getValueColor()
    {
        if ($this->value < 30)
            $this->setColor(231, 34, 34); // ROUGE
        elseif ($this->value > 70)
            $this->setColor(23, 140, 4); //VERT
        else
            $this->setColor(237, 237, 9); //Jaune
    }

    /**
     * Défini une postion pour le texte de la valeur en fonction de sa taille
     *
     * @return int
     * @access private
     */
    private function computeXposValue()
    {
        $taille = strlen($this->value);
        switch ($taille)
        {
            case 1:
                $pos = 70;
                break;
            case 2:
                $pos = 70;
                break;
            case 3:
                $pos = 60;
                break;
            default :
                $pos = 70;
                break;
        }
        return $pos;
    }

}