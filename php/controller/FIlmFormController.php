<?php
    $x = explode("php", __DIR__);
    require_once $x[0].'/php/SAR/database.php';
    require_once $x[0].'php/model/genre.php';
    require_once $x[0].'php/model/films.php';

    class FilmFormController extends AbstractFormController
    {

        public GenreController $genreController;

        public function __construct() 
        {
            parent::__construct(new Film());
            $this->genreController = new GenreController();
            $this->genreController->fetchData();
        }
        
        public function fillRecord(?array $data)
        {
            $this->model()->filmID = $this->sessions->selectedID();
            $this->model()->title = $data[0];
            $this->model()->yearReleased = $data[1];
            $this->model()->rating = $data[2];
            $this->model()->duration = $data[3];
            $this->model()->genre->genreID = $data[4];    
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

        public function save(array $data)
        {
            if ($this->model()->isNewRecord()) 
            {
                $this->db->save(
                    $this->model()->title,
                    $this->model()->yearReleased,
                    $this->model()->rating,
                    $this->model()->duration,
                    $this->model()->genre->genreID,
                );
            }
            else 
            {
                $this->db->save(
                    $this->model()->title,
                    $this->model()->yearReleased,
                    $this->model()->rating,
                    $this->model()->duration,
                    $this->model()->genre->genreID,
                    $this->model()->filmID
                );
            }
        }
    }

    class GenreController extends AbstractController 
    {
        public function __construct() 
        {
            parent::__construct(new Genre());
        }
        
        public function genreList(?Genre $genre) 
        {
            $selected=-1;
            if ($genre!=null) 
            {
                $selected = $genre->genreID;
            }

            echo '<option value="-1" selected disabled hidden>Select Genre</option>';
            foreach($this->records as $record) 
            {
                /** @var Genre $genre */
                $genre = $record;
                if ($genre->genreID==$selected) 
                    echo "<option value=". $genre->genreID ." selected>". $genre . "</option>";                
                else 
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