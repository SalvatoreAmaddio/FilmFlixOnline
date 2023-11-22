<?php
include("php/SAR/database.php");
include("php/model/genre.php");
include("php/model/films.php");

    class FilmController extends AbstractController
    {        
        public function __construct() 
        {
            parent::__construct(new Film());
        }

        public function displayTableData() 
        {
            echo "<tr>
            <th colspan='2'>Title</th>
            <th>Year</th>
            <th>Rating</th>
            <th>Duration</th>
            <th>Genre</th>
            <th colspan='2'>COMMANDS</th>
            </tr>";

            foreach($this->records as $record) 
            {
                /** @var Film $film */
                $film = $record;
                echo "<tr {$this->selectedRow($record)} value='{$film->filmID}'>
                        <td class=recordIndicator>➤</td>
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

        public function displayFormData()
        {

        }
        
        public function read() : bool
        {
            return parent::read();
        }

        public function findIDCriteria($record,$id) : bool
        {
              /** @var Film $film */
              $film = $record;
              return $film->filmID == $id;
        }

        public function model() : Film
        {
            /** @var Film $film */
            $film = $this->model;
            return $film;
        }
    }
?>