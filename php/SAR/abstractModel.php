<?php
class QueryGenerator
{
    private Ref $ref;
    private string $tableName;
    public $insertStmt;
    public $updateStmt;
    public $deleteStmt;

    public function __construct(Ref &$ref, $tableName) 
    {
        $this->tableName= strtolower($tableName);
        $this->ref = &$ref;
        $this->insertStmt = $this->generateInsertStmt();
        $this->updateStmt = $this->generateUpdateStmt();
        $this->deleteStmt = "DELETE FROM {$this->tableName} WHERE ". $this->getPK() . "=?;";
    }

    private function generateInsertStmt() : string 
    {
        $str ="INSERT INTO {$this->tableName} (";
        $fields = "";
        foreach($this->getFields() as $field)
            $fields = $fields . $field . ", ";

        foreach($this->getKFs() as $fk) 
            $fields = $fields . $fk . ", ";

        $fields = substr($fields, 0, strlen($fields)-2);
        return $str . $fields . ") " . $this->generateQuestionMakrs(count($this->getFields()) + count($this->getKFs()));    
    }

    private function generateUpdateStmt() : string 
    {
        $str = "UPDATE {$this->tableName} SET ";
        $where = " WHERE " . $this->getPK() . "=?;";
        $fields="";
        foreach($this->getFields() as $field)
            $fields = $fields . $field . "=?, ";

        foreach($this->getKFs() as $fk) 
            $fields = $fields . $fk . "=?, ";

        $fields = substr($fields, 0, strlen($fields)-2);
        return $str . $fields . $where;    
    }

    private function getFields() : Array
    {
        $array = array();
        $this->ref->getProperties();

        foreach($this->ref->getProperties() as $prop) 
        {
            $pattern = "/_/";
            $found = preg_match($pattern, $prop->getName());
            if ($found==1) 
            {
                array_push($array,$this->correctFieldName($prop->getName()));
            }
        }

        return $array;
    }

    private function getKFs() : Array
    {
        $array = array();
        $this->ref->getProperties();

        foreach($this->ref->getProperties() as $prop) 
        {
            $pattern = "/fk/";
            $found = preg_match($pattern, $prop->getName());
            if ($found==1) 
                array_push($array, $this->correctFKFieldName($prop));
        }

        return $array;
    }

    private function generateQuestionMakrs($count) : string 
    {
        $values="VALUES (";
        for($i=0; $i < $count; $i++) 
            $values= $values . "?, ";

        $values = substr($values, 0, strlen($values)-2);
        $values = $values . ");";
        return $values;
    }

    private function correctFieldName($str) : string
    {
        return str_replace("_", "", $str);
    }

    private function correctFKFieldName(ReflectionProperty $prop) : string
    {
        $ref = new Ref($prop->getType()->getName());
        return str_replace('pk', '', $ref->findProperty('pk'));
    }

    private function getPK() : string 
    {
        return str_replace('pk', '', $this->ref->findProperty('pk'));
    }
}

abstract class AbstractModel 
{
    public string $tableName;
    private Ref $ref;
    private QueryGenerator $queryGenerator;
    private $insertStmt;
    private $updateStmt;
    private $deleteStmt;
    private $selectStmt;

    public function __construct() 
    {
        $this->tableName = $this->me();
        $this->ref = new Ref($this);
        $this->queryGenerator = new QueryGenerator($this->ref,$this->tableName);
        $this->insertStmt = $this->queryGenerator->insertStmt;
        $this->updateStmt = $this->queryGenerator->updateStmt;
        $this->deleteStmt = $this->queryGenerator->deleteStmt;
        $this->selectStmt = "SELECT * FROM {$this->tableName};";
    }

    public function me() : string 
    {
        return get_class($this);
    }

    public function isNewRecord() : bool 
    {
        $this->ref->findProperty("pk");
        return $this->ref->getValue()==0;
    }

    public abstract static function readRow(array $row) : AbstractModel;

    public function returnNew(...$args) : AbstractModel 
    {
        return $this->ref->newInstanceArgs(...$args);
    }

    public function selectSQL() : string 
    {
        return $this->selectStmt;
    }

    public function insertSQL(): string
    {
        return $this->insertStmt;
    }

    public function updateSQL() : string 
    {
        return $this->updateStmt;
    }

    public function deleteSQL(): string
    {
        return $this->deleteStmt;
    }

    abstract public function checkIntegrity() : bool;
    abstract public function checkMandatory() : bool;


//1select
//2update
//3insert
//4delete
    abstract public function bindParam(int $crud) : string;
}
?>