<?php
//Inclusion minimale et indispensable
require('../Pry/Pry.php');
Pry::register();

$view = new \Pry\View\View();
$view->setViewBase('tpl/');

$view->set('hello','world');

$view->load('demo.html.php');
$view->render();