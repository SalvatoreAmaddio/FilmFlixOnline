<?php
    class Genre extends AbstractModel 
    {
        public int $genreID = 0;
        public string $genreName = "";

        public function __construct() 
        {
            $this->tableName = "genre";
        }

        public static function returnNew() : Genre
        {
            return new Genre();
        }

        public static function readRow(array $row) : Genre
        {
            $genre = new Genre();
            $genre->genreID = $row["genreID"];
            $genre->genreName = $row["genreName"];
            return $genre;
        }

        public function __toString() : string
        {
            return $this->genreName;
        }

    }
?>