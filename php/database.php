<?php
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

        public function __construct() 
        {
            $this->connect();
            $this->getColumns();
            $this->select();
        }

        public function connect() 
        {
            $this->conn = new mysqli($this->serverName, $this->user, $this->pwd, $this->db);
            if ($this->conn->connect_error)
                die("Connection failed: " . $this->conn->connect_error);
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
            $this->table = $this->conn->query("SELECT * FROM tblFilms;");
        }
    }

    
$db = new Database();  
$db->select();

while($row = $db->table->fetch_assoc()) 
{
    echo $row["title"] . " " . $row["yearReleased"];
    echo "<br>";
}

?>