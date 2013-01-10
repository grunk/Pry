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

use Pry\Util\Registry;

/**
 * Controller de base fournissant les info essentielles aux autres controlleurs
 * @category Pry
 * @package Controller
 * @version 1.2.0
 * @author Olivier ROGER <oroger.fr>
 */
abstract class BaseController
{

    /**
     * Objet de vue
     * @var mixed
     */
    protected $view;

    /**
     * Requête utilisé pour atteindre le controller
     * @var Pry\Net\Request
     */
    protected $request;

    /**
     * Objet base de données
     * @var Zend_Db_Adapter_Abstract 
     */
    protected $db;

    /**
     * Langue détectée dans l'URL. Code sur deux lettre
     * @var string 
     */
    protected $codeLangue;

    /**
     * Instanciation du controller
     * @param Net_Request $requete Requête utilisé
     * @param string $codeLangue Code langue. par défaut défini à fr
     */
    public function __construct($requete, $codeLangue = 'fr')
    {
        if (Registry::isRegistered('Db'))
            $this->db = Registry::get('Db');

        $this->request    = $requete;
        $this->codeLangue = $codeLangue;
    }

    /**
     * Redirection
     * @param string $url 
     */
    public function redirect($url)
    {
        header('Location: /' . $url);
        exit;
    }

    public function setView($view)
    {
        $this->view = $view;

        if ($this->view instanceof Pry\View\View)
            $this->view->controller = $this->request->controller;
    }

    abstract public function index();
}
