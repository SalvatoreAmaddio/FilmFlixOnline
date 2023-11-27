<?php
define("home",getcwd());
define('mc', home."\mc");
define('js', home."\js");
define('SAR', home."\SAR");
define('model', mc."\model");
define('controller', mc."\controller");

require_once controller."\\filmFormListController.php";

$f = new Film();
echo $f->isNewRecord();

$f = new FilmFormListController();

$d = new Database();

echo $_SERVER['DOCUMENT_ROOT'];
?>