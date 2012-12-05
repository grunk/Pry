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

namespace Pry\Controller;

use Pry\Net\Request;

/**
 * Router permettant la mise en place du pattern MVC
 * Gère les routes classiques ainsi que les règles de routages
 * <code>
 * $router = Router::getInstance();
 * $router->setPath(ROOT_PATH.'includes/controllers/'); // Chemin vers les controlleurs
 * $router->addRule('test/regles/:id/hello',array('controller'=>'index','action'=>'withRule'));
 * </code>
 * Nécessite une règle de routage du type RewriteRule ^(.*)$ index.php?url=$1 [QSA,L] dans le serveur web
 * 
 * @category Pry
 * @package Controller
 * @version 1.3.5
 * @author Olivier ROGER <oroger.fr>
 *
 */
class Router
{

    /**
     * Instance du router
     * @static
     * @var Controller_Router
     */
    static private $instance;

    /**
     * Controller à utiliser. Par defaut index
     * @var string
     */
    private $controller;

    /**
     * Action du controller. Par défaut index
     * @var string
     */
    private $action;

    /**
     * Tableau des paramètres
     * @var array
     */
    private $params;

    /**
     * Liste des règles de routage
     * @var array
     */
    private $rules;

    /**
     * Chemin vers le dossier contenant les controllers
     * @var string
     */
    private $path;

    /**
     * Objet de vue
     * @var mixed 
     */
    private $view;

    /**
     * Fichier à inclure
     * @var string
     */
    private $file;

    /**
     * Controller par defaut (index)
     * @var string
     */
    private $defaultController;

    /**
     * Action par defaut (index)
     * @var string
     */
    private $defaultAction;

    /**
     * Controller à appelé en cas d'erreur. Par defaut error
     * @var string
     */
    private $errorController;

    /**
     * Action à appelé en cas d'erreur. par defaut index
     * @var string
     */
    private $errorAction;

    /**
     * Le router gère t'il les url du type site.com/fr/controller/action
     * @var boolean 
     */
    private $isMultiLangue = false;

    /**
     * Code langue choisie
     * @var string 
     */
    private $codeLangue = '';

    /**
     * Liste des traduction d'url
     * @var array 
     */
    private $tradController;

    /**
     * Requete HTTP
     * @var Net_Request
     */
    private $request;

    /**
     * Singleton de la classe
     * @return Controller_Router
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
            self::$instance = new Router();
        return self::$instance;
    }

    /**
     * Charge le controller demandé.
     * Prend en compte les règles de routages si nécessaire
     */
    public function load()
    {
        $this->request = new Request();
        $tmp           = $this->request->url;

        $url      = !empty($tmp) ? $tmp : '';
        $tabUrl   = explode('/', $url);
        $isCustom = false;

        //Suppression des éventuelles partie vide de l'url
        $this->clear_empty_value($tabUrl);

        if (!empty($this->rules))
        {
            foreach ($this->rules as $key => $data) {
                $params = $this->matchRules($key, $tabUrl);
                if ($params)
                {
                    $this->controller = $data['controller'];
                    $this->action     = $data['action'];
                    $this->params     = $params;
                    $isCustom         = true;
                    break;
                }
            }
        }

        if (!$isCustom)
            $this->getRoute($tabUrl);

        $this->controller = (!empty($this->controller)) ? $this->controller : $this->defaultController;
        $this->action     = (!empty($this->action)) ? $this->action : $this->defaultAction;
        $ctrlPath         = str_replace('_', '/', $this->controller); // Gestion des sous dossiers dans les controllers
        $this->file       = realpath($this->path) . DIRECTORY_SEPARATOR . $ctrlPath . '.php';

        //Enrichissement de la requête
        $this->params['action']     = $this->action;
        $this->params['controller'] = $this->controller;
        $this->request->add($this->params);

        //is_file bien plus rapide que file_exists
        if (!is_file($this->file))
        {
            header("Status: 404 Not Found");
            $this->controller = $this->errorController;
            $this->action     = $this->errorAction;
            $this->file       = $this->path . $this->controller . '.php';
        }

        //Inclusion du controller
        include $this->file;

        $class = $this->controller . 'Controller';

        if (!empty($this->codeLangue))
        {
            $controller = new $class($this->request, $this->codeLangue);
        }
        else
        {
            $controller = new $class($this->request);
        }

        if (!empty($this->view))
            $controller->setView($this->view);

        if (!is_callable(array($controller, $this->action)))
        {
            $action = $this->defaultAction;
        }
        else
        {
            $action = $this->action;
        }

        $controller->$action();
    }

    /**
     * Ajoute une règle de routage.
     *
     * @param string $rule Règles de routage : /bla/:param1/blabla/:param2/blabla
     * @param array $target Cible de la règle : array('controller'=>'index','action'=>'test')
     */
    public function addRule($rule, $target)
    {
        if ($rule[0] != '/')
            $rule = '/' . $rule; //Ajout du slashe de début si absent

        $this->rules[$rule] = $target;
    }

    /**
     * Vérifie si l'url correspond à une règle de routage
     * @link http://blog.sosedoff.com/2009/07/04/simpe-php-url-routing-controller/
     * @param string $rule
     * @param array $dataItems
     * @return boolean|array
     */
    public function matchRules($rule, $dataItems)
    {
        $ruleItems = explode('/', $rule);
        $this->clear_empty_value($ruleItems);

        if (count($ruleItems) == count($dataItems))
        {
            $result = array();
            foreach ($ruleItems as $rKey => $rValue) {
                if ($rValue[0] == ':')
                {
                    $rValue          = substr($rValue, 1); //Supprime les : de la clé
                    $result[$rValue] = $dataItems[$rKey];
                }
                else
                {
                    if ($rValue != $dataItems[$rKey])
                        return false;
                }
            }
            if (!empty($result))
                return $result;

            unset($result);
        }
        return false;
    }

    /**
     * Retourne l'action
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Retourne le controller
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Défini une route simple
     * @param array $url
     */
    private function getRoute($url)
    {
        $items = $url;

        if (!empty($items))
        {
            if ($this->isMultiLangue)
                $this->codeLangue = array_shift($items);

            $this->controller = array_shift($items);
            $this->action     = array_shift($items);
            $size             = count($items);
            if ($size >= 2)
                for ($i = 0; $i < $size; $i += 2) {
                    $key                = (isset($items[$i])) ? $items[$i] : $i;
                    $value              = (isset($items[$i + 1])) ? $items[$i + 1] : null;
                    $this->params[$key] = $value;
                }
            else
                $this->params       = $items;



            //Permet d'avoir des URL multilangue
            if (!empty($this->tradController))
            {
                if (isset($this->tradController[$this->codeLangue][$this->controller]['controllerName']))
                {
                    $controller       = $this->tradController[$this->codeLangue][$this->controller]['controllerName'];
                    if (!empty($controller))
                        $this->controller = $controller;
                }

                if (isset($this->tradController[$this->codeLangue][$this->controller]['actionsNames'][$this->action]))
                {
                    $action       = $this->tradController[$this->codeLangue][$this->controller]['actionsNames'][$this->action];
                    if (!empty($action))
                        $this->action = $action;
                }
            }
        }
    }

    /**
     * Défini le chemin des controllers
     * @param string $path
     */
    public function setPath($path)
    {
        if (is_dir($path) === false)
        {
            throw new \InvalidArgumentException('Controller invalide : ' . $path);
        }

        $this->path = $path;
    }

    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * Défini le router comme pouvant gérer ou non le multinlangue
     * @param boolean $is 
     */
    public function setMultiLangue($is)
    {
        $this->isMultiLangue = $is;
    }

    /**
     * Défini un tableau permettant d'avoir des URL multi langue.
     * Format du tableau : 
     * 
     * @param array $trad format : 
     * $urlTraduction = array(
      'fr'=>array(
      'accueil'=>array(
      'controllerName'	=> 'index',
      'actionsNames'		=> array(
      'presentation'	=> 'index',
      'liste'			=> 'list',
      'recherche'		=> 'search'
      )
      )
      ),
      'en'=>array(...));
     */
    public function setControllerTraduction($trad)
    {
        $this->tradController = $trad;
    }

    /**
     * Défini le controller et l'action par défaut
     * @param string $controller
     * @param string $action
     */
    public function setDefaultControllerAction($controller, $action)
    {
        $this->defaultController = $controller;
        $this->defaultAction     = $action;
    }

    /**
     * Défini le controller et l'actionen cas d'erreur
     * @param string $controler
     * @param string $action
     */
    public function setErrorControllerAction($controller, $action)
    {
        $this->errorController = $controller;
        $this->errorAction     = $action;
    }

    /**
     * Renvoi les paramètres disponibles
     * @return array
     */
    public function getParameters()
    {
        return $this->params;
    }

    /**
     * Constructeur
     */
    private function __construct()
    {
        $this->rules = array();
        $this->defaultController = 'index';
        $this->defaultAction     = 'index';
        $this->errorController   = 'error';
        $this->errorAction       = 'index';
    }

    /**
     * Supprime d'un tableau tous les élements vide
     * @param array $array
     */
    private function clear_empty_value(&$array)
    {
        foreach ($array as $key => $value) {
            if (empty($value) && $value != 0)
                unset($array[$key]);
        }
        $array = array_values($array); // Réorganise les clés
    }

}
