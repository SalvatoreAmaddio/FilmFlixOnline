<?php
if (!defined('controller')) define('controller', $_SERVER['DOCUMENT_ROOT']."/filmflix/mc/controller");    
require_once controller."//filmFormListController.php";

$controller = new FilmFormListController();
$controller->onFilter();
$controller->readRequests();
$controller->readSessions();
?>
