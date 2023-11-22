<?php
include("php/SAR/database.php");
include("php/model/genre.php");
include("php/model/films.php");

    class FilmController extends AbstractController implements ITableDisplayer
    {        
        public function __construct() 
        {
            parent::__construct(new Film());
        }

        public function displayTableData() 
        {

            foreach($this->records as $record) 
            {
                /** @var Film $film */
                $film = $record;
                echo "<tr>
                    <td class=recordIndicator>&#129170;</td>
                    <td><p>{$film->title}</p></td>
                    <td><p>{$film->yearReleased}</p></td>
                    <td><p>{$film->rating}</p></td>
                    <td><p>{$film->duration}</p></td>
                    <td><p>{$film->genre->genreName}</p></td>
                    <td><button value={$film->filmID}>✎</button></td>
                    <td><button value={$film->filmID}>X</button></td>
                </tr>";
            }
        }

        public function model() : Film
        {
            /** @var Film $film */
            $film = $this->model;
            return $film;
        }
    }
?>