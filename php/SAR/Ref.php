<?php

class Ref extends ReflectionClass
{
    private $obj;
    private ReflectionProperty $property;
    private ReflectionMethod $method;

    public function __construct($obj)
    {
        parent::__construct($obj);
        $this->obj = $obj;
    }

    public function copy() 
    {
        $obj = $this->newInstanceArgs();
        $ref = new Ref($obj);

        foreach ($ref->getProperties() as $prop) 
        {
            $ref->access($prop->getName());            
            $this->access($prop->getName());
            $ref->setValue($this->getValue());
        }

        return $obj;
    }

    public function access(string $property) 
    {
        $this->property = $this->getProperty($property);
        $this->property->setAccessible(true);        
    }

    public function getValue() 
    {
        return $this->property->getValue($this->obj);
    }

    public function setValue($value) 
    {
       $this->property->setValue($this->obj, $value);
    }

    public function invoke(string $name, ...$vars) 
    {
        $this->method = $this->getMethod($name);

        if (is_string($this->obj)==1) 
        {
            $this->obj = $this->newInstanceArgs();
        }
        return $this->method->invoke($this->obj, ...$vars);
    }

    public function findProperty(string $propName) : string
    {
        $array = $this->getProperties();
        foreach($array as $prop) 
        {
            $found = strpos(strtolower(trim($prop->getName())), strtolower(trim($propName)));
            if ($found>=0) 
            {
                $this->property = $prop;
                $this->property->setAccessible(true);        
                return $prop->getName();
            }
        }
        return "NOT FOUND";
    }

    public static function correctFieldName($str) : string
    {
        return str_replace("_", "", $str);
    }

    public function getPropertyName() : string 
    {
        return $this->property->getName();
    }

    public function getPK() : string 
    {
        return str_replace('pk', '', $this->findProperty('pk'));
    }

    public function getFields() : Array
    {
        $fields = array();
        foreach($this->getProperties() as $prop) 
        {
            $pattern = "/_/";
            $found = preg_match($pattern, $prop->getName());
            if ($found==1) 
                array_push($fields,$prop);
        }
        return $fields;
    }

    public function getFKs() : Array
    {
        $fields = array();
        foreach($this->getProperties() as $prop) 
        {
            $pattern = "/fk/";
            $found = preg_match($pattern, $prop->getName());
            if ($found==1) 
                array_push($fields,$prop);
        }
        return $fields;
    }
}
?>