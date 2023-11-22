<?php
    abstract class AbstractController 
    {
        public $records = array();
        protected AbstractModel $model;
        public Database $db;
        protected int $recordIndex = 0;

        public function __construct(AbstractModel $model) 
        {
            $this->db = new Database();
            $this->model = $model;
            $this->db->setModel($this->model);
            $this->db->connect();
        }

        public function s_SelectedIndex(int $index = -1)
        {
            if ($index < 0) return $_SESSION['selectedIndex'];
            $_SESSION['selectedIndex'] = $index;
        }

        public function is_r_updateRecordTracker() : bool 
        {
            return isset($_REQUEST['updateRecordTracker']);
        }

        public function is_r_selectedID() : bool 
        {
            return isset($_REQUEST["selectedID"]);
        }

        public function is_r_newRecord() : bool 
        {
            return isset($_REQUEST["newRecord"]);
        }

        public function r_selectedID() : int
        {
            return $_REQUEST["selectedID"];
        }

        public function hasRequests() : bool 
        {
            return count($_REQUEST) > 0;
        }

        public function currentIndex() : int 
        {
            return array_search($this->model,$this->records);
        }

        public function reload() 
        {
            $this->readTable();
        }

        public function recordCount() : int 
        {
            return count($this->records);
        }

        public function reportRecordPosition() : string
        {
            if ($this->recordCount()==0) return "No Records";
            if ($this->isNewRecord()) return "New Record";
            return "Record {$this->currentRecordPosition()} of {$this->recordCount()}";
        }

        public function printInfo() 
        {
            echo "<div style='border: 1px solid black; padding: .5rem'>
                <p><span>Record Position: </span>{$this->currentRecordPosition()}</p>
                <p><span>Current Record: </span>{$this->model}</p>
                <p><span>Record Count: </span>{$this->recordCount()}</p>
                <p><span>New Record: </span>{$this->isNewRecord()}</p>
                <p><span>BOF: </span>{$this->BOF()}</p>
                <p><span>EOF: </span>{$this->EOF()}</p>
                <p>{$this->reportRecordPosition()}</p>
                </div>";
        }

        public abstract function model() : Film;

        public function readTable() 
        {
            $this->db->select();
            while($row = $this->db->table->fetch_assoc()) 
                array_push($this->records, $this->model->readRow($row));

            if ($this->recordCount()>0)
                $this->model = $this->records[$this->recordIndex];

            $this->db->close();
        }

        public function currentRecordPosition() : int
        {
            return $this->recordIndex + 1;
        }

        public function EOF() : bool 
        {
            return $this->recordIndex == $this->recordCount()-1;
        }

        public function isNewRecord() : bool 
        {
            return $this->recordIndex > $this->recordCount()-1;
        }

        public function BOF() : bool 
        {
            return $this->recordIndex == 0;
        }

        public function outOfRange() : bool 
        {
            return $this->recordIndex < 0;
        }

        public function moveNext() 
        {
            $this->recordIndex++;
            if ($this->isNewRecord()) 
            {
                $this->recordIndex = $this->recordCount()-1;
                return;
            }

            $this->model = $this->records[$this->recordIndex];
        }

        public function movePrevious() 
        {
            $this->recordIndex--;
            if ($this->outOfRange()) 
            {
                $this->recordIndex = 0;
            }

            $this->model = $this->records[$this->recordIndex];
        }

        public function moveLast() 
        {
            $this->recordIndex = $this->recordCount()-1;
            $this->model = $this->records[$this->recordIndex];
        }

        public function moveFirst() 
        {
            $this->recordIndex = 0;
            $this->model = $this->records[$this->recordIndex];
        }

        public function moveNew() 
        {
            $this->recordIndex = $this->recordCount();
            $this->model = $this->model::returnNew();
        }

        public function moveTo($index) 
        {
            $this->recordIndex = $index;
            $this->model = $this->records[$this->recordIndex];
        }

        public function addRecordTracker() 
        {
            echo "<section class=recordTrackerSection>
                    <div class=recordTracker>
                        <button>⮜⮜</button>
                        <button>⮜</button>
                        <label>{$this->reportRecordPosition()}</label>
                        <button>➤</button>
                        <button>➤➤</button>
                        <button class=newButton>+</button>
                    </div>
                 </section>";
        }

        abstract public function findIDCriteria($record,$id) : bool;

        public function findID($id) : AbstractModel
        {
            $result = array_values(array_filter($this->records, 
            function($record) use ($id)
            {
                return $this->findIDCriteria($record, $id);
            }));

            return (count($result)>0) ? $result[0] : null;
        }
    }
    
    interface ITableDisplayer 
    {
        public function displayTableData();
    }

    interface IFormDisplayer 
    {
        public function displayTableData();
    }
?>