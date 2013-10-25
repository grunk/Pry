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

define('FONTFOLDER', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'font');

/**
 * Retourne la police choisie
 * @category Pry
 * @package Image
 * @version 1.0.1 
 * @author Olivier ROGER <oroger.fr>
 *
 */
class Font
{

    private $fontname;

    /**
     * Constructeur
     *
     * @param string $name Nom ou chemin de la font
     */
    public function __construct($name)
    {
        $this->fontname = $name;
        $this->checkName();
    }

    /**
     * Vérifie l'existance de la police , sinon essai de la trouver dans le dossier font
     *
     */
    private function checkName()
    {
        $fileInfo = pathinfo($this->fontname);
        if (!file_exists($this->fontname))
        {
            $this->fontname = FONTFOLDER . DIRECTORY_SEPARATOR . $fileInfo['basename'];
            if (!isset($fileInfo['extension']))
                $this->fontname.='.ttf';
        }
    }

    /**
     * Retourne le nom de la police et son chemin complet
     *
     * @return string
     */
    public function utilise()
    {
        return $this->fontname;
    }

    /**
     * Liste toutes les polices disponibles dans le dossier font sous forme d'une image
     * <code>echo Image_Font::listFont()</code>
     * @return image
     */
    public static function listFont()
    {
        $file      = new File_FolderManager(FONTFOLDER . DIRECTORY_SEPARATOR);
        $listeFont = $file->listFile();

        $nbPolice = count($listeFont);
        $image    = new Image(null, 200, ($nbPolice * 14) + 14);
        $image->setType(Image::IMG_JPG);
        $image->setBgColor(255, 255, 255);
        $image->setColor(0, 0, 0);

        for ($i = 0; $i < $nbPolice; $i++) {
            $image->setFont($listeFont[$i]['name']);
            $image->setText($listeFont[$i]['name'], 14, 0, (18 * $i));
        }

        //return $image->save('listeFont.jpg');
        return $image->display();
    }

}