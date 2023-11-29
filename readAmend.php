<?php
if (!defined('controller')) define('controller', $_SERVER['DOCUMENT_ROOT']."\mc\controller");    
require_once controller."\\filmFormListController.php";
$controller = new FilmFormController();
$controller->fetchData();
$controller->readRequests();
$controller->readSessions();
?>
