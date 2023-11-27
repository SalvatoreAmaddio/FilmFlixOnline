<?php
    $x = explode("php", __DIR__);
    require_once $x[0].'/php/SAR/database.php';
    require_once $x[0].'/php/model/genre.php';
    require_once $x[0].'/php/model/films.php';

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

        public function onSaveRecordRequest(){}
    }

?>