<?php
if (!defined('SAR')) define('SAR', dirname(__DIR__,2)."/SAR");
if (!defined('model')) define('model', dirname(__DIR__)."/model");
if (!defined('controller')) define('controller', __DIR__);

require_once model."/films.php";
require_once SAR."/abstractController.php";
require_once controller."/filmFormController.php";

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

        public function displayData()
        {
            echo "<caption>Record</caption>
            <tr>
                <td>
                    <label>Title</label>
                </td>
                <td>
                    <input class='recordField' type='text' value='".$this->model()->_title."'>
                </td>
            </tr>
            <tr>
                <td>
                    <label>Year</label>
                </td>
                <td>
                    <input id='yearReleased' class='recordField' type='number' value='".$this->model()->_yearReleased ."'>
                </td>
            </tr>
            <tr>
                <td>
                    <label>Rating</label>
                </td>
                <td>
                    <select class='recordField' value='".$this->model()->fkrating->pkratingID."'>";
                            echo $this->ratingController->ratingList($this->model()->fkrating);
                    echo "</select>
                </td>
            </tr>
            <tr>
                <td>
                    <label>Duration</label>
                </td>
                <td>
                    <input class='recordField' type='number' value='".$this->model()->_duration."'>
                </td>
            </tr>
            <tr>
                <td>
                    <label>Genre</label>
                </td>
                <td>
                    <select class='recordField' value='".$this->model()->fkgenre->pkgenreID."'>";
                        echo $this->genreController->genreList($this->model()->fkgenre);
                    echo "</select>
                </td>
            </tr>";
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

        public function displayData()
        {
            
        }
    }
?>