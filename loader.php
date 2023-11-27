<?php

define("home",getcwd());
define('php', home."\php");
    define('SAR', php."\SAR");
    define('model', php."\model");
    define('controller', php."\controller");
    define('js', php."\js");

require_once model."\\films.php";
require_once controller."\\filmFormListController.php";

$f = new Film();
echo $f->isNewRecord();

$f = new FilmFormListController();

$d = new Database();
?>