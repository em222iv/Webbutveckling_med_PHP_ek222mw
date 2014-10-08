<?php
//stanna på samma sida när man uppdtarerar
error_reporting(E_ALL); ini_set('display_errors','on');
ini_set('default_charset', 'UTF-8');
require_once("login.php");
$controller = new login();

//hej
$html = $controller->doControll();

$view = new loginView();
$view->echoHTML($html);









