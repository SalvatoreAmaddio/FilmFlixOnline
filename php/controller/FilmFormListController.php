<?php
    $x = explode("php", __DIR__);
    require_once $x[0].'/php/SAR/database.php';
    require_once $x[0].'php/model/genre.php';
    require_once $x[0].'php/model/films.php';

    class FilmFormListController extends AbstractFormListController
    {        

        public function __construct() 
        {
            parent::__construct(new Film());
        }

        public function displayData() 
        {
            if ($this->recordCount()==0) 
            {
                echo "<tr>
                <th colspan='6'>NO DATA</th>
                </tr>";    
                return;
            }

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
                        <td><button class=editButton value={$film->filmID}>✎</button></td>
                        <td><button class=deleteButton value={$film->filmID}>X</button></td>
                    </tr>";
            }
        }

        public function findIDCriteria($record,$id) : bool
        {
              /** @var Film $film */
              $film = $record;
              return $film->filmID == $id;
        }

        public function findRecordCriteria($record,$value) : bool
        {
              /** @var Film $obj */
              $obj = $record;
              return str_contains(strtolower(trim($obj->title)), strtolower(trim($value)));
        }

        public function model() : Film
        {
            /** @var Film $film */
            $film = $this->model;
            return $film;
        }
    }

    class GenreController extends AbstractController 
    {
        public function __construct() 
        {
            parent::__construct(new Genre());
        }

        public function displayData()
        {

        }
        
        public function genreList() 
        {
            foreach($this->records as $record) 
            {
                /** @var Genre $genre */
                $genre = $record;
                echo "<option value=". $genre->genreID .">". $genre . "</option>";
            }
        }

        public function findRecordCriteria($record, $value) : bool
        {
              /** @var Genre $obj */
              $obj = $record;
              return $obj->genreID == $value;
        }

        public function findIDCriteria($record,$id) : bool
        {
              /** @var Genre $genre */
              $genre = $record;
              return $genre->genreID == $id;
        }

        public function model() : Genre
        {
            /** @var Genre $genre */
            $genre = $this->model;
            return $genre;
        }


    }


    $controller = new FilmFormListController();
    $controller->fetchData();
    $controller->readRequests();
    $controller->readSessions();
?>