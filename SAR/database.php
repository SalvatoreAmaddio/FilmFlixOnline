<?php
if (!defined('SAR')) define('SAR', __DIR__);
require_once SAR."/abstractModel.php";

    class Database 
    {
        public string $serverName = "localhost";
        public string $user = "admin";
        public string $pwd = "soloio59";
        public string $db = "filmflixdb";
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
            $this->model = &$model;
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

        public function save(...$vars) : int
        {
            $id=0;
            $this->connect();
            $sql="";
            $params="";
            if ($this->model->isNewRecord()) 
            {
                $sql = $this->model->insertSQL();
                $params = $this->model->bindParam(3);    
            } 
            else 
            {
                $sql = $this->model->updateSQL();
                $params = $this->model->bindParam(2);    
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($params, ...$vars);    
            $stmt->execute();
            $id = ($this->model->isNewRecord()) ? $stmt->insert_id :  $stmt->affected_rows;
            $stmt->close();
            $this->close();
            return $id;
        }

        public function delete(...$vars) : int
        {
            $this->connect();
            $stmt = $this->conn->prepare($this->model->deleteSQL());
            $stmt->bind_param($this->model->bindParam(4), ...$vars);    
            $stmt->execute();
            $id = $stmt->affected_rows;
            $stmt->close();
            $this->close();
            return $id;
        }

        public function preparedSelect(string $sql, string $paramTypes, ...$vars) : int
        {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($paramTypes, ...$vars);
            $stmt->execute();
            $id = $stmt->affected_rows;
            $this->table = $stmt->get_result();
            $stmt->close();
            return $id;    
        }

        public function select()
        {
            $this->table = $this->conn->query($this->model->selectSQL());
            if (!$this->table) {
       throw new Exception("Database Error [{$this->conn->errno}] {$this->conn->error}");
}
        }
    }
?>