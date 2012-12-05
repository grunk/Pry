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

use Pry\Form\Error;

/**
 * Element date. Permet de valider différent type de date dans un champs text
 * @category Pry
 * @package Form
 * @subpackage Form_Element
 * @version 1.1.0 
 * @author Olivier ROGER <oroger.fr>
 */
class Date extends Text
{

    /**
     * Format de date
     *
     * @var string
     * @access protected
     */
    protected $format;

    /**
     * Constructeur. Par défaut format fr
     *
     * @param string $nom
     * @param Form_Form $form
     * @access public
     */
    public function __construct($nom, $form)
    {
        parent::__construct($nom, $form);
        $this->format = 'fr';
    }

    /**
     * Défini le format de date
     *
     * @param string $format
     * @access public
     * @return Form_Element_Date
     */
    public function format($format)
    {
        $formatAutorise = array('fr', 'us', 'sql');
        if (in_array($format, $formatAutorise))
            $this->format = $format;
        else
            throw new \InvalidArgumentException('Format : "' . $format . '" de date non reconnu');

        return $this;
    }

    /**
     * Validation du contenu
     *
     * @param string $value
     * @access public
     * @return boolean
     */
    public function isValid($value)
    {
        if (parent::isValid($value))
        {
            if (!$this->required && empty($value))
                return true;
            switch ($this->format)
            {
                case 'fr' :
                    if (preg_match('`^\d{1,2}/\d{1,2}/(\d{4}|\d{2})$`', $value) > 0 ||
                            preg_match('`^\d{1,2}/\d{1,2}/(\d{4}|\d{2}) \d{1,2}:\d{1,2}:\d{1,2}$`', $value) > 0 ||
                            preg_match('`^\d{1,2}/\d{1,2}/(\d{4}|\d{2}) \d{1,2}:\d{1,2}$`', $value) > 0)
                    {
                        if (strpos($value, ':'))
                        {
                            $datetime = explode(' ', $value);
                            list($jour, $mois, $annee) = explode('/', $datetime[0]);
                            $minutes  = explode(':', $datetime[1]);
                            $heure    = $minutes[0];
                            $min      = (isset($minutes[1])) ? $minutes[1] : '00';
                            $sec      = (isset($minutes[2])) ? $minutes[2] : '00';
                            if (is_null($sec))
                            {
                                $sec = '00';
                            }
                            if (checkdate($mois, $jour, $annee) && $heure < 24 && $min < 60 && $sec < 60)
                                return true;
                        }
                        else
                        {
                            list($jour, $mois, $annee) = explode('/', $value);
                            if (checkdate($mois, $jour, $annee))
                                return true;
                        }
                    }
                case 'us' :
                    if (preg_match('`^\d{1,2}/\d{1,2}/(\d{4}|\d{2})$`', $value) > 0 ||
                            preg_match('`^\d{1,2}/\d{1,2}/(\d{4}|\d{2}) \d{1,2}:\d{1,2}:\d{1,2}$`', $value) > 0 ||
                            preg_match('`^\d{1,2}/\d{1,2}/(\d{4}|\d{2}) \d{1,2}:\d{1,2}$`', $value) > 0)
                    {
                        if (strpos($value, ':'))
                        {
                            $datetime = explode(' ', $value);
                            list($mois, $jour, $annee) = explode('/', $datetime[0]);
                            list($heure, $min, $sec) = explode(':', $datetime[1]);
                            if (is_null($sec))
                            {
                                $sec = '00';
                            }
                            if (checkdate($mois, $jour, $annee) && $heure < 24 && $min < 60 && $sec < 60)
                                return true;
                        }
                        else
                        {
                            list($mois, $jour, $annee) = explode('/', $value);
                            if (checkdate($mois, $jour, $annee))
                                return true;
                        }
                    }
                case 'sql' :
                    if (preg_match('`^(\d{4}|\d{2})-\d{1,2}-\d{1,2}$`', $value) > 0 ||
                            preg_match('`^(\d{4}|\d{2})-\d{1,2}-\d{1,2} \d{1,2}:\d{1,2}:\d{1,2}$`', $value) > 0 ||
                            preg_match('`^(\d{4}|\d{2})-\d{1,2}-\d{1,2} \d{1,2}:\d{1,2}$`', $value) > 0)
                    {
                        if (strpos($value, ':'))
                        {
                            $datetime = explode(' ', $value);
                            list($annee, $mois, $jour) = explode('/', $datetime[0]);
                            list($heure, $min, $sec) = explode(':', $datetime[1]);
                            if (is_null($sec))
                            {
                                $sec = '00';
                            }
                            if (checkdate($mois, $jour, $annee) && $heure < 24 && $min < 60 && $sec < 60)
                                return true;
                        }
                        else
                        {
                            list($annee, $mois, $jour) = explode('/', $value);
                            if (checkdate($mois, $jour, $annee))
                                return true;
                        }
                    }

                default :
                    $this->errorMsg = Error::NOTDATE;
                    return false;
            }
            $this->errorMsg = Error::NOTDATE;
            return false;
        }
    }

}

?>