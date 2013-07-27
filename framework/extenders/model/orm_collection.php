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
        
        public $each = array();
        private $class;
        private $first;
        
        public function __construct( $records, $class_name ) {
            
            if(gettype($records) !== "array") { return false; }
            
            $this->each = $records;
            $this->class = $class_name;

            return $this;
            
        }
        
        
        public static function build( $class_name, $data ) {

            return new self($data, $class_name);

        }

        public static function buildFromObjects( $data ) 
        {

            if(empty($data)) { return false; }

            $className = get_class($data[0]);
            $collection = array();

            foreach($data as $a) {
                array_push($collection, $a->toArray());
            }

            return self::build( $className, $collection );

        }
        
        
        /**
         * 
         * @param function $iterator - Iterator to loop through each model
         * @return \ArrayObject
         */
        public function each( $iterator = null ) {
            
            $class = $this->class;

            if( !is_null($iterator) && $this->isNotEmpty() ) {
                
                $ormObject = null;

                for( $i = 0, $j = count($this->each); $i < $j; $i++ ) {
                    
                    //Init object instance
                    $ormObject = new $class( null, $this->each[$i] );

                    //Passing object instance to iterator
                    $iterator( $ormObject );

                    //Removing object instance from memory
                    unset($ormObject);
                    $ormObject = null;
                    
                }

                return $this;
                
            } else {

                $returnArray = array();

                for($i = 0, $j = count($this->each); $i < $j; $i++) {
                    array_push( $returnArray, new $class(null, $this->each[$i]) );
                }

                return $returnArray;

            }
            
            
        }
        
        /**
         * 
         * @abstract Test to see if any models exist in array
         * 
         * @return Boolean
         */
        public function isNotEmpty() {
            return ( gettype($this->each) === "array" && $this->size() > 0 ) ? true : false;
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
            return ( $this->each !== false ) ? count( $this->each ) : 0;
        }
        
        
        /**
         * @abstract Fetch the first model in array
         * 
         * @return \Framework\Extenders\Model\ORM
         */
        public function first() {
            $class = $this->class;
            if(!isset($this->first) || is_null($this->first) || !$this->first->id) {
                $this->first = new $class( null, $this->each[0] );
            }
            
            return $this->first;
        }
        
        
        /**
         * @abstract Fetch the last model in array
         * 
         * @return \Framework\Extenders\Model\ORM
         */
        public function last() {
            $last = ($this->size() - 1);
            $class = $this->class;
            return ( isset($this->each[$last]['id']) && $this->each[$last]['id'] > 0) ? new $class(null, $this->each[$last]) : new $class(null, array());
        }
        
        
        /**
         * @abstract Take a chunk out of the objects array
         * 
         * @return \Framework\Extenders\Model\ORM_Collection
         */
        public function slice( $from, $to ) {
            $length = ( $from - $to );
            return new self(array_splice( $this->each, $from, $length ));
        }
        
        
        /**
         * @abstract Get rid of the first model in array
         * 
         * @return \Framework\Extenders\Model\ORM
         */
        public function shift() {
            return array_shift($this->each);
        }
        
        /**
         * @abstract Get rid of the last model in array
         * 
         * @return \Framework\Extenders\Model\ORM
         */
        public function pop() {
            return array_pop($this->each);
        }
        
        
        /**
         * @abstract Place another model object at front of array
         * 
         * @return \Framework\Extenders\Model\ORM
         */
        public function prepend( $data ) {
            
            array_unshift( $this->each, $model->toArray() );
            return $this;
            
        }
        
        
        /**
         * @abstract Attach another model object to the end of the array
         * 
         * @return \Framework\Extenders\Model\ORM
         */
        public function push( $model ) {
            
            array_push( $this->each, $model->toArray() );
            return $this;
            
        }
        
        
        /**
         * @abstract Concatenate all objects by a property
         * 
         * @return String
         */
        public function implode( $column, $glue ) {
            
            $implodeArray = array();
            
            for( $i = 0, $j = count($this->each); $i < $j; $i++ ) {
                array_push( $implodeArray, $this->each[$i][$column] );
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
            
            for( $i = 0, $j = count($this->each); $i < $j; $i++ ) {
                array_push( $implodeArray, $open . $this->each[ $i ][ $column ] . $close );
            }
            
            return implode( $glue, $implodeArray );
            
        }
        
        
        
        public function mass_assign( $property, $value ) {
            
            for($i = 0, $j = $this->size(); $i < $j; $i++) {
                
                $this->each[$i][$property] = $value;

            }
            
            return $this;
            
        }
        
        public function save() {

            $status = true;

            $this->each( function( $object ) {
                
                if( !$object->save() ) {

                    global $status;
                    $status = false;

                }

            } );
            
            return $status;
            
        }
        
        
        
        
        /**
         * @abstract Get the total of all objects based on common property
         * 
         * @return Float
         */
        public function sum( $column ) {
            
            $sum = 0;
            
            for($i = 0, $j = $this->size(); $i < $j; $i++) {
                $sum += $this->each[ $i ][ $column ];
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
        public function toXML( $avoid = array() ) {
            
            return \Framework\Objects\XML::collectionToXML($this, $avoid);
            
        }
        
        public function toArray( $avoid = array() )
        {
            
            return \Framework\Objects\JSON::collectionToJSON($this, $avoid)->getRawJSON();
            
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
         * @abstract Convert each object to a JSON object view
         * 
         * @return \Framework\Objects\XML
         */
        public function toJSON( $avoid = array() ) {
            
            return \Framework\Objects\JSON::collectionToJSON($this, $avoid);
            
        }
        
        /**
         * @abstract Search all object properties for a needle
         * 
         * @return \Framework\Objects\XML
         */
        public function has( $what, $column = "id" ) {
            
            for($i = 0, $j = $this->size(); $i < $j; $i++) {
                if( isset($this->each[ $i ][ $column ]) && $this->each[ $i ][ $column ] == $what) { return true; }
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
            \Libs\Rosborne\CFDump::dump( $this->each );
            return $this;
            
        }
        
        
        public function getTargetClass()
        {
            return $this->class;
        }
        
        
        
        
    }
    
    
}


