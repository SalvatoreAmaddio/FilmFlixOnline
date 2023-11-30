<?php
if (!defined('controller')) define('controller', getcwd()."/mc/controller");    
require_once controller."/filmFormListController.php";
$controller = new FilmFormController();
$controller->fetchData();
$controller->readRequests();
$controller->readSessions();
?>
