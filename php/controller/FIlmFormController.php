<?php
    $x = explode("php", __DIR__);
    require_once $x[0].'/php/SAR/database.php';
    require_once $x[0].'/php/model/genre.php';
    require_once $x[0].'/php/model/films.php';

    class FilmFormController extends AbstractFormController
    {

        public GenreController $genreController;
        public RatingController $ratingController;

        public function __construct() 
        {
            parent::__construct(new Film());
            $this->genreController = new GenreController();
            $this->ratingController = new RatingController();
            $this->genreController->fetchData();
            $this->ratingController->fetchData();
        }

        public function save(array $data)
        {
            if ($this->model()->isNewRecord()) 
            {
                $this->db->save(
                    $this->model()->title,
                    $this->model()->yearReleased,
                    $this->model()->rating->ratingID,
                    $this->model()->duration,
                    $this->model()->genre->genreID,
                );
            }
            else 
            {
                $this->db->save(
                    $this->model()->title,
                    $this->model()->yearReleased,
                    $this->model()->rating->ratingID,
                    $this->model()->duration,
                    $this->model()->genre->genreID,
                    $this->model()->filmID
                );
            }
        }

        public function fillRecord(?array $data)
        {
            $this->model()->filmID = $this->sessions->selectedID();
            $this->model()->title = $data[0];
            $this->model()->yearReleased = $data[1];
            $this->model()->rating->ratingID = $data[2];
            $this->model()->duration = $data[3];
            $this->model()->genre->genreID = $data[4];    
        }

        public function onSearchValueRequest() {}

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
                $selected = $genre->pkgenreID;
            }

            echo '<option value="-1" selected disabled hidden>Select Genre</option>';
            foreach($this->records as $record) 
            {
                /** @var Genre $genre */
                $genre = $record;
                if ($genre->pkgenreID==$selected) 
                    echo "<option value=". $genre->pkgenreID ." selected>". $genre . "</option>";                
                else 
                    echo "<option value=". $genre->pkgenreID .">". $genre . "</option>";
            }
        }

        public function onSearchValueRequest(){}
        public function onDeleteRecordRequest(){}
        public function onSaveRecordRequest(){}
    }

    class RatingController extends AbstractController 
    {
        public function __construct() 
        {
            parent::__construct(new Rating());
        }
        
        public function ratingList(?Rating $rating) 
        {
            $selected=-1;
            if ($rating!=null) 
            {
                $selected = $rating->pkratingID;
            }

            echo '<option value="-1" selected disabled hidden>Select Rating</option>';
            foreach($this->records as $record) 
            {
                /** @var Rating $rating */
                $rating = $record;
                if ($rating->pkratingID==$selected) 
                    echo "<option value=". $rating->pkratingID ." selected>". $rating . "</option>";                
                else 
                    echo "<option value=". $rating->pkratingID .">". $rating . "</option>";
            }
        }

        public function onSearchValueRequest(){}
        public function onDeleteRecordRequest(){}
        public function onSaveRecordRequest(){}
    }

    $controller = new FilmFormController();
    $controller->fetchData();
    $controller->readRequests();
    $controller->readSessions();

?>