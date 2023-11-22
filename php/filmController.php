<?php
    class FilmController extends AbstractController 
    {
        public function __construct() 
        {
            parent::__construct(new Film());
        }
    }
?>