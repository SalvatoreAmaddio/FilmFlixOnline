<?php
include_once 'php/SAR/Ref.php';
include_once 'php/SAR/abstractModel.php';
include_once 'php/SAR/abstractController.php';
include_once 'php/model/genre.php';
include_once 'php/model/films.php';

class Ciao 
{
    private string $property = 'c';
    private string $property2 = 'c';

    public function ciao() : string 
    {
        return "ciao";
    }
}

$film = new Film();
$ref = new Ref($film);
echo $film->select();
echo "<br>";
echo $film->deleteSQL();
echo "<br>";
echo $film->insertSQL();
echo "<br>";
echo $film->updateSQL();

?>
