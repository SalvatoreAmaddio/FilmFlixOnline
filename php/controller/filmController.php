<?php
include("php/SAR/database.php");
include("php/model/genre.php");
include("php/model/films.php");

    class FilmController extends AbstractController implements ITableDisplayer
    {        
        public function __construct() 
        {
            parent::__construct(new Film());
        }

        private function selectedRow($record) : string
        {
            if ($this->model == $record) 
            {
                return "style='background-color: coral;'";
            }

            return "";
        }

        public function displayTableData() 
        {
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
                        <td><button value={$film->filmID}>✎</button></td>
                        <td><button value={$film->filmID}>X</button></td>
                    </tr>";
            }
        }

        public function read() : bool
        {
            if (!$this->hasRequests()) return false;

            if ($this->is_r_selectedID()) 
            {
                $this->model = $this->findID($this->r_selectedID());
                $key = $this->currentIndex();
                $this->moveTo($key);
                $this->s_SelectedIndex($key);
                echo $this->displayTableData();
                return true;
            }

            if($this->is_r_updateRecordTracker()) 
            {
                $this->moveTo($this->s_SelectedIndex());
                echo $this->addRecordTracker();
                return true;
            }
        }

        public function findID($id) : Film
        {
            $result = array_values(array_filter($this->records, 
            function($e) use ($id)
            {
                /** @var Film $film */
                $film = $e;
                return $film->filmID == $id;
            }));

            if (count($result)>0) 
            {
                return $result[0];
            }
            return null;
        }

        public function model() : Film
        {
            /** @var Film $film */
            $film = $this->model;
            return $film;
        }
    }
?>