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
session_start();
require('../Pry/Pry.php');
Pry::register();
if(!empty($_POST) || !empty($_GET))
{
	var_dump($_SESSION);
	var_dump($_POST);
	var_dump(Pry\Util\Token::checkToken());
	var_dump(Pry\Util\Token::getError());
}
else
{
Pry\Util\Token::genToken();
?>
<form action="example_token.php" method="post">
	<input type="text" value=" TEst" name="testtext">
	<input type="hidden" value="<?php echo Pry\Util\Token::getToken(); ?>" name="csrf_protect">
	<input type="submit" value="envoi" />
</form>
<?php
}
?>