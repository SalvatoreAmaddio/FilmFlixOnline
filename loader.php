<?php
define("home", $_SERVER['DOCUMENT_ROOT']);
define('mc', $_SERVER['DOCUMENT_ROOT']."\mc");
define('js', $_SERVER['DOCUMENT_ROOT']."\js");
define('SAR', $_SERVER['DOCUMENT_ROOT']."\SAR");
define('model', mc."\model");
define('controller', mc."\controller");

require_once controller."\\filmFormListController.php";

$f = new Film();
echo $f->isNewRecord();

$f = new FilmFormListController();

$d = new Database();

echo $_SERVER['DOCUMENT_ROOT'];
?>