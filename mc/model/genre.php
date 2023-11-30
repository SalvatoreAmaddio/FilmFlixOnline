<?php
if (!defined('SAR')) define('SAR', dirname(getcwd(),2)."/SAR");
require_once SAR."/abstractModel.php";

    class Genre extends AbstractModel 
    {
        public int $pkgenreID = 0;
        public string $_genreName = "";

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