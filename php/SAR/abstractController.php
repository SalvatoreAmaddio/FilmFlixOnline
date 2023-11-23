<?php    
    abstract class AbstractController 
    {
        public $records = array();
        protected AbstractModel $model;
        public Database $db;
        protected int $recordIndex = 0;
        public RecordTracker $recordTracker;
        public RequestManager $requests;
        public SessionManager $sessions;

        public function __construct(AbstractModel $model) 
        {
            $this->db = new Database();
            $this->model = $model;
            $this->db->setModel($this->model);
            $this->db->connect();
            $this->recordTracker =  new RecordTracker($this->model,$this->recordIndex,$this->records);
            $this->requests = new RequestManager();
            $this->sessions = new SessionManager();
        }

        public abstract function model() : AbstractModel;
        public abstract function displayData();
        public abstract function findIDCriteria($record,$id) : bool;

        public function recordCount() : int 
        {
            return count($this->records);
        }

        public function fetchData() 
        {
            $this->db->select();
            while($row = $this->db->table->fetch_assoc()) 
                array_push($this->records, $this->model->readRow($row));

            if ($this->recordCount()>0)
                $this->model = $this->records[$this->recordIndex];

            $this->db->close();
        }

        public function readInputs() : bool
        {
            return false;           
        }

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

    abstract class AbstractFormListController extends AbstractController
    {

        protected function selectedRow($record) : string
        {
            if ($this->model == $record) 
            {
                return "class='selectedRow'";
            }
            return "";
        }

        public function readInputs() : bool
        {
            return false;           
        }
    }

    interface IManager 
    {
        public function isEmpty() : bool;
    }

    class SessionManager implements IManager
    {
        public function isEmpty() : bool 
        {
            return count($_SESSION) == 0;
        }

        public function s_SelectedIndex(int $index = -1)
        {
            if ($index < 0) return $_SESSION['selectedIndex'];
            $_SESSION['selectedIndex'] = $index;
        }

        public function issetSelectedID() : bool
        {
            return isset($_SESSION['selectedID']);
        }

        public function s_SelectedID(int $index = -1)
        {
            if ($index < 0) return $_SESSION['selectedID'];
            $_SESSION['selectedID'] = $index;
        }

        public function unsetSelectedID()
        {
            unset($_SESSION['selectedID']);
        }

        public function s_Amend(bool $value)
        {
            $_SESSION['amend'] = $value;
        }

        public function issetAmend() : bool
        {
            return isset($_SESSION['amend']);
        }

        public function unsetAmend() 
        {
            unset($_SESSION['amend']);
        }
    }

    class RequestManager implements IManager
    {

        public function isEmpty() : bool 
        {
            return count($_REQUEST) == 0;
        }

        public function is_r_amend() : bool 
        {
            return isset($_REQUEST['amend']);
        }

        public function is_r_deleteID() : bool 
        {
            return isset($_REQUEST['deleteID']);
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

        public function r_amendID() : int 
        {
            return $_REQUEST['amendID'];
        }
    }

    class RecordTracker 
    {
     
        private $records = array();
        private AbstractModel $model;
        private int $recordIndex = 0;

        public function __construct(AbstractModel &$model, int &$recordIndex, &$records) 
        {
            $this->model = &$model;
            $this->recordIndex = &$recordIndex;
            $this->records = &$records;
        }

        public function recordCount() : int 
        {
            return count($this->records);
        }


        public function currentIndex() : int 
        {
            return array_search($this->model,$this->records);
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
        ######        

    }
?>