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
use Pry\File\Upload;
require('../Pry/Pry.php');
Pry::register();

if(isset($_POST['MAX_FILE_SIZE']))
{
	try{
	$upload = new Upload('test/','files');
	//Ajout d'extension
	$upload->setAllowedExtensions(array('perso','perso2'));
	//Ajotu de type mime
	$upload->setAllowedMime('test/prynel');
	$upload->upload();
	}
	catch(Util_ExceptionHandler $e){
		echo $e->getError();
	}
}
else
{
echo substr('test.php', 0, strrpos('test.php', '.'))
?>
<form enctype="multipart/form-data" method="post" action="example_upload.php">
	<input type="file" name="files[]" />
	<input type="hidden" name="MAX_FILE_SIZE" value="128000">
	<input type="submit" value="envoyer" />
</form>

<?php
}
?>