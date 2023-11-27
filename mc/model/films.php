<?php
require_once SAR."\\abstractModel.php";
require_once model."\genre.php";

class Film extends AbstractModel
{
    public int $pkfilmID = 0;
    public string $_title = "";
    public int $_yearReleased = 0;
    public int $_duration = 0;
    public Rating $fkrating;
    public Genre $fkgenre;

    public function __construct() 
    {
        parent::__construct();
        $this->_yearReleased = date("Y");
        $this->fkgenre = new Genre();
        $this->fkrating = new Rating();
    }
    
    public function selectSQL() : string 
    {
        return "SELECT film.*, rating.ratingName, genre.genreName FROM film INNER JOIN genre ON film.genreID = genre.genreID INNER JOIN rating ON film.ratingID = rating.ratingID ORDER BY film.filmID;";
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
        return .$this->_title;
    }
}

new Film();
?>