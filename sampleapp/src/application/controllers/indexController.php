<?php
namespace application\controllers;

use Pry\Util\Registry;

/**
 * Controller par défaut
 */
use Pry\Controller\BaseController;

class indexController extends BaseController
{
    public function __construct($requete, $codeLangue = 'fr') {
        parent::__construct($requete, $codeLangue);
    }

    public function index() {
        var_dump('INDEX CONTROLLER');
    }
	
	public function test() {
		var_dump('ACTION TEST');
	}
}
