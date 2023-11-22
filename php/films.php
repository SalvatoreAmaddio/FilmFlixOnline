<?php
    class Film extends AbstractModel
    {
        public int $filmID = 0;
        public string $title = "";
        public int $yearReleased = 0;
        public string $rating = "";
        public int $duration = 0;
        public int $genreID = 0;

        public function __construct() 
        {
            $this->tableName = "tblFilms";
            $this->yearReleased = date("Y");
        }

        public static function returnNew() : Film
        {
            return new Film();
        }

        public static function readRow(array $row) : Film
        {
            $film = new Film();
            $film->filmID = $row["filmID"];
            $film->title = $row["title"];
            $film->yearReleased = $row["yearReleased"];
            $film->rating = $row["rating"];
            $film->duration = $row["duration"];
            $film->genreID = $row["genreID"];
            return $film;
        }

        public function __toString() : string
        {
            return $this->title;
        }
    }

?>