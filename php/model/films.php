<?php
    class Film extends AbstractModel
    {
        public int $filmID = 0;
        public string $title = "";
        public int $yearReleased = 0;
        public string $rating = "";
        public int $duration = 0;
        public Genre $genre;

        public function __construct() 
        {
            $this->tableName = "tblfilms";
            $this->yearReleased = date("Y");
            $this->genre = new Genre();
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
            return "SELECT tblfilms.*, genre.genreName FROM tblfilms INNER JOIN genre ON tblfilms.genreID = genre.genreID;";
        }

        public static function readRow(array $row) : Film
        {
            $film = new Film();
            $film->filmID = $row["filmID"];
            $film->title = $row["title"];
            $film->yearReleased = $row["yearReleased"];
            $film->rating = $row["rating"];
            $film->duration = $row["duration"];
            $film->genre = $film->genre->readRow($row);
            return $film;
        }

        public function insertSQL(): string
        {
            return "";
        }

        public function updateSQL(): string
        {
            return "UPDATE {$this->tableName} SET title=?, yearReleased=?, rating=?, duration=?, genreID=? WHERE filmID=?;";
        }

        public function bindParam(int $crud): string
        {
            switch($crud) 
            {
                case 1://select
                return "";
                case 2://update
                return "sisiii";
                case 3://insert
                return "";
                case 4://delete
                return "";
            }
        }

        public function __toString() : string
        {
            return $this->title;
        }
    }

?>