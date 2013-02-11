<?php

/**
 *
 * @package
 * @version
 * @author Olivier ROGER <roger.olivier@gmail.com>
 * @copyright  2007-2011 Prynel
 *
 */
use Pry\Controller\BaseController;

class indexController extends BaseController
{

    private $auth;

    public function __construct($requete, $codeLangue = 'fr')
    {
        parent::__construct($requete, $codeLangue);
    }

    public function index()
    {
	echo 'Action = ' . $this->request->action . '<br />';
        echo 'Controller = ' . $this->request->controller . '<br />';

        echo 'param ID = ' . $this->request->id . '<br />';
        echo 'param form = ' . $this->request->getParam('input', 'post');

        //Gestion de la vue avec View_View

        $this->view->controller = $this->request->controller;
        $this->view->set('action', $this->request->action);

        echo 'param ID = ' . $this->request->id . '<br />';
        echo 'param form = ' . $this->request->getParam('input', 'post');
        $this->view->load('index/index.html');
        $this->view->render();
    }

}

?>
