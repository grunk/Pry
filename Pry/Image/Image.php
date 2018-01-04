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
 * Gestion de base des images
 * @category Pry
 * @package Image
 * @version 1.5.0 
 * @author Olivier ROGER <oroger.fr>  
 *
 */
class Image
{

    const IMG_GIF = 1;
    const IMG_JPG = 2;
    const IMG_PNG = 3;
    const IMG_SWF = 4;
    const IMG_PSD = 5;
    const IMG_BMP = 6;
    const IMG_TIF = 7;

    /**
     * @access protected
     * @var int
     */
    protected $width;

    /**
     * @var int
     * @access protected
     */
    protected $height;

    /**
     * Type de l image : 1 = gif ; 2 = jpeg; 3= png
     * @var string 
     * @access protected
     * 
     */
    protected $type;

    /**
     * @var string $mime Type mime de l image
     * @access protected
     */
    protected $mime;

    /**
     * @var img Fichier image source
     * @access protected
     */
    protected $source;

    /**
     * @var string Police d'ecriture selectionnee. Defaut verdanna.
     * @access private
     */
    protected $font;

    /**
     * @var ressource Couleur choisi (par defaut bordeau)
     * @access protected
     */
    protected $couleur;

    /**
     * @var array Info de l'image
     * @access protected
     */
    protected $infoImage;

    /**
     * @var int Poids de l'image
     * @access protected
     */
    protected $poids;
    private $copie;

    /**
     * @var \Pry\Image\Image autre image a coller sur l image d origine
     * @access private
     */
    private $logo;

    /**
     * @var ressource Largeur du logo
     * @access private
     */
    private $widthL;

    /**
     * @var ressource Hauteur du logo
     * @access private
     */
    private $heightL;

    /**
     * @var ressource Type du logo
     * @access private
     */
    private $typeL;

    /**
     * Construteur
     * @param string $img Chemin vers l'image
     * @param int $w Largeur a fournir si aucune image source
     * @param int $h Hauteur a fournir si aucune image source
     */
    public function __construct($img, $w = null, $h = null)
    {
        if (!empty($img) && file_exists($img))
        {
            if (!is_readable($img))
            {
                throw new Image_Exception('Le fichier n\'est pas ouvert en lecture');
            }
            $info         = getimagesize($img);
            $this->width  = $info[0];
            $this->height = $info[1];
            $this->type   = $info[2]; // 1:gif; 2:jpg; 3:png; 4:swf; 5:psd; 6:bmp; 7:tiff
            $this->mime   = $info['mime'];
            $this->copie  = null;
            $this->source = $this->createFromType($img);
            if ($this->source == false)
            {
                throw new Exception('Format d\'image non supporté. Seul les formats JPG,PNG ou GIF sont admis');
            }
            $this->couleur   = imagecolorallocate($this->source, 187, 3, 33);
            $this->poids     = filesize($img);
            $this->infoImage = array();
            $this->infoImage['extension'] = strchr($img, '.');
            $this->infoImage['extension'] = substr($this->infoImage['extension'], 1); // Récupére l'extension après le .
            $this->infoImage['extension'] = strtolower($this->infoImage['extension']);
        }
        else
        {
            /**
             * Gestion de création d'image vierge
             * @since 1.2.0
             */
            if ($w > 0 && $h > 0)
            {
                $width        = intval($w);
                $height       = intval($h);
                $this->source = imagecreatetruecolor($width, $height);
                $this->width  = $width;
                $this->height = $height;
                $this->type   = null;
            }
            else
            {
                throw new Exception('Si aucune image source n\'est fournie la taille est obligatoire');
            }
        }
    }

    /**
     * Récupére les informations de l'image
     * @access public
     * @return array Information de l'image (reso,poids,extension,mime)
     */
    public function getInfo()
    {
        $this->infoImage['width']  = $this->width;
        $this->infoImage['height'] = $this->height;
        $this->infoImage['mime']   = $this->mime;
        $this->infoImage['poids']  = round($this->poids / 1024, 2);

        return $this->infoImage;
    }

    /**
     * Ajoute une image en tant que logo
     * @access public
     * @param string chemin vers l'image
     */
    public function addLogo($logo)
    {
        if (!file_exists($logo))
            throw new Exception('Le logo ne semble pas exister');

        $this->logo = new Image($logo);

        $tailleLogo    = $this->logo->getInfo();
        $this->widthL  = $tailleLogo['width'];
        $this->heightL = $tailleLogo['height'];
    }

    /**
     * Ajoute le logo à l'image principale
     * @access public
     * @param string $pos Position du logo : ct(centre),hg(haut gauche),hd,bg(bas gauche),bd(défaut)
     * @param int $opacite % d'opacité du logo , par défaut 75
     * @return bool 
     */
    public function mergeLogo($pos = 'bd', $opacite = 75)
    {
        if ($pos == 'hg')
        {
            $posX = 0;
            $posY = 0;
        }
        elseif ($pos == 'hd')
        {
            $posX = ($this->width - $this->widthL);
            $posY = 0;
        }
        elseif ($pos == 'bg')
        {
            $posX = 0;
            $posY = ($this->height - $this->heightL);
        }
        elseif ($pos == 'bd')
        {
            $posX = ($this->width - $this->widthL);
            $posY = ($this->height - $this->heightL);
        }
        elseif ($pos == 'ct')
        {
            $posX = (($this->width / 2) - ($this->widthL / 2));
            $posY = (($this->height / 2) - ($this->heightL / 2));
        }

        if (imagecopymerge($this->source, $this->logo->getSource(), $posX, $posY, 0, 0, $this->widthL, $this->heightL, $opacite))
            return true;
        else
            return false;
    }

    /**
     * Créer une ressource image selon son type
     * @access protected
     * @param string image
     * @return ressource image
     */
    protected function createFromType($img)
    {
        if ($this->type == self::IMG_GIF)
        {
            $crea = imagecreatefromgif($img);
            imagealphablending($crea, TRUE);
        }
        elseif ($this->type == self::IMG_JPG)
        {
            $crea = imagecreatefromjpeg($img);
        }
        elseif ($this->type == self::IMG_PNG)
        {
            $crea = imagecreatefrompng($img);
            imagealphablending($crea, true);
            imagesavealpha($crea, true);
        }
        else
            $crea = false;

        return $crea;
    }

    /**
     * Créer une copie de l image original pour restauration ulterieur.
     * @access public
     */
    public function duplicate()
    {
        $this->copie = imagecreatetruecolor($this->width, $this->height);
        imagecopy($this->copie, $this->source, 0, 0, 0, 0, $this->width, $this->height);
    }

    /**
     * Restaure la copie de sauvegarde de l'image
     * @access public
     */
    public function restore()
    {
        $this->source = $this->copie;
    }

    /**
     * Permet le changement de police. indiquer le chemin vers le fichier ttf
     * @access public
     * @param string $path Chemin vers la police ou non de la police dans le dossier /font
     */
    public function setFont($path)
    {
        $font       = new Font($path);
        $this->font = $font->utilise();
    }

    /**
     * Permet de définir une couleur à utiliser
     * @access public
     * @param int $r composante rouge
     * @param int $v composante verte
     * @param int $b composante bleue
     * @return int
     */
    public function setColor($r, $v, $b)
    {
        return $this->couleur = imagecolorallocate($this->source, $r, $v, $b);
    }

    /**
     * Remplie le fond d'une image avec une couleur
     * @since 1.2.0
     * @param int $r Composante Rouge
     * @param int $v Composante Verte
     * @param int $b Composante Bleu
     */
    public function setBgColor($r, $v, $b)
    {
        imagefill($this->source, 0, 0, $this->setColor($r, $v, $b));
    }

    /**
     * Determine le type d'image voulu
     * @since 1.2.0
     * @param int $type
     */
    public function setType($type)
    {
        if ($this->type == null)
            $this->type = intval($type);
    }

    /**
     * Ecrit un texte sur l image aux positions données
     * @access public
     * @param string $texte Texte à afficher
     * @param int $size Taille du texte
     * @param int $x Position en X
     * @param int $y Position en Y
     * @param int $angle Inclinaison du texte
     * @param bool $rect Ajout ou non d'un rectangle blanc sous le texte
     */
    public function setText($texte, $size, $x, $y, $angle = 0, $rect = false)
    {
        if ($rect)
        {
            $rectText = imageftbbox($size, 0, $this->font, $texte); // Retourne les coordoonées du text
            $wText    = abs($rectText[4] - $rectText[0]); // Largeur du texte
            $hText    = abs($rectText[1] - $rectText[5]); // hauteur du texte
            $this->setColor(255, 255, 255); // Fond blanc
            imagefilledrectangle($this->source, $x, ($y - $hText), ($x + $wText + 5), ($y + 5), $this->couleur); //($y-$htext) permet de placer
            // le rectangle au bon endroit
            $this->setColor(0, 0, 0); // Texte noir
            imagettftext($this->source, $size, $angle, $x, $y, $this->couleur, $this->font, $texte);
        }
        else
        {
            imagettftext($this->source, $size, $angle, $x, $y, $this->couleur, $this->font, $texte);
        }
    }

    /**
     * Permet la rotation d'une image
     *
     * @param int $angle
     * @since 1.4.1
     * @access public
     */
    public function rotate($angle)
    {
        $this->source = imagerotate($this->source, $angle, -1);
    }

    /**
     * Redimensionne l'image. Si une des deux dimension = 0. Redimensionnement proportionnel sur celle donnée
     * @param int $newW Largeur souahitée
     * @param int $newH Hauteur souhaitée
     * @access public
     * @return bool
     */
    public function resize($newW, $newH)
    {
        if ($newW == 0) // Largeur non spécifiée donc dimension basé sur hauteur
        {
            $scale = $newH / $this->height;
            $newW  = $this->width * $scale;
        }
        elseif ($newH == 0) // Hauteur non spécifiée donc dimension basé sur largeur
        {
            $scale   = $newW / $this->width;
            $newH    = $this->height * $scale;
        }
        $tempImg = imagecreatetruecolor($newW, $newH);

        //Evite fond noir avec la transparence GIF
        if (self::IMG_GIF == $this->type)
        {
            imagealphablending($tempImg, false);
            imagesavealpha($tempImg, true);
            $transparence = imagecolorallocatealpha($tempImg, 255, 255, 255, 127);
            imagefilledrectangle($tempImg, 0, 0, $newW, $newH, $transparence);
            imagecolortransparent($tempImg, $transparence);
        }

        if (self::IMG_PNG == $this->type)
        {
            $background = imagecolorallocate($tempImg, 0, 0, 0);
            imagecolortransparent($tempImg, $background); // Image temp totalement transparente
            imagealphablending($tempImg, false); // Pas d'alpha blending pour garder le channel alpha
        }

        if (imagecopyresampled($tempImg, $this->source, 0, 0, 0, 0, $newW, $newH, $this->width, $this->height))
        {
            $this->source = $tempImg;
            $this->width  = $newW;
            $this->height = $newH;
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Créer une miniature de l'image source.
     * Si l'image n'a pas le même format que la miniature , des bandes noires apparaitrons.
     *
     * @param int $newW Largeur de la miniature
     * @param int $newH Hauteur de la miniature
     * @param string $color Couleur en hexa du fond de la miniature
     * @since 1.4.5
     */
    public function miniaturise($newW, $newH, $color = "#000000")
    {
        $rgb     = $this->hexToRgb($color);
        $tempImg = imagecreatetruecolor($newW, $newH);
        $color   = imagecolorallocate($tempImg, $rgb[0], $rgb[1], $rgb[2]);
        imagefill($tempImg, 0, 0, $color);
        //La largeur est la plus grande valeur
        if ($this->width == max($this->width, $this->height))
        {
            $this->resize($newW, null);
            $dH           = $newH - $this->height;
            if (imagecopy($tempImg, $this->source, 0, ($dH / 2), 0, 0, $this->width, $this->height))
                $this->source = $tempImg;
            else
                throw new \RuntimeException('Création de la miniature impossible');
        }
        else
        {
            $this->resize(null, $newH);
            $dW           = $newW - $this->width;
            if (imagecopy($tempImg, $this->source, ($dW / 2), 0, 0, 0, $this->width, $this->height))
                $this->source = $tempImg;
            else
                throw new \RuntimeException('Création de la miniature impossible');
        }
    }

    /**
     * Crop une image aux dimensions voulues et à partir de l'endroit voulu
     *
     * @param int $cropW Largeur de la zone de crop
     * @param int $cropH Hauteur de la zone de crop
     * @param int $cropStartX Coordonnées en X de départ
     * @param int $cropStartY Coordonnées en Y de départ
     * @return bool
     */
    public function crop($cropW, $cropH, $cropStartX, $cropStartY)
    {
        $tempImg = imagecreatetruecolor($cropW, $cropH);
        //Evite le fond noir
        if (self::IMG_GIF == $this->type)
        {
            imagealphablending($tempImg, false);
            imagesavealpha($tempImg, true);
            $transparence = imagecolorallocatealpha($tempImg, 255, 255, 255, 127);
            imagefilledrectangle($tempImg, 0, 0, $cropW, $cropH, $transparence);
            imagecolortransparent($tempImg, $transparence);
        }

        if (imagecopyresized($tempImg, $this->source, 0, 0, $cropStartX, $cropStartY, $cropW, $cropH, $cropW, $cropH))
        {
            $this->source = $tempImg;
            $this->width  = $cropW;
            $this->height = $cropH;
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Convertit une valeur hexadecimal en couleur RGB
     *
     * @param string $color Couleur hexa nettoyer de tout caractère supplémentaires (0x,#,...)
     * @since 1.2.0
     * @return array
     */
    public static function hexToRgb($color)
    {
        if ($color[0] == '#')
        {
            $color = substr($color, 1);
        }
        else if ($color[1] == 'x')
        {
            $color = substr($color, 2);
        }
        $rgb   = array();
        $rgb[0] = hexdec(substr($color, 0, 2));
        $rgb[1] = hexdec(substr($color, 2, 2));
        $rgb[2] = hexdec(substr($color, 4, 2));
        return $rgb;
    }

    /**
     * Convertit une valeur RGB en valeur hexa
     *
     * @param array $rgb Tableau des valeurs rgb array(45,49,176);
     * @since 1.2.0
     * @return string
     */
    public static function RgbToHex($rgb)
    {
        for ($i = 0; $i < 2; $i++) {
            if ($rgb[$i] < 0 || $rgb[$i] > 255)
                throw new Exception('La valeur RGB est incorrecte (compris en 0 et 255');
        }

        return str_pad((dechex($rgb[0]) . dechex($rgb[1]) . dechex($rgb[2])), 6, "0", STR_PAD_LEFT);
    }

    /**
     * Créee une bordure autour de l'image
     *
     * @param int $border Taille en px de la bordure
     * @param string $color Couleur hexa de la bordure (#FFFFFF ou 0xFFFFFF)
     */
    public function setBorder($border, $color)
    {
        $couleur = $this->hexToRgb($color);
        $this->setColor($couleur[0], $couleur[1], $couleur[2]);
        // Trait vertical gauche
        imagefilledrectangle($this->source, 0, 0, $border, $this->height, $this->couleur);
        // Trait vertical droit
        imagefilledrectangle($this->source, $this->width - $border, 0, $this->width, $this->height, $this->couleur);
        // Trait horizontal haut
        imagefilledrectangle($this->source, 0, 0, $this->width, $border, $this->couleur);
        //Trait horizontal bas
        imagefilledrectangle($this->source, 0, $this->height - $border, $this->width, $this->height, $this->couleur);
    }

    /**
     * Sauvegarde l'image sur le disque
     * @access public
     * @param string $file Nom et chemin de fichier
     * @return bool
     */
    public function save($file, $qualite = 95)
    {
        if ($this->type == self::IMG_GIF)
        {
            if (imagegif($this->source, $file))
                return true;
            else
                return false;
        }
        elseif ($this->type == self::IMG_JPG)
        {
            if (imagejpeg($this->source, $file, $qualite))
                return true;
            else
                return false;
        }
        elseif ($this->type == self::IMG_PNG)
        {
            if (imagepng($this->source, $file))
                return true;
            else
                return false;
        }
    }

    /**
     * Affiche l'image sur la sortie standard
     * @access public
     * @return img
     */
    public function display($qualite = 100)
    {
        if ($this->type == self::IMG_GIF)
        {
            header("Content-type: image/gif");
            return imagegif($this->source);
        }
        elseif ($this->type == self::IMG_JPG)
        {
            header("Content-type: image/jpeg");
            return imagejpeg($this->source, null, $qualite);
        }
        elseif ($this->type == self::IMG_PNG)
        {
            header("Content-type: image/png");
            return imagepng($this->source);
        }
    }

    /**
     * Getter pour la ressource image
     * 
     * @since 1.3.0
     * @return resource
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Setter pour la resource image
     *
     * @param resource $resource
     * @since 1.3.0
     */
    public function setSource($resource)
    {
        if ($resource != null && is_resource($resource))
            $this->source = $resource;
        else
            throw new Exception('La ressource n est pas valide.');
    }

    /**
     * Destructeur
     */
    public function __destruct()
    {
        if (is_resource($this->source))
            imagedestroy($this->source);

        if ($this->copie != null)
            @imagedestroy($this->copie);
    }

}