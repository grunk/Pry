<form action="example_request.php?page=test" method="post">
	<input type="text" name="test" />
	<input type="text" name="email" />
	<select name="multi[]" multiple="multiple">
	<option value="1">o1</option>
	<option value="2">o2</option>
	<option value="3">o3</option>
	</select>
	<input type="submit" value="send" />
</form>
<?php
/**
 *
 * @package Demo
 * @version 1.0.0 
 * @author Olivier ROGER <roger.olivier@gmail.com>
 * @copyright  2007-2010 Prynel
 *       
 *
 */
//Inclusion minimale et indispensable
define("INC_CORE_PATH", realpath(dirname(__FILE__).'/../PryNS/Pry'));
set_include_path(INC_CORE_PATH . PATH_SEPARATOR . get_include_path());

require('../Pry/Pry.php');
use Pry\Net\Request;
Pry::register();

$r = new Request();

$r->setFilter(array(
	'test' => FILTER_SANITIZE_NUMBER_INT,
	'email'=> FILTER_VALIDATE_EMAIL
),'post');

$r->setDefaultMethod('request');
//var_dump($r);

//var_dump($r->getParam('test','get'),$r->getParams('get'),$r->getHeaders());

//var_dump($r->getParam('test'),$r->getParam('email'));
var_dump($r->getParams('get'));
$r->add(array('id' =>1, 'pager'=>45),'get');
var_dump($r->getParams('get'));

var_dump($r->get('pager','int'));
var_dump($r->get('pager','email'));
var_dump($r->getPost('pager','url'));



?>