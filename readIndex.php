<?php
if (!defined('controller')) define('controller', getcwd()."/mc/controller");    
require_once controller."//filmFormListController.php";

$controller = new FilmFormListController();
$controller->onFilter();
$controller->readRequests();
$controller->readSessions();
?>
