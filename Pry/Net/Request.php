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
    protected $put;
    protected $delete;
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
        
        if($this->isPut())
            parse_str(file_get_contents("php://input"),$this->put);
        
        if($this->isDelete())
            parse_str(file_get_contents("php://input"),$this->delete);
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
        
        if($this->isPut())
            parse_str(file_get_contents("php://input"),$this->put);
        
        if($this->isDelete())
            parse_str(file_get_contents("php://input"),$this->delete);
    }

    /**
     * Retourne l'ensemble des entêtes de la requête
     * @return array
     */
    public function getHeaders()
    {
        if (empty($this->headers))
            $this->headers = $this->getAllHeaders();

        return $this->headers;
    }

    /**
     * Retourne un header en particulier
     * @param string $name Nom du header
     * @return string Valeur du header ou null si l'entête n'existe pas.
     */
    public function getHeader($name)
    {
        $name = strtolower($name);
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
     * @throws \InvalidArgumentException
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
     * @param array|int $flag Flag optionnel à utiliser pour le filtre
     * Types autorisés int,float,string,email,url,ip
     * @return mixed
     */
    public function getPost($name, $dataType = null,$flag = 0)
    {
        return $this->getWithFilter($name, 'post', $dataType, $flag);
    }
    
    /**
     * Récupère une valeur PUT
     * @param string $name Nom de la valeur PUT
     * @param string $dataType Type de données pour appliquer un filtres.
     * @param array|int $flag Flag optionnel à utiliser pour le filtre
     * Types autorisés int,float,string,email,url,ip
     * @return mixed
     */
    public function getPut($name, $dataType = null,$flag = 0)
    {
        return $this->getWithFilter($name, 'put', $dataType, $flag);
    }
    
    /**
     * Récupère une valeur DELETE
     * @param string $name Nom de la valeur DELETE
     * @param string $dataType Type de données pour appliquer un filtres.
     * @param array|int $flag Flag optionnel à utiliser pour le filtre
     * Types autorisés int,float,string,email,url,ip
     * @return mixed
     */
    public function getDelete($name, $dataType = null,$flag = 0)
    {
        return $this->getWithFilter($name, 'delete', $dataType, $flag);
    }

    /**
     * Récupère une valeur GET
     * @param string $name Nom de la valeur GET
     * @param string $dataType Type de données pour appliquer un filtres.
     * Types autorisés int,float,string,email,url,ip
     * @return mixed
     */
    public function get($name, $dataType = null,$flag = 0)
    {
        return $this->getWithFilter($name, 'get', $dataType, $flag);
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
     * @return Request
     * @throws \InvalidArgumentException En cas de type invalide
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
    
    public function __isset($name)
    {
        $tmp = $this->getParam($name);
        return isset($tmp);
    }
    
    /**
     * Ajoute un paramètre à la requête
     * @param mixed $params Valeur à ajouter
     * @param string [$type] Type de requête
     */
    public function add($params, $type = null)
    {
        $type = empty($type) ? $this->defaultMethod : strtolower($type);

        $this->{$type} = array_merge($this->{$type}, $params);

        if (isset($this->filters[$type]))
            $this->filters[$type]['isFiltered'] = false;
    }
    
    /**
     * Vérifie si la requête est de type post
     * @return boolean
     */
    public function isPost()
    {
        if($this->getServer('REQUEST_METHOD') == 'POST') {
            return true;
        }
        
        return false;
    }
    
    /**
     * Vérifie si la requête est de type put
     * @return boolean
     */
    public function isPut()
    {
        if($this->getServer('REQUEST_METHOD') == 'PUT') {
            return true;
        }
        
        return false;
    }
    
    /**
     * Vérifie si la requête est de type Delete
     * @return boolean
     */
    public function isDelete()
    {
        if($this->getServer('REQUEST_METHOD') == 'DELETE') {
            return true;
        }
        
        return false;
    }
    
    /**
     * Vérifie si la requête est de type get
     * @return boolean
     */
    public function isGet()
    {
        if($this->getServer('REQUEST_METHOD') == 'GET') {
            return true;
        }
        
        return false;
    }
    
    /**
     * Vérifie si la requête est de type Ajax.
     * Cet header est en général fourni par tous les FW js mais peut cependant
     * être absent dans certains cas.
     * @return boolean
     */
    public function isXmlHttpRequest()
    {
        $xhttp = $this->getServer('HTTP_X_REQUESTED_WITH');
        if(!empty($xhttp) && strtolower($xhttp) == 'xmlhttprequest') {
            return true;
        }
        
        return false;
    }
    
    /**
     * Vérifie la validité de la méthode demandé
     * @param string $method
     * @return boolean
     */
    protected function isValidMethod($method)
    {
        return in_array($method, array('get', 'post', 'request', 'cookie', 'put', 'delete'));
    }

    /**
     * Applique les filtres défini
     * @param string $type le type de requête
     * @return boolean
     * @throws \RuntimeException Si les filtres échoue
     */
    protected function applyFilters($type)
    {
        if (empty($this->filters[$type]) || empty($this->$type))
            return false;

        $this->$type = filter_var_array($this->$type, $this->filters[$type]['filter']);

        if (!$this->$type)
            throw new \RuntimeException('Filtres invalide');

        $this->filters['isFiltered'] = true;

        return true;
    }

    /**
     * Récupère un paramètre en appliquant un filtres particulier
     * @param string $name Nom du paramètre
     * @param string $type Type de paramètre
     * @param string $dataType Type de données attendu
     * @param array|int $flag Flag optionnel à utiliser
     */
    protected function getWithFilter($name, $type, $dataType,$flag = 0)
    {
        $type = empty($type) ? $this->defaultMethod : strtolower($type);
        if (isset($this->{$type}[$name]))
        {
            $param = $this->{$type}[$name];
            switch ($dataType)
            {
                case 'int' :
                    return intval($param);
                case 'float' :
                    return floatval($param);
                case 'string' :
                    $str = filter_var($param, FILTER_SANITIZE_FULL_SPECIAL_CHARS,$flag);
                    return ($str != false) ? $str : null;
                case 'email' :
                    $str =  filter_var($param, FILTER_VALIDATE_EMAIL,$flag);
                    return ($str != false) ? $str : null;
                case 'url' :
                    $str =  filter_var($param, FILTER_VALIDATE_URL,$flag);
                    return ($str != false) ? $str : null;
                case 'ip' :
                    $str =  filter_var($param, FILTER_VALIDATE_IP,$flag);
                    return ($str != false) ? $str : null;
                case 'intarray' :
                {
                    if(is_array($param))
                        return array_filter($param, function($v){return intval($v);});
                    else
                        return array();
                }
                case 'floatarray' :
                {
                    if(is_array($param))
                        return array_filter($param, function($v){return floatval($v);});
                    else
                        return array();
                }
                case 'stringarray':
                {
                    if(is_array($param))
                        return filter_var($param,FILTER_SANITIZE_FULL_SPECIAL_CHARS,FILTER_REQUIRE_ARRAY);
                    else
                        return array();
                }
                default :
                    return $param;
            }
        }
        else
        {
            return null;
        }
    }
    
    private function getAllHeaders()
    {
        if (!function_exists('getallheaders')) 
        { 
            $headers = ''; 
            foreach ($_SERVER as $name => $value) 
            { 
                if (substr($name, 0, 5) == 'HTTP_') 
                { 
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
                } 
            } 
            return $headers; 
        }
        else
        {
            return array_change_key_case(getallheaders(),CASE_LOWER);
        }
    }

}