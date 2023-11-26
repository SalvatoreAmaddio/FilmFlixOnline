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
        $this->method->invoke($this->obj, ...$vars);
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
}
?>