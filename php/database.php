<?php
    class Database 
    {
        public $serverName = "localhost";
        public $user = "admin";
        public $pwd = "password1";
        public $db = "FilmFlixDB";
        private $conn;
                
        public function __construct() 
        {
            $this->conn = mysqli_connect($this->serverName,$this->user,$this->pwd,$this->db);
            if ($this->conn) 
                echo 'Connected';
            else 
               echo 'Failed'; 
        }
    }

    
new Database();    
?>