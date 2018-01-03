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

use Pry\Util\Token;

/**
 * Class représentant un formulaire.
 * 
 * <code>
 * $form = new Form_Form('monForm');
 * $form->setPostedValue($_POST);
 * $form->action('form.php')
 * 	    ->setAttributes('onsubmit','');
 * 
 * $form->add('Text','nom')
 *    	->label('test')
 *  	->value('Mon nom')
 * 		->addClass('cssclass')
 * 		->minLength(3);
 * $form->add('Submit','envoi')
 * 		->id('envoiBtn')
 * 		->value('Envoyer');
 * 
 * if($form->isValid($_POST))
 * {
 * 		echo 'OK';
 * }
 * else
 * 		echo $form;
 * </code>
 * 
 * @category Pry
 * @package Form
 * @version 1.1.0 
 * @author Olivier ROGER <oroger.fr>
 *       
 */
class Form
{

    /**
     * Liste des champs du formulaire
     *
     * @var array
     * @access private
     */
    private $champs;

    /**
     * Liste des champs submit
     *
     * @var array
     * @access private
     */
    private $champs_submit;

    /**
     * Liste des champs hidden
     *
     * @var array
     */
    private $champs_hidden;

    /**
     * Contenu du formulaire après $_POST
     *
     * @var array
     * @access private
     */
    private $posted_value;

    /**
     * Liste des attributs du formulaire
     *
     * @var array
     * @access private
     */
    protected $attrs;

    /**
     * identifiant du formulaire
     *
     * @var string
     * @access protected
     */
    protected $uniqid;

    /**
     * Instance de formulaire
     *
     * @var array
     * @access protected
     */
    protected static $instances = array();

    /**
     * Liste des infos à afficher
     *
     * @var array
     * @access public
     */
    public $listTooltips;
    public $javascript;

    /**
     * Constructeur
     *
     * @param string $uniqid Id unique identifiant le formulaire
     * @param string $method
     * @access public
     */
    public function __construct($uniqid, $method = 'post')
    {
        $this->champs = array();
        $this->champs_submit = array();
        $this->champs_hidden = array();
        $this->attrs = array();
        $this->listTooltips = array();

        if ($uniqid !== FALSE && in_array($uniqid, self::$instances))
            throw new \Exception('Une instance de ce formulaire semble déjà exister');
        else
        {
            $this->uniqid = $uniqid;
            self::$instances[] = $this->uniqid;
            $this->method($method);
            $this->add('Hidden', 'uniqid')
                    ->value($this->uniqid);
            if (!$this->isSubmited(true))
            {
                Token::genToken(30);
                $token = Token::getToken();
            }
            else
                $token = $_POST['csrf_protect'];
            $this->add('Hidden', 'csrf_protect')->value($token);
        }
    }

    /**
     * Valide le formulaire
     *
     * @param array $post
     * @param boolean $noSubmit true si aucun bouton submit.
     * Permet de valider le formulaire avec un envoi javascript
     * @access public
     * @return boolean
     */
    public function isValid($post, $noSubmit = false)
    {
        if ($this->isSubmited($noSubmit) && Token::checkToken())
        {
            $valid = true;
            foreach ($this->champs as $objet) {
                $nom        = $objet->getName();
                if (strstr($nom, '[]')) // Cas des selection multiple
                    $nom        = substr($nom, 0, strlen($nom) - 2);
                if (!isset($post[$nom]))
                    $post[$nom] = null;
                $valid      = $objet->isValid($post[$nom]) && $valid;
            }
            return $valid;
        }
        return false;
    }

    /**
     * Ajoute un élément de formulaire
     *
     * @param string $type
     * @param string $nom
     * @access public
     * @return Form_Input
     */
    public function add($type, $nom)
    {
        if (!isset($this->champs[$nom]))
        {
            $classChamps = 'Pry\Form\Element\\' . $type;
            $oChamps     = new $classChamps($nom, $this);
            if ($type != 'Html')
            {
                if ($type == 'Submit')
                    $this->champs_submit[$nom] = $oChamps;
                elseif ($type == 'Hidden')
                    $this->champs_hidden[$nom] = $oChamps;

                $this->champs[$nom]            = $oChamps;
            }
            else
                $this->champs[$nom . uniqid()] = $oChamps;
            return $oChamps;
        }
        else
            throw new \UnexpectedValueException('Un champs avec le nom ' . $nom . ' existe déjà dans ' . $this->uniqid);
    }

    /**
     * Attribue une action
     *
     * @param string $action
     * @access public
     * @return Form_Form
     */
    public function action($action)
    {
        $this->attrs['action'] = $action;
        return $this;
    }

    /**
     * Setter d'attribut
     *
     * @param string $nom
     * @param string $valeur
     * @access public
     * @return Form_Form
     */
    public function setAttributes($nom, $valeur)
    {
        if (!isset($this->attrs[$nom]))
            $this->attrs[$nom] = $valeur;
        return $this;
    }

    /**
     * Enregistre les valeur poster pour réutilisation
     *
     * @param array $data
     * @access public
     * @return Form_Form
     */
    public function setPostedValue($data)
    {
        if (is_array($data))
        {
            foreach ($data as $name => $valeur) {
                //var_dump($valeur);
                $this->posted_value[$name] = $valeur;
            }
        }
        return $this;
    }

    /**
     * Récupère la valeur postée pour un élément
     *
     * @param string $name
     * @access public
     * @return string
     */
    public function getPostedvalue($name)
    {
        if (isset($this->posted_value[$name]))
            return $this->posted_value[$name];
        else
            return '';
    }

    /**
     * Attribue une méthode au formulaire
     *
     * @param string $method
     * @access public
     * @return Form_Form
     */
    public function method($method)
    {
        $method = strtolower($method);
        if (in_array($method, array('post', 'get')))
        {
            $this->attrs['method'] = $method;
            return $this;
        }
        else
            throw new \Exception('Merci d\'utiliser post ou get');
    }

    /**
     * Attribue un enctype au formulaire
     *
     * @param string $txt
     * @access public
     * @return Form_Form
     */
    public function enctype($txt)
    {
        if (in_array($txt, array('multipart/form-data', 'application/x-www-form-urlencoded')))
            $this->attrs['enctype'] = $txt;
        else
            throw new \InvalidArgumentException('Enctype non supporté.');
        return $this;
    }

    /**
     * Vérifie si le formulaire à bien été soumis
     * 
     * @access private
     * @param boolean $noSubmit true si aucun bouton submit
     * @return boolean
     */
    private function isSubmited($noSubmit)
    {
        $methode = ($this->attrs['method'] == 'post') ? $_POST : $_GET;
        if (!empty($methode['uniqid']) && $methode['uniqid'] == $this->uniqid)
        {
            if ($noSubmit)
                return true;

            foreach ($this->champs_submit as $submit) {
                if (isset($methode[$submit->getName()]))
                    return true;
            }
        }
        return false;
    }

    /**
     * Linéarise les attributs du formulaire
     * @access private
     * @return string
     */
    private function attrsToString()
    {
        $attributs = '';
        foreach ($this->attrs as $key => $value)
            $attributs .= $key . '="' . $value . '" ';

        return $attributs;
    }

    /**
     * Retourne une chaine des différents élément du formulaire
     * @access private
     * @return string
     */
    private function fieldsToString()
    {
        $champsTxt = '';
        foreach ($this->champs as $champ)
            $champsTxt .= $champ->__toString() . "\n";

        return $champsTxt;
    }

    /**
     * Ecriture du formulaire et ajout des appel JS
     * @access private
     * @return string
     */
    public function __toString()
    {
        $form = '<form ' . $this->attrsToString() . ' >' . "\n";
        $form.='<p>' . "\n";
        $form.= $this->fieldsToString();
        $form.='</p>' . "\n";
        $form.='</form>' . "\n";
        $form.='<script type="text/javascript" >' . "\n";
        $form .= '$(document).ready(function(){';
        if (!empty($this->javascript))
        {
            $form.= $this->javascript;
        }
        if (count($this->listTooltips) > 0)
        {
            $form.= '$(\'.form_tooltip\').tipTip();';
        }
        $form.='});';
        $form.='</script>' . "\n";
        return $form;
    }

}