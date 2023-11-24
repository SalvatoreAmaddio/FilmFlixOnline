<?php
    $x = explode("php", __DIR__);
    require_once $x[0].'/php/SAR/database.php';
    require_once $x[0].'php/model/genre.php';
    require_once $x[0].'php/model/films.php';

    class FilmFormController extends  AbstractFormController
    {

        public function __construct() 
        {
            parent::__construct(new Film());
        }

        public function findIDCriteria($record,$id) : bool
        {
              /** @var Film $film */
              $film = $record;
              return $film->filmID == $id;
        }

        public function findRecordCriteria($record,$value) : bool
        {
              /** @var Film $obj */
              $obj = $record;
              return str_contains(strtolower(trim($obj->title)), strtolower(trim($value)));
        }

        public function displayData() 
        {
        }

        public function model() : Film
        {
            /** @var Film $film */
            $film = $this->model;
            return $film;
        }
    }

?>