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

namespace Pry\Form\Element;

use Pry\Form\Input;
use Pry\Form\Error;

/**
 * Element File. Selection de fichier
 * 
 * @category Pry
 * @package Form
 * @subpackage Form_Element
 * @version 1.1.2 
 * @author Olivier ROGER <oroger.fr>    
 *
 */
class File extends Input
{

    /**
     * Taille en octet maximal pour le fichier envoyer
     *
     * @var int
     * @access private
     */
    private $maxFileSize;

    /**
     * Tableau des extensions autorisées
     *
     * @var array
     * @access private
     */
    private $extension;

    /**
     * Nom de l'élément pour multiple
     *
     * @var string
     */
    private $name;

    /**
     * Nombre d'input à afficher en cas de multiple
     *
     * @var int
     * @since 1.1.0
     */
    private $nbInput;

    /**
     * Upload multiple ?
     *
     * @var boolean
     * @since 1.1.0
     */
    private $multiple;

    /**
     * Constructeur. Par defaut extension jpg,png,gif et taille de 2Mo
     *
     * @param string $nom
     * @param Form_Form $form
     * @access public
     */
    public function __construct($nom, $form)
    {
        parent::__construct($nom, $form);
        $this->name          = $nom;
        $this->nbInput       = 1;
        $this->multiple      = false;
        $this->attrs['type'] = 'file';
        $this->form->enctype('multipart/form-data');
        $this->maxFileSize   = 2097152; //2Mo
        $this->extension     = array('jpg', 'gif', 'png');
    }

    /**
     * Défini la taille maximal de fichier autorisé
     *
     * @param int $size
     * @access public
     * @return Form_Element_File
     */
    public function maxFileSize($size)
    {
        $this->maxFileSize = $size;
        return $this;
    }

    /**
     * Supprime toutes les extensions préenregistrées
     *
     * @access public
     * @return Form_Element_File
     */
    public function flushAllowedFileType()
    {
        $this->extension = array();
        return $this;
    }

    /**
     * Défini le type file comme multiple
     *
     * @param int $nb Nombre d'input
     * @since 1.1.0
     * @return Form_Element_File
     */
    public function multiple($nb = 1)
    {
        $this->nbInput       = intval($nb);
        $this->multiple      = true;
        $this->attrs['name'] = $this->attrs['name'] . '[]';
        return $this;
    }

    /**
     * Ajoute une ou des extensions à accepter
     *
     * @param mixed $newExt
     * @access public
     * @return Form_Element_File
     */
    public function allowFileType($newExt)
    {
        if (is_array($newExt))
        {
            foreach ($newExt as $value) {
                if (!in_array($value, $this->extension))
                    $this->extension[] = $value;
            }
        }
        else
        if (!in_array($newExt, $this->extension))
            $this->extension[] = $newExt;

        return $this;
    }

    /**
     * Validation
     *
     * @param string $value
     * @access public
     * @return boolean
     */
    public function isValid($value)
    {
        if (isset($_FILES[$this->name]))
        {
            if (!$this->multiple)
            {
                if (parent::isValid($_FILES[$this->name]['name']))
                {
                    if (!$this->required && $_FILES[$this->name]['tmp_name'] == '')
                    {
                        return true;
                    }
                    //Taille
                    if ($this->maxFileSize < $_FILES[$this->name]['size'])
                    {
                        $this->errorMsg = Error::TOOBIG;
                        return false;
                    }
                    //Bien envoyé ?
                    if (!is_uploaded_file($_FILES[$this->name]['tmp_name']) || $_FILES[$this->name]['error'] != 0)
                    {
                        $this->errorMsg = Error::UPLOAD;
                        return false;
                    }
                    //Extension
                    if (!empty($this->extension))
                    {
                        $extension = pathinfo($_FILES[$this->name]['name'], PATHINFO_EXTENSION);
                        if (!in_array($extension, $this->extension))
                        {
                            $this->errorMsg = Error::EXT;
                            return false;
                        }
                    }
                    return true;
                }
            }
            else
            {
                $return = true;
                for ($i = 0; $i < $this->nbInput; $i++) {
                    if (parent::isValid($_FILES[$this->name]['name'][$i]))
                    {
                        if (!$this->required && $_FILES[$this->name]['tmp_name'][$i] == '')
                        {
                            $return = $return && true;
                            break;
                        }
                        else if ($this->required && $_FILES[$this->name]['tmp_name'][$i] == '')
                        {
                            $return         = $return && false;
                            $this->errorMsg = Error::REQUIRED;
                        }
                        //Taille
                        if ($this->maxFileSize < $_FILES[$this->name]['size'][$i])
                        {
                            $this->errorMsg = Error::TOOBIG;
                            $return         = $return && false;
                        }
                        //Bien envoyé ?
                        if (!is_uploaded_file($_FILES[$this->name]['tmp_name'][$i]) || $_FILES[$this->name]['error'][$i] != 0)
                        {
                            $this->errorMsg = Error::UPLOAD;
                            $return         = $return && false;
                        }
                        //Extension
                        if (!empty($this->extension))
                        {
                            $extension = pathinfo($_FILES[$this->name]['name'][$i], PATHINFO_EXTENSION);
                            if (!in_array($extension, $this->extension))
                            {
                                $this->errorMsg = Error::EXT;
                                $return         = $return && false;
                            }
                        }
                        $return         = $return && true;
                    }
                    else
                    {
                        $return = $return && false;
                    }
                }
                return $return;
            }
        }
        return false;
    }

    /**
     * Ecriture de l'objet
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        $css   = $this->cssClass();
        $label = '';
        //LABEL
        if (!empty($this->label))
        {
            $label     = "\t" . '<label for="' . $this->attrs['id'] . '" class="' . $this->cssLabel . '">' . $this->label . '</label>' . "\n";
            if ($this->labelNewLine)
                $label.="\t" . '<br />' . "\n";
        }
        //INPUT DE BASE
        $attributs = $this->attrsToString();
        $field     = "\t" . '<input ' . $css . ' ' . $attributs . ' />' . "\n";
        //INFOS
        if (!empty($this->info))
            $field.="\t" . '<img src="' . $this->imgInfo . '" id="' . $this->attrs['name'] . '_tooltip" class="form_tooltip" title="' . $this->info . '" alt="" style="cursor:help;" />';
        //AUTRES INPUTS	
        if ($this->multiple && $this->nbInput > 1)
        {
            for ($i = 0; $i < ($this->nbInput - 1); $i++) {
                $field.="\t" . '<br /><input ' . $css . ' ' . $attributs . ' />' . "\n";
            }
        }
        //MAX FILE SIZE
        $field.="\t" . '<input type="hidden" name="MAX_FILE_SIZE" value="' . $this->maxFileSize . '" />' . "\n";
        if ($this->fieldNewLine)
            $field.="\t" . '<br />' . "\n";
        $error = '';
        //ERROR
        if (!is_null($this->errorMsg))
        {
            $error = '<span class="' . $this->errorClass . '">' . $this->errorMsg . '</span><br />';
        }
        return $label . $field . $error;
    }

}

?>