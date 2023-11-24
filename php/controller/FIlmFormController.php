<?php
    $x = explode("php", __DIR__);
    require_once $x[0].'/php/SAR/database.php';
    require_once $x[0].'php/model/genre.php';
    require_once $x[0].'php/model/films.php';

    class FilmFormController extends  AbstractFormController
    {

        public GenreController $genreController;

        public function __construct() 
        {
            parent::__construct(new Film());
            $this->genreController = new GenreController();
            $this->genreController->fetchData();
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

        public function displayData() 
        {
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

    $controller = new FilmFormController();
    $controller->fetchData();
    $controller->readRequests();
    $controller->readSessions();

?>