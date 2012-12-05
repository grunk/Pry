<?php
/**
 *
 * @package
 * @version
 * @author Olivier ROGER <roger.olivier@gmail.com>
 * @copyright  2007-2010 Prynel
 *
 */
class errorController extends Controller_BaseController
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
		$this->view['text'] = 'Le module que vous demandez n\'existe pas';
        $template           = $this->template->loadTemplate('error/index.html');
		echo $template->render($this->view);
    }

}
?>
