<?php
include_once 'php/SAR/Ref.php';
include_once 'php/SAR/abstractModel.php';
include_once 'php/SAR/abstractController.php';
include_once 'php/model/genre.php';
include_once 'php/model/films.php';
include_once 'php/controller/FilmFormListController.php';

$controller = new FilmFormListController();
$controller->fetchData();
echo "<table>";
$controller->displayData();
echo "</table>";
?>
