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

        public function __toString() : string
        {
            return $this->title;
        }
    }

?>