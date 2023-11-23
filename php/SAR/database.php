<?php
    require_once 'abstractModel.php"';
    require_once 'abstractController.php';
    class Database 
    {
        public string $serverName = "localhost";
        public string $user = "admin";
        public string $pwd = "password1";
        public string $db = "FilmFlixDB";
        private $conn;
        protected AbstractModel $model;
        protected $schema;
        public $table;
        public $tableAssoc;

        public function connect() 
        {
            $this->conn = new mysqli($this->serverName, $this->user, $this->pwd, $this->db);
            if ($this->conn->connect_error)
                die("Connection failed: " . $this->conn->connect_error);

            $this->getColumns();
        }

        public function setModel(AbstractModel &$model) 
        {   
            $this->model = $model;
        }

        public function isConnected() : bool 
        {
            if ($this->conn) return true;
            return false;
        }

        public function close() 
        {
            $this->conn->close();
        }

        public function columns() : int 
        {
            return $this->schema->num_rows;
        }

        public function isEmpty() : bool 
        {
            return $this->table->num_rows == 0;
        }

        private function getColumns() 
        {
            $this->schema = $this->conn->query($this->schemaQuery());
        }

        private function schemaQuery() : string
        {
            return "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$this->db}' AND TABLE_NAME = 'tblFilms';";
        }

        public function select() 
        {
            $this->table = $this->conn->query($this->model->select());
        }
    }
?>