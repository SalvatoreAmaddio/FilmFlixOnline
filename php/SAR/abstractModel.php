<?php
abstract class AbstractModel 
{
    public string $tableName;

    public abstract static function readRow(array $row) : AbstractModel;

    public abstract static function returnNew() : AbstractModel;
    public function select() : string 
    {
        return "SELECT * FROM {$this->tableName};";
    }
}
?>