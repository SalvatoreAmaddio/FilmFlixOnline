<?php
abstract class AbstractModel 
{
    public string $tableName;

    public abstract static function readRow(array $row) : AbstractModel;

    public abstract static function returnNew() : AbstractModel;

    abstract public function insertSQL() : string;
    abstract public function updateSQL() : string;
    abstract public function deleteSQL() : string;

//1select
//2update
//3insert
//4delete
    abstract public function bindParam(int $crud) : string;

    abstract public function isNewRecord() : bool;

    public function select() : string 
    {
        return "SELECT * FROM {$this->tableName};";
    }
}
?>