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
namespace Pry\Util;
/**
 * Classe d"identification de navigateur internet.
 * <code>
 * $ua = new UserAgent();
 * if($ua->isMobile()){echo 'Navigateur mobile';}
 * $ua->addMobile('Mon Mobile');
 * $mesMobiles = array('mobile 1', 'mobile2');
 * $ua->addMobile($mesMobiles);
 * $ua->removeMobile('Nokia');
 * </code>
 * @category Pry
 * @package Util
 * @author          Olivier ROGER <oroger@prynel.com>
 * @version         1.1.6
 *
 */
class UserAgent
{

    /**
     * User agent du navigateur
     * @var string
     * @access private
     */
    private $user_agent;

    /**
     * Tableau des entête mobile
     * @var array
     * @access private
     */
    private $tabOfMobile;

    /**
     * Tableau des mot clé pour smartphone
     *
     * @var array
     * @access private
     */
    private $tabOfSmart;

    /**
     * Tableau des navigateur de bureau
     * @var array
     * @access private
     */
    private $tabDesktopBrowser;

    /**
     * Taille du tableau des entêtes
     * @var int
     * @access private
     */
    private $tabSize;

    /**
     * Taille du tableau des navigateurs
     * @var int
     * @access private
     */
    private $tabSizeDesktopBrowser;

    /**
     * Nav Mobile ou non
     * @var boolean
     * @access private
     */
    private $mobile;

    /**
     * Navigateur sur smartphone ou non
     *
     * @var bool
     * @access private
     */
    private $smartphone;

    /**
     * Nav "desktop" ou non
     * @var boolean
     * @access private
     */
    private $desktop;

    /**
     * Le bavigateur de bureau utilisé
     * @var string
     * @access private
     */
    private $usedDesktop;

    /**
     * Le navigateur mobile détecté
     * @var string
     */
    private $usedMobile;

    /**
     * Constructeur
     * @param string $UA HTTP_USER_AGENT du navigateur
     */
    public function __construct($UA)
    {
        $this->user_agent   = $UA;
        $this->tabOfMobile  = array("Android",
            "Blazer",
            "Palm",
            "Handspring",
            "Nokia",
            "Kyocera",
            "Samsung",
            "Motorola",
            "Smartphone",
            "Windows CE",
            "Blackberry",
            "WAP",
            "SonyEricsson",
            "Pda",
            "Cldc",
            "Midp",
            "Symbian",
            "Ericsson",
            "Portalmmm",
            "PANTECH",
            "Bcdm",
            "Bvirtual",
            "Klondike",
            "Pocketpc",
            "Vodafone",
            "AvantGo",
            "Minimo",
            "iPhone",
            "iPod",
            "iPad",
            "HP",
            "LGE",
            "mmp",
            "Xda",
            "PSP",
            "phone",
            "mobile");
        $this->tabSize = count($this->tabOfMobile);
        $this->tabDesktopBrowser = array(
            "Chrome",
            "Netscape",
            "Safari",
            "Firefox",
            "Konqueror",
            "Epiphany",
            "Lynx",
            "MSIE",
            "Opera");
        $this->tabOfSmart = array("wap", "smartphone", "phone", "mmp", "symbian", "midp");
        $this->tabSizeDesktopBrowser = count($this->tabDesktopBrowser);
        $this->mobile = false;
        $this->desktop = false;
        $this->smartphone = false;
        $this->usedDesktop = 'Inconnu';
    }

    /**
     * Détécte si le navigateur est un navigateur d'appareil mobile;
     * @access public
     * @return boolean
     */
    public function isMobile()
    {
        for ($i = 0; $i < $this->tabSize; $i++) {
            if (!strripos($this->user_agent, $this->tabOfMobile[$i]) === FALSE)
            {
                $this->mobile = true;
                $this->usedMobile = $this->tabOfMobile[$i];
                break;
            }
        }
        if ($this->mobile == false)
        {
            $this->desktop = true;
            $this->quelDesktop();
        }
        return $this->mobile;
    }

    /**
     * Détecte si l'appareil est un Iphone//Ipod touch
     * return boolean
     */
    public function isIphone()
    {
        if ($this->isMobile())
        {
            if (strripos($this->user_agent, 'iPhone') || strripos($this->user_agent, 'iPod'))
                return true;
        }
        return false;
    }

    /**
     * Détecte si l'appareil est un iPad
     * @return boolean
     */
    public function isIpad()
    {
        if ($this->isMobile())
        {
            if (strripos($this->user_agent, 'iPad'))
                return true;
        }
        return false;
    }
    
    /**
     * Détecte si le device est un device android
     * @return boolean 
     */
    public function isAndroid()
    {
        if ($this->isMobile())
            if (strripos($this->user_agent, 'Android'))
                return true;

        return false;
    }

    /**
     * Détecte si l'appareil mobile est un smartphone. Si non c'est un pda
     * 
     * @access public
     * @return bool
     */
    public function isSmartphone()
    {
        $tailleSmart = count($this->tabOfSmart);
        for ($i = 0; $i < $tailleSmart; $i++) {
            if (!strripos($this->user_agent, $this->tabOfSmart[$i]) === FALSE)
            {
                $this->smartphone = true;
                break;
            }
        }
        return $this->smartphone;
    }

    /**
     * Détecte si le navigateur est un navigateur de bureau;
     * @access public
     * @return boolean
     */
    public function isDesktop()
    {
        if ($this->isMobile() === FALSE)
        {
            $this->desktop = true;
            $this->quelDesktop();
        }
        else
            $this->desktop = false;

        return $this->desktop;
    }

    /**
     * Retourne le navigateur de bureau utilisé
     * @access public
     * @return string
     */
    public function showDesktop()
    {
        if ($this->isDesktop() === true)
        {
            $nav = $this->usedDesktop;
        }
        else
        {
            $nav = 'Navigateur mobile';
        }
        return $nav;
    }

    /**
     * Retourne le navigateur mobile utilisé
     * @since 1.1.4
     * @return string
     */
    public function showMobile()
    {
        if ($this->isMobile())
            $nav = $this->usedMobile;
        else
            $nav = 'Desktop';

        return $nav;
    }

    /**
     * Retourne le navigateur de bureau utilisé
     * @access private
     *
     */
    private function quelDesktop()
    {
        for ($i = 0; $i < $this->tabSizeDesktopBrowser; $i++) {
            if (!strripos($this->user_agent, $this->tabDesktopBrowser[$i]) === FALSE)
            {
                $this->usedDesktop = $this->tabDesktopBrowser[$i];
                break;
            }
        }
    }

    /**
     * Ajoute un ou plusieur mobile à la liste des chaines de détection
     * @access public
     * @param string/array $input Chaine ou tableau de chaine représentant un mobile potentiel.
     */
    public function addMobile($input)
    {
        if (is_array($input))
        {
            foreach ($input as $value) {
                array_push($this->tabOfMobile, $value);
            }
        }
        else
        {
            array_push($this->tabOfMobile, $input);
        }
        $this->tabSize = count($this->tabOfMobile);
        return $this->tabOfMobile;
    }

    /**
     * Retire un mobile au tableau des chaines de détection
     * @access public
     * @param string $valeur Chaine ou tableau de chaine représentant un mobile potentiel.
     */
    public function removeMobile($valeur)
    {
        if (in_array($valeur, $this->tabOfMobile))
        {
            $key = array_search($valeur, $this->tabOfMobile);
            unset($this->tabOfMobile[$key]);
            $this->tabSize = count($this->tabOfMobile);
        }
    }

    /**
     * Retour le user agent
     * @since 1.1.4
     * @return string
     */
    public function get()
    {
        return $this->user_agent;
    }

}

?>