<?php
    class Genre extends AbstractModel 
    {
        public int $genreID = 0;
        public string $genreName = "";

        public function __construct() 
        {
            $this->tableName = "genre";
        }

        public function isNewRecord(): bool
        {
            return $this->genreID==0;
        }

        public function insertSQL(): string
        {
            return "";
        }
        
        public function deleteSQL(): string
        {
            return "";
        }

        public function updateSQL(): string
        {
            return "";    
        }

        public function bindParam(int $crud): string
        {
            switch($crud) 
            {
                case 1:
                return "";
                case 2:
                return "";
                case 3:
                return "";
                case 4:
                return "i";
            }
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