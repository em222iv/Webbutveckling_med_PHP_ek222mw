<?php
require_once("login.php");
$controller = new login();

//hej
$html = $controller->doControll();

$view = new loginView();
$view->echoHTML($html);









