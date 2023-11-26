<?php
    class Genre extends AbstractModel 
    {
        public int $pkgenreID = 0;
        public string $genreName = "";

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

        public static function readRow(array $row) : Genre
        {
            $genre = new Genre();
            $genre->pkgenreID = $row["genreID"];
            $genre->genreName = $row["genreName"];
            return $genre;
        }

        public function __toString() : string
        {
            return $this->genreName;
        }

        public function checkIntegrity(): bool
        {
            return true;
        }

        public function checkMandatory(): bool
        {
            switch(true) 
            {
                case is_null($this->pkgenreID):
                return false;
                case is_null($this->genreName):
                return false;
            }
            return true;
        }

    }

    class Rating extends AbstractModel 
    {
        public int $pkratingID = 0;
        public string $ratingName = "";


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

        public static function readRow(array $row) : Rating
        {
            $rating = new Rating();
            $rating->pkratingID = $row["ratingID"];
            $rating->ratingName = $row["ratingName"];
            return $rating;
        }

        public function __toString() : string
        {
            return $this->ratingName;
        }

        public function checkIntegrity(): bool
        {
            return true;
        }

        public function checkMandatory(): bool
        {
            switch(true) 
            {
                case is_null($this->pkratingID):
                return false;
                case is_null($this->ratingName):
                return false;
            }
            return true;
        }

    }
?>