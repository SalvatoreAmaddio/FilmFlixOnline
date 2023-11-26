<?php
    class Film extends AbstractModel
    {
        public int $filmID = 0;
        public string $title = "";
        public int $yearReleased = 0;
        public Rating $rating;
        public int $duration = 0;
        public Genre $genre;
        private string $selectStr='';

        public function __construct() 
        {
            $this->tableName = "tblfilms";
            $this->yearReleased = date("Y");
            $this->genre = new Genre();
            $this->rating = new Rating();
            $this->selectStr = "SELECT tblfilms.*, rating.ratingName, genre.genreName FROM tblfilms INNER JOIN genre ON tblfilms.genreID = genre.genreID INNER JOIN rating ON tblfilms.ratingID = rating.ratingID ORDER BY tblFilms.filmID;";
        }

        public function filter(string $filter)
        {
            switch($filter) 
            {
                case $filter == 'genre':
                    $this->selectStr = "SELECT tblfilms.*, rating.ratingName, genre.genreName FROM tblfilms INNER JOIN genre ON tblfilms.genreID = genre.genreID INNER JOIN rating ON tblfilms.ratingID = rating.ratingID WHERE tblfilms.genreID = ? ORDER BY tblFilms.filmID;";
                break;
                case $filter == 'rating':
                    $this->selectStr = "SELECT tblfilms.*, rating.ratingName, genre.genreName FROM tblfilms INNER JOIN genre ON tblfilms.genreID = genre.genreID INNER JOIN rating ON tblfilms.ratingID = rating.ratingID WHERE tblfilms.ratingID = ? ORDER BY tblFilms.filmID;";
                break;
            }
        }

        public static function readRow(array $row) : Film
        {
            $film = new Film();
            $film->filmID = $row["filmID"];
            $film->title = $row["title"];
            $film->yearReleased = $row["yearReleased"];
            $film->rating = $film->rating->readRow($row);
            $film->duration = $row["duration"];
            $film->genre = $film->genre->readRow($row);
            return $film;
        }

        public function isNewRecord(): bool
        {
            return $this->filmID==0;
        }
        
        public static function returnNew() : Film
        {
            return new Film();
        }

        public function select() : string 
        {
            return $this->selectStr;
        }

        public function insertSQL(): string
        {
            return "INSERT INTO {$this->tableName} (title, yearReleased, ratingID, duration, genreID) VALUES (?,?,?,?,?);";
        }

        public function updateSQL(): string
        {
            return "UPDATE {$this->tableName} SET title=?, yearReleased=?, ratingID=?, duration=?, genreID=? WHERE filmID=?;";
        }

        public function deleteSQL(): string
        {
            return "DELETE FROM {$this->tableName} WHERE filmID=?;";
        }

        public function bindParam(int $crud): string
        {
            switch($crud) 
            {
                case 1://select
                return "i";
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
            $this->title = ucwords($this->title);
            if ($this->yearReleased < 1888) return false;
            if ($this->yearReleased > date("Y")) return false;
            if ($this->duration <= 0) return false;
            return true;
        }

        public function checkMandatory(): bool
        {
            switch(true) 
            {
                case is_null($this->title):
                return false;
                case empty($this->title):
                return false;
                case is_null($this->yearReleased):
                return false;
                case is_null($this->duration):
                return false;
                case !$this->genre->checkMandatory():
                return false;
                case !$this->rating->checkMandatory():
                return false;
            }
            return true;
        }

        public function __toString() : string
        {
            return $this->title;
        }
    }

?>