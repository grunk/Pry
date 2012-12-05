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

namespace Pry\Net;

/**
 * Classe requête permettant la récupération des paramètres et l'ajout de filtre
 * @category Pry
 * @package Net
 * @author Olivier ROGER <oroger.fr>
 *
 */
class Request
{

    protected $headers;
    protected $get;
    protected $post;
    protected $cookie;
    protected $request;
    protected $file;
    protected $filters;
    protected $defaultMethod = 'request';

    public function __construct()
    {
        $this->get     = $_GET;
        $this->post    = $_POST;
        $this->cookie  = $_COOKIE;
        $this->file    = $_FILES;
        $this->request = $_REQUEST;
    }

    /**
     * Réinitialise l'objet request
     */
    public function reset()
    {
        $this->get     = $_GET;
        $this->post    = $_POST;
        $this->cookie  = $_COOKIE;
        $this->file    = $_FILES;
        $this->request = $_REQUEST;
        $this->headers = null;
    }

    /**
     * Retourne l'ensemble des entêtes de la requête
     * @return array
     */
    public function getHeaders()
    {
        if (empty($this->headers))
            $this->headers = getallheaders();

        return $this->headers;
    }

    /**
     * Retourne un header en particulier
     * @param string $name Nom du header
     * @return string Valeur du header ou null si l'entête n'existe pas.
     */
    public function getHeader($name)
    {
        $this->getHeaders();
        return (isset($this->headers[$name])) ? $this->headers[$name] : null;
    }

    /**
     * Récupère une variable $_SERVER
     * @param string $name Nom de la variable à récupérer. Si null la totalité des variables sera retournée
     * @return mixed Retourne une string en cas de valeur unique un array sinon
     */
    public function getServer($name = null)
    {
        if (!empty($name))
            return (isset($_SERVER[$name])) ? $_SERVER[$name] : null;

        return $_SERVER;
    }

    /**
     * Retourne un paramètre de la requête. Le paramètres pourra être filtré si des filtres ont été défini
     * @param string $name Nom du paramètre
     * @param string $type Type de requête. Peut être get|post|request|cookie
     * @return mixed
     */
    public function getParam($name, $type = null)
    {
        $type = empty($type) ? $this->defaultMethod : strtolower($type);

        if (!$this->isValidMethod($type))
            throw new \InvalidArgumentException('Type de paramètre invalide');

        if (!empty($this->filters[$type]) && $this->filters[$type]['isFiltered'] == false)
            $this->applyFilters($type);

        return (isset($this->{$type}[$name])) ? $this->{$type}[$name] : null;
    }

    /**
     * Retourne l'ensemble des pramètres de type $type
     * @param string $type Peut être get|post|request|cookie
     * @return array
     * @throws InvalidArgumentException
     */
    public function getParams($type = null)
    {
        $type = empty($type) ? $this->defaultMethod : strtolower($type);

        if (!$this->isValidMethod($type))
            throw new \InvalidArgumentException('Type de paramètre invalide');

        if (!empty($this->filters[$type]) && $this->filters[$type]['isFiltered'] == false)
            $this->applyFilters($type);

        return $this->$type;
    }

    /**
     * Récupère une valeur POST
     * @param string $name Nom de la valeur POST
     * @param string $dataType Type de données pour appliquer un filtres.
     * Types autorisés int,float,string,email,url
     * @return mixed
     */
    public function getPost($name, $dataType = null)
    {
        return $this->getWithFilter($name, 'post', $dataType);
    }

    /**
     * Récupère une valeur GET
     * @param string $name Nom de la valeur GET
     * @param string $dataType Type de données pour appliquer un filtres.
     * Types autorisés int,float,string,email,url
     * @return mixed
     */
    public function get($name, $dataType = null)
    {
        return $this->getWithFilter($name, 'get', $dataType);
    }

    /**
     * Récupère une variable d'environnement
     * @param string $name
     */
    public function getEnv($name)
    {
        return getenv($name);
    }

    /**
     * Retourne la variable $_FILES demandé
     * @param string $name nom du file
     * @return array
     */
    public function getFile($name)
    {
        return isset($this->file[$name]) ? $this->file[$name] : null;
    }

    /**
     * Retourne $_FILES
     * @return array
     */
    public function getFiles()
    {
        return $this->file;
    }

    /**
     * Ajoute un filtre à appliquer lors de la récupération de paramètre.
     * <code>
     * setFilter(
     *      array(
     *         'id' => FILTER_SANITIZE_NUMBER_INT,
     *         'nom'=> FILTER_SANITIZE_STRING
     *      ),'post'
     * );
     * </code>
     * @param array $filtre Description du filtre. Doit être compatible avec filter_var_array
     * @param string $type Le type de requête
     * @see http://github.com/bdelespierre/php-axiom/tree/master/libraries/axiom/axRequest.class.php
     * @see http://php.net/manual/fr/function.filter-var-array.php
     * @return \Controller_Request
     * @throws InvalidArgumentException En cas de type invalide
     */
    public function setFilter(array $filtre, $type = null)
    {
        $type = empty($type) ? $this->defaultMethod : strtolower($type);

        if (!$this->isValidMethod($type))
            throw new \InvalidArgumentException('Type de paramètre invalide');

        $this->filters[$type] = array(
            'filter' => $filtre,
            'isFiltered' => false
        );

        return $this;
    }

    /**
     * Défini la méthode par défaut à utiliser. request est utilisé de base.
     * Cela agit directement sur les méthode getParam() , getParams() , setFilter() , quand le 
     * paramètre de méthode n'est pas fournit
     * @param string $name Nom de la méthode parmis get|post|cookie|request
     */
    public function setDefaultMethod($name)
    {
        $this->defaultMethod = strtolower($name);
    }

    public function __get($name)
    {
        return $this->getParam($name);
    }

    public function add($params, $type = null)
    {
        $type = empty($type) ? $this->defaultMethod : strtolower($type);

        $this->{$type} = array_merge($this->{$type}, $params);

        if (isset($this->filters[$type]))
            $this->filters[$type]['isFiltered'] = false;
    }

    /**
     * Vérifie la validité de la méthode demandé
     * @param string $method
     * @return boolean
     */
    protected function isValidMethod($method)
    {
        return in_array($method, array('get', 'post', 'request', 'cookie'));
    }

    /**
     * Applique les filtres défini
     * @param string $type le type de requête
     * @return boolean
     * @throws RuntimeException Si les filtres échoue
     */
    protected function applyFilters($type)
    {
        if (empty($this->filters[$type]) || empty($this->$type))
            return false;

        $this->$type = filter_var_array($this->$type, $this->filters[$type]['filter']);

        if (!$this->$type)
            throw new \RuntimeException('Filtres invalide');

        $this->filters['isFiltered'] = true;
    }

    /**
     * Récupère un paramètre en appliquant un filtres particulier
     * @param string $name Nom du paramètre
     * @param string $type Type de paramètre
     * @param string $dataType Type de données attendu
     */
    protected function getWithFilter($name, $type, $dataType)
    {
        $type = empty($type) ? $this->defaultMethod : strtolower($type);
        if (isset($this->{$type}[$name]))
        {
            switch ($dataType)
            {
                case 'int' :
                    return intval($this->{$type}[$name]);
                    break;
                case 'float' :
                    return floatval($this->{$type}[$name]);
                    break;
                case 'string' :
                    return filter_var($this->{$type}[$name], FILTER_SANITIZE_STRING);
                    break;
                case 'email' :
                    return filter_var($this->{$type}[$name], FILTER_VALIDATE_EMAIL);
                    break;
                case 'url' :
                    return filter_var($this->{$type}[$name], FILTER_VALIDATE_URL);
                    break;
                default :
                    return $this->{$type}[$name];
            }
        }
        else
        {
            return null;
        }
    }

}

?>
