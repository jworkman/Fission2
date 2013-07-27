<?php
namespace Framework\Extenders\Model {
    
    
    
    /*
    * ORM_Collection
    * 
    * @description - Array collection of ORM records with attached methods
    * 
    * @package    Framework.Database.Adapters
    * @author     Justin Workman <jworkmandevelopment@gmail.com>
    */
    class ORM_Collection {
        
        public $data = array();
        
        public function __construct( $records ) {
            
            if(gettype($records) !== "array") { return false; }
            
            for( $i = 0; $i < count($records); $i++ ) {
                
                array_push( $this->data, $records[$i] );
                
            }
            
        }
        
        
        public static function build( $class_name, $data ) {
            
            $return_array = array();
            
            for($i = 0, $j = count($data); $i < $j; $i++) {
                
                array_push( $return_array, new $class_name( null, $data[$i] ) );
                
            }
            
            return new self( $return_array );
            
        }
        
        
        /**
         * 
         * @param function $iterator - Iterator to loop through each model
         * @return \ArrayObject
         */
        public function each( $iterator = null ) {
            
            if( !is_null($iterator) && $this->isNotEmpty() ) {
                
                foreach($this->data as $data) {
                    
                    $iterator( $data );
                    
                }
                
            }
            
            return ( $this->isNotEmpty() ) ? $this->data : array();
            
        }
        
        /**
         * 
         * @abstract Test to see if any models exist in array
         * 
         * @return Boolean
         */
        public function isNotEmpty() {
            return ( gettype($this->data) === "array" && $this->size() > 0 ) ? true : false;
        }
        
        /**
         * 
         * @abstract Test to see if any models exist in array
         * 
         * @return Boolean
         */
        public function any() {
            return $this->isNotEmpty();
        }
        
        /**
         * @abstract Get the amount of objects returned
         * 
         * @return \Framework\Extenders\Model\ORM
         */
        public function size() {
            return ( $this->data !== false ) ? count( $this->data ) : 0;
        }
        
        
        /**
         * @abstract Fetch the first model in array
         * 
         * @return \Framework\Extenders\Model\ORM
         */
        public function first() {
            return ( isset($this->data[0]->id) && $this->data[0]->id > 0) ? $this->data[0] : array();
        }
        
        
        /**
         * @abstract Fetch the last model in array
         * 
         * @return \Framework\Extenders\Model\ORM
         */
        public function last() {
            $last = ($this->size() - 1);
            return ( isset($this->data[$last]->id) && $this->data[$last]->id > 0) ? $this->data[$last] : array();
        }
        
        
        /**
         * @abstract Take a chunk out of the objects array
         * 
         * @return \Framework\Extenders\Model\ORM_Collection
         */
        public function slice( $from, $to ) {
            $length = ( $from - $to );
            return new self(array_splice( $this->data, $from, $length ));
        }
        
        
        /**
         * @abstract Get rid of the first model in array
         * 
         * @return \Framework\Extenders\Model\ORM
         */
        public function shift() {
            return array_shift($this->data);
        }
        
        /**
         * @abstract Get rid of the last model in array
         * 
         * @return \Framework\Extenders\Model\ORM
         */
        public function pop() {
            return array_pop($this->data);
        }
        
        
        /**
         * @abstract Place another model object at front of array
         * 
         * @return \Framework\Extenders\Model\ORM
         */
        public function prepend( $model ) {
            
            array_unshift( $this->data, $model );
            return $this;
            
        }
        
        
        /**
         * @abstract Attach another model object to the end of the array
         * 
         * @return \Framework\Extenders\Model\ORM
         */
        public function push( $model ) {
            
            array_push( $this->data, $model );
            return $this;
            
        }
        
        
        /**
         * @abstract Concatenate all objects by a property
         * 
         * @return String
         */
        public function implode( $column, $glue ) {
            
            $implodeArray = array();
            
            foreach( $this->each() as $data ) {
                array_push( $implodeArray, $data->$column );
            }
            
            return implode( $glue, $implodeArray );
            
        }
        
        
        /**
         * @abstract Concatenate all objects into one string with tags wrapped arround each property
         * 
         * @return \Framework\Extenders\Model\ORM
         */
        public function implode_tag( $column, $open, $close ) {
            
            $implodeArray = array();
            
            foreach( $this->each() as $data ) {
                array_push( $implodeArray, $open.$data->$column.$close );
            }
            
            return implode( $glue, $implodeArray );
            
        }
        
        
        
        public function mass_assign( $property, $value ) {
            
            for($i = 0, $j = count($this->data); $i < $j; $i++) {
                
                $this->data[$i]->$property = $value;
                
            }
            
            return $this;
            
        }
        
        public function save() {
            
            foreach( $this->each() as $model ) {
                if(!$model->save()) { return false; }
            }
            
            return true;
            
        }
        
        
        
        
        /**
         * @abstract Get the total of all objects based on common property
         * 
         * @return Float
         */
        public function sum( $column ) {
            
            $sum = 0;
            
            foreach( $this->each() as $data ) {
                $sum += $data->$column;
            }
            
            return $sum;
            
        }
        
        
        /**
         * @abstract Get the average amount of a common property
         * 
         * @return Float
         */
        public function average( $column ) {
            
            return ( $this->sum( $column ) / $this->size() );
            
        }
        
        
        /**
         * @abstract Convert each object to a XML object view
         * 
         * @return \Framework\Objects\XML
         */
        public function toXML() {
            
            $xmlArray = array();
            
            foreach( $this->each() as $data ) {
                array_push( $xmlArray, $data->toXML() );
            }
            
            return $xmlArray;
            
        }
        
        
        /**
         * @abstract Convert each object to a JSON object view
         * 
         * @return \Framework\Objects\XML
         */
        public function toJSON() {
            
            $jsonArray = array();
            
            foreach( $this->each() as $data ) {
                array_push( $jsonArray, $data->toJSON() );
            }
            
            return $jsonArray;
            
        }

        /**
         * @abstract Convert each object to an array
         * 
         * @return \Framework\Objects\XML
         */
        public function toArray() {
            
            $return = array();
            
            foreach( $this->each() as $data ) {
                array_push( $return, $data->toArray() );
            }
            
            return $return;
            
        }


        public function toView( $viewName = null, $data = null ) 
        {

            if(is_null($viewName)) {
                $objectName = get_class($this);
            } else {
                $objectName = $viewName;
            }
            ini_set('display_errors',1); error_reporting(E_ALL);
            require APPLICATION_PATH."/views/scripts/objects/".ucfirst($objectName).".php";
            
        }

        
        /**
         * @abstract Search all object properties for a needle
         * 
         * @return \Framework\Objects\XML
         */
        public function has( $what, $column = "id" ) {
            
            foreach( $this->each() as $data ) {
                if( $data->$column == $what ) { return true; }
            }
            
            return false;
            
        }
        
        /**
         * @abstract Dump all variable data in a friendly GUI
         * 
         * @return \Framework\Extenders\Model\ORM_Collection
         */
        public function examine() {
            
            //TODO: Hook up an inspection tool
            \Libs\Rosborne\CFDump::dump( $this->data );
            return $this;
            
        }
        
        
        
        
    }
    
    
}


