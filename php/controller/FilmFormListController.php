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
                echo "<table style='background-color:transparent'><tr>
                <th colspan='6' style='padding: 0rem; border-bottom: none;'>
                <div id='noData'>
                    <p>NO DATA</p>
                    <img src='img/idk.png'>
                </div>
                </th>
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
                echo "<tr {$this->selectedRow($record)} value='{$film->filmID}'>
                        <td class=recordIndicator>➤</td>
                        <td class='responsiveTitle'><p>Title</p></td>
                        <td><p>{$film->title}</p></td>
                        <td class='responsiveTitle'><p>Year</p></td>
                        <td><p>{$film->yearReleased}</p></td>
                        <td class='responsiveTitle'><p>Rating</p></td>
                        <td><p>{$film->rating}</p></td>
                        <td class='responsiveTitle'><p>Duration</p></td>
                        <td><p>{$film->duration}</p></td>
                        <td class='responsiveTitle'><p>Genre</p></td>
                        <td><p>{$film->genre->genreName}</p></td>
                        <td class='responsiveTitle'><p style='padding: 0rem'></p></td>
                        <td class='commands'><button class=editButton value={$film->filmID}>✎</button></td>
                        <td class='commands'><button class=deleteButton value={$film->filmID}>X</button></td>
                    </tr>";
            }
            echo "</table>";
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

    $controller = new FilmFormListController();
    $controller->fetchData();
    $controller->readRequests();
    $controller->readSessions();
?>