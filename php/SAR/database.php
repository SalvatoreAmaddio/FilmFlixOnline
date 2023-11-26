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
            if ($this->model->isNewRecord()) 
            {
                $id = $stmt->insert_id;
            }
            else 
            {
                $id = $stmt->affected_rows;
            }
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

        public function selectPrep(...$vars) : int
        {
            $this->connect();
            $stmt = $this->conn->prepare($this->model->select());
            $stmt->bind_param($this->model->bindParam(1), ...$vars);    
            $stmt->execute();
            $result = $stmt->get_result();
            $this->table = $result->fetch_assoc();
            $id = $stmt->affected_rows;
            $stmt->close();
            $this->close();
            return $id;
        }

        public function select() 
        {
            $this->table = $this->conn->query($this->model->select());
        }
    }
?>