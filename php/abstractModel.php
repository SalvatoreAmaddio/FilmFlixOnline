<?php
abstract class AbstractModel 
{
    public string $tableName;

    public function select() : string 
    {
        return "SELECT * FROM {$this->tableName};";
    }
}
?>