<?php

class Galaxy extends \Framework\Extenders\Model\ORM {
    
    public $_table = "galaxies";
    
    /*public $_propertyFilters = array(
        "name" => PROPERTY_FILTER_NOT_PUBLIC,
        "size" => "validateSize"
    );*/
    
    public function Stars()
    {
        return $this->getHasMany( new Star() );
    }
    
}


