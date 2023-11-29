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

        public string $filterQry = "SELECT * FROM qryfilms WHERE LOWER(title) LIKE ?;";
        public string $filterGenre = "SELECT * FROM qryfilms WHERE genreID = ?;";
        public string $filterRating = "SELECT * FROM qryfilms WHERE ratingID = ?;";
        public string $filterYear = "SELECT * FROM qryfilms WHERE yearReleased = ?;";
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

        public function onSearchValueRequest()
        {
            $this->sessions->setSearchValue("%".strtolower($this->requests->searchValue())."%");
            $this->db->connect();
            $this->preparedFetchData($this->filterQry, "s", $this->sessions->getSearchValue());
            $this->recordTracker->moveTo(0);
            echo $this->displayData();
        }

        private function searchByValue() : bool 
        {
            return $this->sessions->issetSearchValue();
        }

        private function searchByGenre() : bool 
        {
            return isset($_SESSION["formListFilterType"]) 
            && $_SESSION["formListFilterType"]==2 
            && isset($_SESSION["formListFilterValue"]);
        }

        private function searchByRating() : bool 
        {
            return isset($_SESSION["formListFilterType"]) 
            && $_SESSION["formListFilterType"]==1 
            && isset($_SESSION["formListFilterValue"]);
        }

        private function searchByYear() : bool 
        {
            return isset($_SESSION["formListFilterType"]) 
            && $_SESSION["formListFilterType"]==3 
            && isset($_SESSION["formListFilterValue"]);
        }

        public function onFilter() 
        {
            switch(true)
            {
                case $this->searchByYear():
                    $this->preparedFetchData($this->filterYear, "i", intval($_SESSION["formListFilterValue"]));
                break;
                case $this->searchByRating():
                    $this->preparedFetchData($this->filterRating, "i", intval($_SESSION["formListFilterValue"]));
                break;
                case $this->searchByGenre():
                    $this->preparedFetchData($this->filterGenre, "i", intval($_SESSION["formListFilterValue"]));
                break;
                case $this->searchByValue():
                    $this->preparedFetchData($this->filterQry,"s",$this->sessions->getSearchValue());
                break;
                default:
                    $this->fetchData();
            }        
        }

        private function readFilterValue($request) 
        {
            $_SESSION["formListFilterValue"] = $request;
            $this->db->connect();            
            $this->onFilter();
            $this->recordTracker->moveTo(0);
            echo $this->displayData();
        }

        private function readFilterOption($request) 
        {
            $_SESSION["formListFilterType"] = $request;
            switch($_SESSION["formListFilterType"]) 
            {
                case 0: 
                    unset($_SESSION["formListFilterType"]);
                    unset($_SESSION["formListFilterValue"]);
                    echo "";
                break;
                case 1:
                    echo "<select id='filter'>";
                    $this->ratingController->ratingList(null);
                    echo "</select>";
                break;
                case 2:
                    echo "<select id='filter'>";
                    $this->genreController->genreList(null);
                    echo "</select>";
                break;
                case 3: echo "<input id='filter' placeholder='Select year...' type='number'>";
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
                case isset($_REQUEST["filterValue"]) && $_REQUEST["filterValue"]:
                $this->readFilterValue($_REQUEST["filterValue"]);
                break;
            }
        }
    }

    $controller = new FilmFormListController();
    $controller->onFilter();
    $controller->readRequests();
    $controller->readSessions();
?>