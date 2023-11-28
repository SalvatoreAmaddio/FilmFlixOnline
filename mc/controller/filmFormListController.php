<?php
    if(session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!defined('SAR')) define('SAR', $_SERVER['DOCUMENT_ROOT']."\SAR");
    if (!defined('model')) define('model', $_SERVER['DOCUMENT_ROOT']."\mc\model");
    if (!defined('controller')) define('controller', $_SERVER['DOCUMENT_ROOT']."\mc\controller");

    require_once model."\\films.php";
    require_once SAR."\\abstractController.php";
    require_once controller."\\filmFormController.php";

    class FilmFormListController extends AbstractFormListController
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

        public function displayData() 
        {
            if ($this->recordCount()==0) 
            {
                echo "<table style='background-color:transparent'><tr>
                <div id='noData'>
                    <p>NO DATA</p>
                    <img src='img/idk.png'>
                </div>
                </tr></table>";    
                return;
            }

            echo "<table style='background-color: white;'>
            <tr>
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
                echo "<tr {$this->selectedRow($record)} value='{$film->pkfilmID}'>
                        <td class=recordIndicator>➤</td>
                        <td class='responsiveTitle'><p>Title</p></td>
                        <td><p>{$film->_title}</p></td>
                        <td class='responsiveTitle'><p>Year</p></td>
                        <td><p>{$film->_yearReleased}</p></td>
                        <td class='responsiveTitle'><p>Rating</p></td>
                        <td><p>{$film->fkrating}</p></td>
                        <td class='responsiveTitle'><p>Duration</p></td>
                        <td><p>{$film->_duration}</p></td>
                        <td class='responsiveTitle'><p>Genre</p></td>
                        <td><p>{$film->fkgenre->_genreName}</p></td>
                        <td class='responsiveTitle'><p style='padding: 0rem'></p></td>
                        <td class='commands'><button class=editButton value={$film->pkfilmID}>✎</button></td>
                        <td class='commands'><button class=deleteButton value={$film->pkfilmID}>X</button></td>
                    </tr>";
            }
            echo "</table>";
        }

        private function readFilterOption($request) 
        {
            switch($request) 
            {
                case 0:
                    echo "";
                break;
                case 1:
                    echo "<select>";
                    $this->ratingController->ratingList(null);
                    echo "</select>";
                break;
                case 2:
                    echo "<select>";
                    $this->genreController->genreList(null);
                    echo "</select>";
                break;
                case 3:
                    echo "<input placeholder='Select year...' type='number'>";
                break;
            }
        }

        public function readRequests()
        {
            parent::readRequests();
            switch(true) 
            {
                case isset($_REQUEST["filterOption"]) && $_REQUEST["filterOption"]:
                $this->readFilterOption($_REQUEST["filterOption"]);
                break;
            }
        }
    }

    $controller = new FilmFormListController();
    $controller->fetchData();
    $controller->readRequests();
    $controller->readSessions();

?>