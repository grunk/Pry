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

use Pry\Validate\Validate;

/**
 * Class représentant un élément de formulaire
 * @category Pry
 * @package Form
 * @version 1.0.7 
 * @author Olivier ROGER <oroger.fr>
 * @abstract
 * @todo Gestion de plusieurs validateurs
 *       
 *
 */
abstract class Field
{

    /**
     * Formulaire
     *
     * @var Form_Form Formulaire
     * @access protected
     */
    protected $form;

    /**
     * Contient le label de l'élément
     *
     * @var string
     * @access protected
     */
    protected $label;

    /**
     * Class à appliquer aux label
     *
     * @var string
     */
    protected $cssLabel;

    /**
     * Contient la valeur de l'élément
     *
     * @var string
     * @access protected
     */
    protected $value;

    /**
     * Classes CSS de l'élément
     *
     * @var array
     * @access protected
     */
    protected $class;

    /**
     * Liste des attributs de l'élément
     *
     * @var array
     * @access protected
     */
    protected $attrs;

    /**
     * Info à afficher dans un tooltips
     *
     * @var string
     * @access protected
     */
    protected $info;

    /**
     * Chemin de l'image servant au tooltips. 
     * Chemin à donner par rapport à la page
     *
     * @var string
     * @access protected
     */
    protected $imgInfo;

    /**
     * Saut de ligne après élément
     *
     * @var boolean
     * @access protected
     */
    protected $fieldNewLine;

    /**
     * Saut de ligne après label
     *
     * @var booleang
     * @access protected
     */
    protected $labelNewLine;

    /**
     * Elément requis
     *
     * @var boolean
     * @access protected
     */
    protected $required;

    /**
     * Message d'erreur
     *
     * @var string
     * @access protected
     */
    protected $errorMsg;

    /**
     * Class CSS à utiliser pour le message d'erreur
     *
     * @var string
     */
    protected $errorClass;

    /**
     * Validateur
     *
     * @var Validate_Validate
     * @since 1.0.1
     */
    protected $validator;

    /**
     * Constructeur
     *
     * @param string $nom
     * @param Form_Form $form
     * @access protected
     */
    protected function __construct($nom, $form)
    {
        $this->form     = $form;
        $this->label    = '';
        $this->cssLabel = '';
        $this->value    = '';
        $this->info     = '';
        $this->imgInfo  = 'data:image/gif;base64,R0lGODlhEAAQAPcAAAAAAP///zNVnzVWnjRTlTRQkjRSkjVRkDRVmTRQijRQiTRPiDVQiTRPhjVPhDVrsTVmq1Z7rp264KvC4a7F4/T3+zVpq1J6q1eAsl6FtF6Es16Fs12DsWOLu2OKuWCGtGmRwneg0JOz242qzZ+84J+836K93bTL57PK5rjN5l+Jt1yDsGSPvmKMumaQv2WPvmKKuGKKtmyYx2yWxXGdzGmSvnKezXGcy3Sez3Wgz3ql1Hqj0HKWvoKp1nidxYWr14mt1ZGz2Zm11Jez0aO/3aXA36jE4qK82qjD4ajC4LDJ5K/H47jL4dPf7dDc6tTg7luKuWeTwWmWxGmVw2mWw2iUwmiUwWaRvmyax2qXxGuYxWuYxHGdy3Gey22Yw3ek0Xai0Hml0XehzX2p1necw4Wr0Yyy2aC/4KK/3Z+72KvF3/X4+/T3+vz9/jV+vzV9vUWMzUeNz0eOzkmR0UyS0mKXyF6Rv2iczWqg0GuZxYGqz4au1IWs0Jy826rG3zV+vDuFxUGLy0CLyX2ny5C00pO31Ze62KbD20GOy3ejxXmlxqbD2pO40v7//////wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAI4ALAAAAAAQABAAAAjFAB0JHEiwoEFHdOAEEnRw4Bw8ZyhQKFHnzcE4ZlJI+NEjyIQ9DwrKMYNiTBgjSMB8McEHAsE7J3SEwLFmTRcuYopAGYiIhIgcNm4QQZMnz5YyhgYIBKRkBw0sWgI0omKlihc1BAS6WQImTxYpTyq4eMGihp8CAv/0ASJjSpQmbDp4gOGD0YGBdpLMAMGkTSMnGTYIiUDQgp4jV1qo0MBhxQhFCgoKGJSGTIwPPIYkcnAQAYZChxYRutCgoUADCRYwMM16YEAAOw==';
        $this->class    = array();
        $this->attrs = array();
        $this->validators = array();
        $this->required     = true;
        $this->fieldNewLine = true;
        $this->labelNewLine = true;
        $this->errorMsg     = null;
        $this->errorClass   = 'errorForm';
    }

    /**
     * Assigne un label à l'élément
     *
     * @param string $txt
     * @param string $css à appliquer
     * @access public
     * @return Form_Field
     */
    public function label($txt, $css = '')
    {
        $this->label    = $txt;
        $this->cssLabel = $css;
        return $this;
    }

    /**
     * Assigne une valeur à l'élément
     *
     * @param string $txt
     * @access public
     * @return Form_Field
     */
    public function value($txt = '')
    {
        $this->value = $this->sanitizedValue($txt);
        return $this;
    }

    /**
     * Assigne une id
     *
     * @param string $txt
     * @access public
     * @return Form_Field
     */
    public function id($txt)
    {
        $this->attrs['id'] = $txt;
        return $this;
    }

    /**
     * Ajoute une classe CSS
     *
     * @param string $css
     * @access public
     * @return Form_Field
     */
    public function addClass($css)
    {
        if (!in_array($css, $this->class))
            $this->class[] = $css;
        return $this;
    }

    /**
     * Ajoute un validateur
     *
     * @param string $nom Nom du validateur
     * @param array $options Option éventuelles
     * @param string $message Message personnalisé
     * @since 1.0.1
     * @return unknown
     */
    public function addValidator($nom, $options = null, $message = '')
    {
        if (!is_object($this->validator))
        {
            $this->validator = new Validate();
        }
        $this->validator->addValidator($nom, $options, $message);
        return $this;
    }

    /**
     * Défini l'élément comme requis ou non
     *
     * @param boolean $bool
     * @access public
     * @return Form_Field
     */
    public function required($bool = true)
    {
        if ($bool === true)
        {
            $this->required = true;
        }
        else
        {
            unset($this->attrs['required']);
            $this->required = false;
        }
        return $this;
    }

    /**
     * Désactive ou non l'élément
     *
     * @param boolean $bool
     * @access public
     * @return Form_Field
     */
    public function disabled($bool = true)
    {
        if ($bool === true)
            $this->attrs['disabled'] = 'disabled';
        else
            unset($this->attrs['disabled']);
        return $this;
    }

    /**
     * Active ou non la lecture seule
     *
     * @param boolean $bool
     * @access public
     * @return Form_Field
     */
    public function readonly($bool = true)
    {
        if ($bool === true)
            $this->attrs['readonly'] = 'readonly';
        else
            unset($this->attrs['readonly']);
        return $this;
    }

    /**
     * Défini une longeur maxi pour la value
     *
     * @param string $val
     * @access public
     * @return Form_Field
     */
    public function maxlength($val)
    {
        if (ctype_digit((string) $val) && $val > 0)
            $this->attrs['maxlength'] = $val;
        else
            unset($this->attrs['maxlength']);

        return $this;
    }

    /**
     * Récupère le name de l'élément
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->attrs['name'];
    }

    /**
     * Récupère la value de l élément
     *
     * @access public
     * @return string
     */
    public function getValue()
    {
        if (isset($this->attrs['value']))
            return $this->attrs['value'];
        else
            return '';
    }

    /**
     * Défini ou non un saut de ligne pour les label et les éléments
     *
     * @param boolean $label
     * @param boolean $field
     * @access public
     * @return Form_Field
     */
    public function newLine($label, $field)
    {
        $this->labelNewLine = $label;
        $this->fieldNewLine = $field;
        return $this;
    }

    /**
     * Nettoie la valeur recue
     *
     * @param string $value
     * @access public
     * @return string
     */
    public function sanitizedValue($value)
    {
        if (!is_array($value))
        {
            $value = trim($value);
            //Suppression des caractère non imprimable (on garde CR ,LF et TAB)
            $value = preg_replace('`[\x00\x08-\x0b\x0c\x0e\x19]`i', '', $value);
        }
        return $value;
    }

    /**
     * Valide l'élément
     *
     * @param string $valeur
     * @access public
     * @return boolean
     */
    public function isValid($valeur)
    {
        $valeur = $this->sanitizedValue($valeur);
        if (!$this->required && empty($valeur))
            return true;

        if (is_object($this->validator))
        {
            $validation = $this->validator->isValid($valeur);
            if ($validation !== true)
            {
                $this->errorMsg = $validation;
                return false;
            }
        }

        if ($this->required && $valeur != '')
        {
            if (isset($this->attrs['maxlength']))
            {
                if (isset($valeur[$this->attrs['maxlength']]))
                { // Utilisation du tableau de carac + sure que strlen à cause de l'encodage.
                    $this->errorMsg = Error::TOOLONG;
                    return false;
                }
            }
            return true;
        }
        elseif (!$this->required)
        {
            return true;
        }
        else
        {
            $this->errorMsg = Error::REQUIRED;
            return false;
        }
    }

    /**
     * Défini un tooltip d'aide sur l'élément
     *
     * @param string $message
     * @return Form_Field
     */
    public function info($message)
    {
        $this->info                 = $message;
        $this->form->listTooltips[] = $this->attrs['name'];
        return $this;
    }

    /**
     * Défini une image pour illustrer les tooltips
     *
     * @param string $img Chemin vers l'image
     * @return Form_Field
     */
    public function setImgInfo($img)
    {
        $this->imgInfo = $img;
        return $this;
    }

    /**
     * Défini une class css d'erreur pour le msg d'erreur
     *
     * @param string $error
     * @access public
     * @return Form_Field
     */
    public function setErrorClass($error)
    {
        $this->errorClass = $error;
        return $this;
    }

    /**
     * Construit l'attribut HTML class=""
     *
     * @access protected
     * @return string
     */
    protected function cssClass()
    {
        $css = implode(' ', $this->class);
        if ($css != '')
            $css = 'class="' . $css . '"';
        else
            $css = '';
        return $css;
    }

    /**
     * Défini un attribut de l'élément
     *
     * @param string $nom
     * @param string $valeur
     */
    public function setAttributes($nom, $valeur)
    {
        if (!isset($this->attrs[$nom]))
            $this->attrs[$nom] = $valeur;
        return $this;
    }

    /**
     * Linéarise les attributs
     *
     * @access public
     * @return string
     */
    protected function attrsToString()
    {
        $attributs = '';
        foreach ($this->attrs as $key => $value)
            $attributs .= $key . '="' . $value . '" ';

        return $attributs;
    }

    /**
     * Ecrit l'objet
     * 
     * @abstract
     * @access private
     */
    abstract public function __toString();
}