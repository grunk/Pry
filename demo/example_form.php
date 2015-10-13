<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="http://localhost/class/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="http://localhost/class/js/jquery-ui-1.8.6.custom.min.js"></script>
<script type="text/javascript" src="http://localhost/class/js/colorpicker/jquery.gccolor.js"></script>
<script type="text/javascript" src="http://localhost/class/js/tooltip/jquery.tipTip.min.js"></script>
<script type="text/javascript" src="http://localhost/class/js/spinbox/jquery.spinbox.js"></script>
<script type="text/javascript" src="http://localhost/class/js/multiselect/jquery.multiselect.min.js"></script>

<link rel="stylesheet" type="text/css" href="http://localhost/class/js/colorpicker/gccolor.css" media="screen"/>
<link rel="stylesheet" type="text/css" href="http://localhost/class/js/tooltip/tipTip.css" media="screen"/>
<link rel="stylesheet" type="text/css" href="http://localhost/class/js/redmond/jquery-ui-1.8.6.custom.css" media="screen"/>
<link rel="stylesheet" type="text/css" href="http://localhost/class/js/spinbox/spinbox.css" media="screen"/>
<link rel="stylesheet" type="text/css" href="http://localhost/class/js/multiselect/jquery.multiselect.css" media="screen"/>

<style type="text/css">
.errorForm{color:#fff;font-style: italic;}
.slider_frame{width:200px;}
select{width:200px;}
input.ui-state-default{font-size:12px;}
</style>
</head>
<body>
<?php
/**
 *
 * @package Demo
 * @version 1.0.0 
 * @author Olivier ROGER <roger.olivier@gmail.com>
 * @copyright  2007-2008 Prynel
 *       
 *
 */
use Pry\Form\Form;

require('../Pry/Pry.php');
Pry::register();

$form = new Form('monForm');

//Couleur
$form->add('Colorpicker','couleur')
		->id('couleur')
		->label('Couleur :')
		->setRGBColor(array(0,0,0));	

$form->add('Text','nom')
      ->label('Test Tooltip')
      ->id('nom')
	  ->value('Mon nom')
	  ->info('Ceci est un tooltip')
	  ->setErrorClass('errorForm');
	  
$form->add('DatePicker','datepicker2')
		->label('Date + time :')
		->value(date("d/m/Y"))
		->id('datepicker')
		->icon('clock.png')
		->setTimepicker(true)
		->format('fr')
		->setErrorClass('errorForm');
		
$form->add('DatePicker','datepicker3')
		->label('Date :')
		->value(date("d/m/Y"))
		->id('datepicker2')
		->icon('clock.png')
		->format('us')
		->setErrorClass('errorForm');

//IP
$form->add('Ip','addIP')
	->label('IP :')
	->id('ip')
	->value('172.161.121.182')
	->required(false)
	->setErrorClass('errorForm');
	

$form->add('Select','multi')
		->id('multi')->multiple(true)->enableJQPlugin(true)->setJQSelectedList(2)
		->choices(array('test11'=>'test1','test2'=>'test2','test3'=>'test3','test4'=>'test4','test5'=>'test5','test6'=>'test6'));	


// INput avec 2 fleches pour augmenter / diminuer une valeur numérique
$form->add('NumericStepper','numerci')
		->label('Numeric stepper')
		->setMax(50)
		->setMin(20)
		->id('monstepper');

// Slider JqueryUI		
$form->add('Slider','myslide')
		->id('slider1')
        ->label('slider')
		->value(10)
		->step(5)
        ->displayValue(true)
		->setRange(20,120);
		
$form->add('Submit','Envoi')->value('Envoyer');

		//var_dump($form);
if($form->isValid($_POST))
{
	echo 'OK';
}
else
	echo $form; // Affichage form (avec erreur si deja soumis)

 ?>
</body>
</html>