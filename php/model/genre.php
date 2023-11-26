<?php
    class Genre extends AbstractModel 
    {
        public int $pkgenreID = 0;
        public string $_genreName = "";

        public static function readRow(array $row) : Genre
        {
            $genre = new Genre();
            $genre->pkgenreID = $row["genreID"];
            $genre->_genreName = $row["genreName"];
            return $genre;
        }

        public function __toString() : string
        {
            return $this->_genreName;
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
                case is_null($this->_genreName):
                return false;
            }
            return true;
        }

    }

    class Rating extends AbstractModel 
    {
        public int $pkratingID = 0;
        public string $_ratingName = "";

        public static function readRow(array $row) : Rating
        {
            $rating = new Rating();
            $rating->pkratingID = $row["ratingID"];
            $rating->_ratingName = $row["ratingName"];
            return $rating;
        }

        public function __toString() : string
        {
            return $this->_ratingName;
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
                case is_null($this->_ratingName):
                return false;
            }
            return true;
        }

    }
?>