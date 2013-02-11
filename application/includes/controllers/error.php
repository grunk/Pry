<?php
/**
 *
 * @package
 * @version
 * @author Olivier ROGER <roger.olivier@gmail.com>
 * @copyright  2007-2010 Prynel
 *
 */
 use Pry\Controller\BaseController;
 
class errorController extends BaseController
{
    public function __construct($req, $codeLangue = 'fr')
    {
        parent::__construct($req, $codeLangue);
    }
    
    /**
     * Erreur en cas d'appel à un module inconnu
     */
    public function index()
    {
		$this->view->set('text','Le module que vous demandez n\'existe pas');
        $this->view->load('error/index.html');
		$this->view->render();
    }

}
?>
