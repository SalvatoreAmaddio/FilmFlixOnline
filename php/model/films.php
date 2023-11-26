<?php
    class Film extends AbstractModel
    {
        public int $pkfilmID = 0;
        public string $_title = "";
        public int $_yearReleased = 0;
        public Rating $fkrating;
        public int $_duration = 0;
        public Genre $fkgenre;

        public function __construct() 
        {
            parent::__construct();
            $this->_yearReleased = date("Y");
            $this->fkgenre = new Genre();
            $this->fkrating = new Rating();
        }

        public static function readRow(array $row) : Film
        {
            $film = new Film();
            $film->pkfilmID = $row["filmID"];
            $film->_title = $row["title"];
            $film->_yearReleased = $row["yearReleased"];
            $film->fkrating = $film->fkrating->readRow($row);
            $film->_duration = $row["duration"];
            $film->fkgenre = $film->fkgenre->readRow($row);
            return $film;
        }
        
        public function select() : string 
        {
            return "SELECT tblfilms.*, rating.ratingName, genre.genreName FROM tblfilms INNER JOIN genre ON tblfilms.genreID = genre.genreID INNER JOIN rating ON tblfilms.ratingID = rating.ratingID ORDER BY tblfilms.filmID;";
        }

        public function bindParam(int $crud): string
        {
            switch($crud) 
            {
                case 1://select
                return "";
                case 2://update
                return "siiiii";
                case 3://insert
                    return "siiii";
                case 4://delete
                return "i";
            }
        }

        public function checkIntegrity(): bool
        {
            $this->_title = ucwords($this->_title);
            if ($this->_yearReleased < 1888) return false;
            if ($this->_yearReleased > date("Y")) return false;
            if ($this->_duration <= 0) return false;
            return true;
        }

        public function checkMandatory(): bool
        {
            switch(true) 
            {
                case is_null($this->_title):
                return false;
                case empty($this->_title):
                return false;
                case is_null($this->_yearReleased):
                return false;
                case is_null($this->_duration):
                return false;
                case !$this->fkgenre->checkMandatory():
                return false;
                case !$this->fkrating->checkMandatory():
                return false;
            }
            return true;
        }

        public function __toString() : string
        {
            return "Ciao".$this->_title;
        }
    }

?>