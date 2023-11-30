<?php
if (!defined('controller')) define('controller', __DIR__."/mc/controller");    
require_once controller."/filmFormListController.php";
$controller = new FilmFormController();
$controller->fetchData();
$controller->readRequests();
$controller->readSessions();
?>
