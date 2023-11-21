<?php
    class Database 
    {
        public string $serverName = "localhost";
        public string $user = "admin";
        public string $pwd = "password1";
        public string $db = "FilmFlixDB";
        private $conn;
        protected AbstractModel $model;
        protected $result;
        protected $schema;

        public function __construct() 
        {
            $this->connect();
            $this->getColumns();
            $this->select();
        }

        public function connect() 
        {
            $this->conn = mysqli_connect($this->serverName, $this->user, $this->pwd, $this->db);
        }

        public function isConnected() : bool 
        {
            if ($this->conn) return true;
            return false;
        }

        public function columns() : int 
        {
            return $this->schema->num_rows;
        }

        public function isEmpty() : bool 
        {
            return $this->result->num_rows == 0;
        }

        public function getColumns() 
        {
            $this->schema = $this->conn->query($this->schemaQuery());
        }

        private function schemaQuery() : string
        {
            return "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$this->db}' AND TABLE_NAME = 'tblFilms';";
        }

        public function select() 
        {
            $this->result = $this->conn->query("SELECT * FROM tblFilms;");
            $arr = $this->result->fetch_assoc();
                print_r($arr);

        }
    }

    
$db = new Database();    
?>