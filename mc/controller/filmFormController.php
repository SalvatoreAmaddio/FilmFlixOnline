<?php
    if (!defined('SAR')) define('SAR', $_SERVER['DOCUMENT_ROOT']."\SAR");
    if (!defined('model')) define('model', $_SERVER['DOCUMENT_ROOT']."\mc\model");
    require_once model."\\films.php";
    require_once SAR."\abstractController.php";

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
                    $this->model()->_title,
                    $this->model()->_yearReleased,
                    $this->model()->_duration,
                    $this->model()->fkrating->pkratingID,
                    $this->model()->fkgenre->pkgenreID,
                );

                echo "inserted";
            }
            else 
            {
                $this->db->save(
                    $this->model()->_title,
                    $this->model()->_yearReleased,
                    $this->model()->_duration,
                    $this->model()->fkrating->pkratingID,
                    $this->model()->fkgenre->pkgenreID,
                    $this->model()->pkfilmID
                );

                echo "updated";
            }
        }

        public function fillRecord(?array $data)
        {
            $this->model()->pkfilmID = $this->sessions->selectedID();
            $this->model()->_title = $data[0];
            $this->model()->_yearReleased = $data[1];
            $this->model()->fkrating->pkratingID = $data[2];
            $this->model()->_duration = $data[3];
            $this->model()->fkgenre->pkgenreID = $data[4];    
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

        public function onDeleteRecordRequest(){}
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

        public function onDeleteRecordRequest(){}
    }
?>