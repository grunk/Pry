<?php
/**
 *
 * @package
 * @version
 * @author Olivier ROGER <roger.olivier@gmail.com>
 * @copyright  2007-2010 Prynel
 *
 */
use Pry\File\Crypt;
use Pry\File\FileManager;

require('../Pry/Pry.php');
Pry::register();

$key = 'lOredvksghvpaj45Skfo8dk4';
$fileclear = 'test/test.txt';
$filecrypt = 'test/test_crypt.txt';
$filedecrypt = 'test/test_decrypt.txt';

$crypter = new Crypt($key);
$crypter->crypt(new FileManager($fileclear), new FileManager($filecrypt),false);
$crypter->decrypt(new FileManager($filecrypt), new FileManager($filedecrypt),false);

echo 'En clair : <br />';
var_dump(file_get_contents('test/test.txt'));
echo 'Crypté : <br />';
var_dump(file_get_contents('test/test_crypt.txt'));
echo 'DéCrypté : <br />';
var_dump(file_get_contents('test/test_decrypt.txt'));
?>
