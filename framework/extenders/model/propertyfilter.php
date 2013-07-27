<?php
namespace Framework\Extenders\Model {
    
    class PropertyFilter {
        
        public static function validate( $model, $key, $value ) 
        {
            
            if( 
                    isset($model->_propertyFilters) && 
                    !empty($model->_propertyFilters) && 
                    gettype($model->_propertyFilters) == "array" && 
                    count($model->_propertyFilters) > 0
            ) {
                
                if(
                    isset($model->_propertyFilters[$key]) && 
                    !empty($model->_propertyFilters[$key]) && 
                    gettype($model->_propertyFilters[$key]) != "integer" && 
                    gettype($model->_propertyFilters[$key]) != "boolean" && 
                    gettype($model->_propertyFilters[$key]) != "double" && 
                    method_exists($model, $model->_propertyFilters[$key]) 
                ) {
                    $filter = $model->_propertyFilters[$key];
                    if(!$model->$filter( $value )) { return $model->$key; }
                } 
                
                if(
                    isset($model->_propertyFilters[$key]) && 
                    !empty($model->_propertyFilters[$key]) && 
                    gettype($model->_propertyFilters[$key]) == "string" && 
                    $model->_propertyFilters[$key] == PROPERTY_FILTER_NOT_PUBLIC 
                ) {
                    return $model->$key;
                } 
                
            }
            
            return $value;
            
        }
        
    }
    
}