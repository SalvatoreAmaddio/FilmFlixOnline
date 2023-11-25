<?php
abstract class AbstractModel 
{
    public string $tableName;

    public abstract static function readRow(array $row) : AbstractModel;

    public abstract static function returnNew() : AbstractModel;
    
    abstract public function updateSQL() : string;

    abstract public function bindParam(int $crud) : string;

    public function select() : string 
    {
        return "SELECT * FROM {$this->tableName};";
    }
}
?>