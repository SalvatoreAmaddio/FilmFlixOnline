<?php
if (!defined('SAR')) define('SAR', __DIR__);
require_once SAR."/ref.php";

class QueryGenerator
{
    private Ref $ref;
    private string $tableName;
    public string $insertStmt="";
    public string $updateStmt="";
    public string $deleteStmt="";
    public string $fieldsType="";
    public string $fkType="";

    public function __construct(Ref &$ref, $tableName) 
    {
        $this->tableName= strtolower($tableName);
        $this->ref = &$ref;
        $this->insertStmt = $this->generateInsertStmt();
        $this->updateStmt = $this->generateUpdateStmt();
        $this->deleteStmt = "DELETE FROM {$this->tableName} WHERE ". $this->ref->getPK() . "=?;";
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
        $where = " WHERE " . $this->ref->getPK() . "=?;";
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
        $this->fieldsType = "";
        foreach($this->ref->getProperties() as $prop) 
        {
            $pattern = "/_/";
            $found = preg_match($pattern, $prop->getName());
            if ($found==1) 
            {   $this->fieldsType = $this->fieldsType . $prop->getType()->getName()[0];
                array_push($array,Ref::correctFieldName($prop->getName()));
            }
        }

        return $array;
    }

    private function getKFs() : Array
    {
        $array = array();
        $this->ref->getProperties();

        $this->fkType = "";
        foreach($this->ref->getProperties() as $prop) 
        {
            $pattern = "/fk/";
            $found = preg_match($pattern, $prop->getName());
            if ($found==1) 
            {
                $this->fkType = $this->fkType . "i";
                array_push($array, $this->correctFKFieldName($prop));
            }
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

    private function correctFKFieldName(ReflectionProperty $prop) : string
    {
        $ref = new Ref($prop->getType()->getName());
        return str_replace('pk', '', $ref->findProperty('pk'));
    }

}

abstract class AbstractModel 
{
    public string $tableName;
    private Ref $ref;
    public QueryGenerator $queryGenerator;
    private $insertStmt;
    private $updateStmt;
    private $deleteStmt;
    public $selectStmt;

    public function __construct() 
    {
        $this->tableName = strtolower($this->me());
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

    public function readRow(array $row) : AbstractModel
    {
        $record = $this->returnNew();
        $fields = $record->ref->getFields();
        foreach($fields as $field) 
        {
            $record->ref->access($field->getName());
            $record->ref->setValue($row[Ref::correctFieldName($field->getName())]);
        }

        $record->ref->findProperty("pk");
        $record->ref->setValue($row[$record->ref->getPK()]);

        $fks = $record->ref->getFKs();
        foreach($fks as $fk) 
        {
            $ref = new Ref($fk->getType()->getName());
            $record->ref->access($fk->getName());
            $record->ref->setValue($ref->invoke("readRow",$row));
        }
        return $record;
    }

    public function returnNew(...$args) : AbstractModel 
    {
        return $this->ref->newInstanceArgs(...$args);
    }

    public function copy() : AbstractModel 
    {
        return $this->ref->copy();
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

    public function isEqual($obj) 
    {
        if ($obj instanceof AbstractModel) 
            return $this == $obj;

        if (is_int($obj)) 
        {
            $this->ref->findProperty("pk");
            return $this->ref->getValue()==$obj;
        }
    }

    public function primaryKey() : int 
    {
        $this->ref->findProperty("pk");
        return $this->ref->getValue();
    }

    abstract public function checkIntegrity() : bool;
    abstract public function checkMandatory() : bool;
//1select
//2update
//3insert
//4delete
        public function bindParam(int $crud): string
        {
            switch($crud) 
            {
                case 1://select
                return "s";
                case 2://update
                return $this->queryGenerator->fieldsType . $this->queryGenerator->fkType . "i";
                case 3://insert
                    return $this->queryGenerator->fieldsType . $this->queryGenerator->fkType;
                case 4://delete
                return "i";
            }
        }
}

?>