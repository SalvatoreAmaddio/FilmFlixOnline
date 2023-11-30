<?php    
if(session_status() !== PHP_SESSION_ACTIVE) session_start();
    
if (!defined('SAR')) define('SAR',__DIR__);
    require_once SAR."/database.php";
    
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
            $this->model = $model;
            $this->db = new Database();
            $this->requests = new RequestManager();
            $this->sessions = new SessionManager($this->me());
            $this->recordTracker =  new RecordTracker($this->model, $this->recordIndex,$this->records);
            $this->db->setModel($this->model);
            $this->db->connect();
        }

        public function refresh() 
        {
            header('Location: '.$_SERVER['REQUEST_URI']);
        }

        public function me() : string 
        {
            return get_class($this);
        }

        public function len() : int 
        {
            return $this->recordCount()-1;
        }

        public function recordCount() : int 
        {
            return count($this->records);
        }

        public function hasRecords() : bool 
        {
            return $this->recordCount() > 0;
        }

        public function model()
        {
            return $this->model;
        }

        public function delete()
        {
            $this->db->delete($this->sessions->selectedID());        
        }

        public function findIDCriteria($record, $id) : bool 
        {
            return $record->isEqual($id);            
        }

        public function findByID($id)
        {
            $result = array_values(array_filter($this->records, 
            function($record) use ($id)
            {
                return $this->findIDCriteria($record, $id);
            }));

            return (count($result)>0) ? $result[0] : null;
        }

        public function findRecordCriteria($record,$value) : bool
        {
            return is_numeric(strpos(strtolower(trim($record)), strtolower(trim($value))));
        }
        
        public function filterRecords($value)
        {
            $this->records = array_values(array_filter($this->records, 
            function($record) use ($value)
            {
                return $this->findRecordCriteria($record, $value);
            }));
        }

        protected function resetIndex(int $direction) 
        {
            $this->recordIndex = $this->sessions->selectedIndex();
            switch($direction) 
            {
                case -1:
                    if ($this->recordCount()==0) 
                    {
                        $this->recordIndex = 0;
                        return;
                    }

                    if ($this->recordIndex > 0) 
                        $this->recordIndex--;
                break;
                case 0:
                    $this->recordTracker->moveNext();
                break;
                case 1:
                    $this->recordTracker->movePrevious();
                break;
                case 2:
                    $this->recordTracker->moveFirst();
                break;
                case 3:
                    $this->recordTracker->moveLast();
                break;
            }
            $this->sessions->selectedIndex($this->recordIndex);
        }

        public function preparedFetchData(string $sql="", string $paramTypes = "", ...$vars) 
        {
            $this->records = array_diff($this->records, $this->records);
                $this->db->preparedSelect($sql, $paramTypes, ...$vars);
            while($row = $this->db->table->fetch_assoc()) 
                array_push($this->records, $this->model->readRow($row));

            if ($this->hasRecords()) 
                $this->model = $this->records[$this->recordIndex];
            
            $this->db->close();
        }

        public function fetchData() 
        {
            $this->records = array_diff($this->records, $this->records);
                $this->db->select();
            while($row = $this->db->table->fetch_assoc()) 
                array_push($this->records, $this->model->readRow($row));

            if ($this->hasRecords()) 
                $this->model = $this->records[$this->recordIndex];
            
            $this->db->close();
        }

        public function onSelectedIDRequest()
        {
            $this->sessions->selectedID($this->requests->selectedID());
            $this->model = $this->findByID( $this->sessions->selectedID());
            $this->sessions->selectedIndex($this->recordTracker->currentIndex());
            $this->recordTracker->moveTo($this->sessions->selectedIndex());
        }

        public function onUpdateRecordTrackerRequest() 
        {            
            $this->recordTracker->moveTo($this->sessions->selectedIndex());
            echo $this->recordTracker->reportRecordPosition();           
        }

        public function onNewRecordRequest() 
        {
            $this->recordTracker->moveNew();
            $this->sessions->selectedIndex($this->recordIndex);
            $this->sessions->selectedID(0); 
        }

        public function onDeleteRecordRequest() 
        {
            $this->delete();
            $this->resetIndex(-1);
        }

        public function readRequests()
        {
            switch(true) 
            {
                case $this->requests->is_selectedID(): $this->onSelectedIDRequest();
                break;
                case $this->requests->is_newRecord(): $this->onNewRecordRequest();
                break;
                case $this->requests->is_goNext(): $this->resetIndex(0);
                break;
                case $this->requests->is_goPrevious(): $this->resetIndex(1);
                break;
                case $this->requests->is_goFirst(): $this->resetIndex(2);
                break;
                case $this->requests->is_goLast(): $this->resetIndex(3);
                break;
                case $this->requests->is_updateRecordTracker(): $this->onUpdateRecordTrackerRequest();
                break;
                case $this->requests->is_delete(): $this->onDeleteRecordRequest();
                break;
            }
        }

        public function readSessions()
        {
            if ($this->sessions->isEmpty()) return;
            switch(true) 
            {
                case $this->sessions->issetSelectedIndex():
                    $this->recordTracker->moveTo($this->sessions->selectedIndex());
                break;
            }
        }        
    }

    abstract class AbstractFormController extends AbstractController
    {
        public function __construct(AbstractModel $model) 
        {
            parent::__construct($model);
            $this->recordTracker->allowNewRecord = true;
        }

        abstract public function save(Array $data);

        abstract function fillRecord(Array $data); 

        public function onDeleteRecordRequest() 
        {
            parent::onDeleteRecordRequest();
            echo true;
        }

        public function readRequests() 
        {
            parent::readRequests();
            switch(true) 
            {
                case $this->requests->is_save(): $this->onSaveRecordRequest();
                break;
            }
        }

        public function onSaveRecordRequest() 
        {
            $data = $this->requests->data();
            $this->fillRecord($data);
            if ($this->model->checkIntegrity() && $this->model->checkMandatory())
                $this->save($data);
            echo $this->model->isNewRecord();
        }
    }

    abstract class AbstractFormListController extends AbstractController
    {
        public abstract function displayData();        
        public abstract function onSearchValueRequest();
                
        public function readRequests() 
        {
            parent::readRequests();
            switch(true) 
            {
                case $this->requests->is_searchValue(): 
                    $this->onSearchValueRequest();
                break;
            }
        }

        protected function selectedRow($record) : string
        {
            if ($this->sessions->selectedIndex() > $this->len()) 
                $this->sessions->selectedIndex(0);
            
            if ($this->records[$this->sessions->selectedIndex()] == $record) 
                return "class='selectedRow'";
            
            return "";
        }

        protected function resetIndex($direction) 
        {
            parent::resetIndex($direction);
            echo $this->displayData();
        }

        public function onDeleteRecordRequest()
        {
            parent::onDeleteRecordRequest();
            echo $this->displayData();
        }

        public function onSelectedIDRequest()
        {
            parent::onSelectedIDRequest();
            $this->displayData();
        }   
    }

    interface IManager 
    {
        public function isEmpty() : bool;
    }

    class SessionManager implements IManager
    {
        private string $origin;

        public function __construct(string $origin) 
        {
            $this->origin = $origin;
            if (!$this->issetSelectedIndex())
                $this->selectedIndex(0);
        }

        public function isEmpty() : bool 
        {
            return count($_SESSION) == 0;
        }

        public function selectedIndex(int $index = -1)
        {
            if ($index < 0) return $_SESSION[$this->origin.'selectedIndex'];
            $_SESSION[$this->origin.'selectedIndex'] = $index;
        }

        public function setSearchValue($value) 
        {
            $_SESSION[$this->origin.'searchValue'] = $value;
        }

        public function getSearchValue() 
        {
            return $_SESSION[$this->origin.'searchValue'];
        }

        public function issetSearchValue() : bool
        {
            return isset($_SESSION[$this->origin.'searchValue']);
        }

        public function issetSelectedIndex() : bool
        {
            return isset($_SESSION[$this->origin.'selectedIndex']);
        }

        public function issetSelectedID() : bool
        {
            return isset($_SESSION[$this->origin.'selectedID']);
        }

        public function selectedID(int $index = -1)
        {
            if ($index < 0) return $_SESSION[$this->origin.'selectedID'];
            $_SESSION[$this->origin.'selectedID'] = $index;
        }

        public function unsetSelectedID()
        {
            unset($_SESSION[$this->origin.'selectedID']);
        }
    }

    class RequestManager implements IManager
    {

        public function isEmpty() : bool 
        {
            return count($_REQUEST) == 0;
        }

        public function is_deleteID() : bool 
        {
            return isset($_REQUEST['deleteID']);
        }

        public function is_updateRecordTracker() : bool 
        {
            return isset($_REQUEST['updateRecordTracker']);
        }

        public function is_selectedID() : bool 
        {
            return isset($_REQUEST["selectedID"]);
        }

        public function is_searchValue() : bool 
        {
            return isset($_REQUEST["searchValue"]);
        }

        public function searchValue() : string
        {
            return $_REQUEST["searchValue"];
        }

        public function setSearchValue(string $value)
        {
            $_REQUEST["searchValue"] = $value;
        }

        public function is_delete() : bool 
        {
            return isset($_REQUEST["delete"]);
        }

        public function is_save() : bool 
        {
            return isset($_REQUEST["save"]);
        }

        public function data() : Array
        {
            return json_decode($_POST['save']);
        }

        public function is_goNext() : bool 
        {
            return isset($_REQUEST["goNext"]);
        }

        public function is_goPrevious() : bool 
        {
            return isset($_REQUEST["goPrevious"]);
        }

        public function is_goFirst() : bool 
        {
            return isset($_REQUEST["goFirst"]);
        }

        public function is_goLast() : bool 
        {
            return isset($_REQUEST["goLast"]);
        }

        public function is_newRecord() : bool 
        {
            return isset($_REQUEST["newRecord"]);
        }

        public function selectedID() : int
        {
            return $_REQUEST["selectedID"];
        }

    }

    class RecordTracker 
    {
     
        private $records = array();
        private AbstractModel $model;
        private int $recordIndex = 0;
        public bool $allowNewRecord = false;

        public function __construct(AbstractModel &$model, int &$recordIndex, &$records) 
        {
            $this->model = &$model;
            $this->recordIndex = &$recordIndex;
            $this->records = &$records;
        }

        public function currentIndex() : int 
        {
            return array_search($this->model,$this->records);
        }

        public function len() : int 
        {
            return $this->recordCount()-1;
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

        public function currentRecordPosition() : int
        {
            return $this->recordIndex + 1;
        }

        public function EOF() : bool 
        {
            return $this->recordIndex == $this->len();
        }

        public function isNewRecord() : bool 
        {
            return $this->recordIndex > $this->len();
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

            switch(true) 
            {
                case $this->isNewRecord() && !$this->allowNewRecord:
                    $this->recordIndex = 0;
                break;
                case $this->isNewRecord() && $this->allowNewRecord:
                    $this->moveNew();
                    return;
                break;
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
            $this->model = $this->model->returnNew();
        }

        public function moveTo($index) 
        {
            if ($index == $this->recordCount()) 
            {
                $this->moveNew();
                return;
            }

            if ($index > $this->len()) $index = $this->currentRecordPosition()-1;

            $this->recordIndex = $index;
            if ($this->recordCount() > 0)
                $this->model = $this->records[$this->recordIndex];
        }

        public function addRecordTracker() 
        {
            echo "<p>Developed by Salvatore Amaddio Rivolta</p>
                  <section class=recordTrackerSection>
                    <div class=recordTracker>
                        <button>⮜⮜</button>
                        <button>⮜</button>
                        <label class='recordTrackerLabel'>{$this->reportRecordPosition()}</label>
                        <button>➤</button>
                        <button>➤➤</button>
                        <button class=newButton>+</button>
                    </div>
                 </section>";
        }
        ######        

    }
?>