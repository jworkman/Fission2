<?php

namespace Framework\Extenders\Model {
    
    $objectCount = 0;
    
    
    /*
    * ORM
    * 
    * @description - Extender for ORM functionality
    * 
    * @package    Framework.Extenders.Model
    * @author     Justin Workman <jworkmandevelopment@gmail.com>
    */
    class ORM extends \Framework\Database\Model {
        
        private $tableName;
        private $sql = "";
        private $params = array();
        private $_tableSchema = array();
        private $_tableSchemaChache = array();
        
        
        public function __construct( $id = null, $data = null ) {

            //Construct the DB connection
            parent::__construct();

            global $objectCount;
            $objectCount++;
            
            //Get the target table
            $this->tableName = $this->_getTableName();
            
            if( !is_null($data) ) { $this->_build($data); }
            
            if( !is_null( $id ) ) { 
                
                $this->_build( 
                        $this->getAdapter()->fetch( 
                                "SELECT * FROM ".$this->_getFullTarget()." WHERE id = :id", 
                                array(":id" => $id)
                        ) 
                );
                
            }
            
            return $this;
            
        }
        
        private function _getSchema( $avoid = array() ) {
            
            if( !empty($this->_tableSchemaChache) && count($this->_tableSchemaChache) > 0 ) { 
                
                $this->_tableSchema = array();

                foreach($this->_tableSchemaChache as $col) {
                    
                    if(in_array($col["Field"], $avoid)) { continue; }
                    
                    array_push($this->_tableSchema, $col["Field"]);
                    
                }
                
                return $this->_tableSchema; 
                
            }
            
            $results = $this->getAdapter()->fetch( "SHOW COLUMNS FROM ".$this->_getFullTarget() );
            $this->_tableSchemaChache = $results;
            if( count($results) > 0 ) {
                
                foreach($results as $col) {
                    
                    if(in_array($col["Field"], $avoid)) { continue; }
                    
                    array_push($this->_tableSchema, $col["Field"]);
                    
                }
                
                return $this->_tableSchema;
            }
            
            throw new \Exception("Table schema for table ".$this->_getFullTarget()." could not be found or loaded in!");
        
        }
        
        
        public function _build( $data ) {
            
            if( gettype($data) !== "array" ) { return false; }
            
            if( count($data) == 1 && gettype($data[0]) == "array" ) { $data = $data[0]; }
            
            foreach( $data as $key => $value ) {
                $this->$key = $value;
            }
            
            return $this;
            
        }
        
        
        public function _getTableName() {
            return (isset($this->_table) && empty($this->_table)) ? strtolower( get_class() ) : $this->_table;
        }
        
        public function _getFullTarget() {
            return $this->getConfigKey( "name" ).".".$this->_getTableName();
        }
        
        
        
        
        
        
        //Public Static API methods for fetching
        
        /**
         * 
         * @param int $id
         * @return \Framework\Extenders\Model\ORM
         */
        public static function find( $id ) {
            
            $class = get_called_class();
            return new $class( $id );
            
        }
        
        /**
         * 
         * @param string $sql
         * @param array $params Bindable parameters for SQL
         * @return \Framework\Extenders\Model\ORM
         */
        public static function find_by( $sql, $params = array() ) {
            
            $orm = new self();
            $results = $orm->getAdapter()->fetch( $sql, $params );
            $class = get_called_class();

            if(count($results) > 0 && !empty($results[0])) {
                
                return \Framework\Extenders\Model\ORM_Collection::build( $class, $results );

            } else {
                //throw new \Exception( "find_one_by() couldn't find any results. Make sure you specify a database target and table." );
                return new \Framework\Extenders\Model\ORM_Collection( array() );
            }
            
            
        }
        
        /**
         * 
         * @param string $sql
         * @param array $params Bindable parameters for SQL
         * @return \Framework\Extenders\Model\ORM
         */
        public static function find_one_by( $sql, $params = array() ) {
            
            $orm = new self();
            $results = $orm->getAdapter()->fetch( $sql, $params );
            $class = get_called_class();
            if(count($results) > 0 && !empty($results[0])) {
                return new $class( null, $results[0] );
            } else {
                //throw new \Exception( "find_one_by() couldn't find any results." );
                return new $class();
            }
            
        }
        
        /**
         * 
         * @return \Framework\Extenders\Model\ORM
         */
        public static function first() {
            $className = get_called_class();
            $class = new $className();
            return self::find_one_by( "SELECT * FROM ".$class->_getFullTarget()." ORDER BY id LIMIT 1" );
        }
        
        
        /**
         * 
         * @return \Framework\Extenders\Model\ORM
         */
        public static function last() {
            
            $className = get_called_class();
            $class = new $className();
            return self::find_one_by( "SELECT * FROM ".$class->_getFullTarget()." ORDER BY id DESC LIMIT 1" );
            
        }
        
        
        /**
         * 
         * @return \Framework\Extenders\Model\ORM
         */
        public static function all() {
            
            $calledClass = get_called_class();
            $model = new \Framework\Database\Model();
            $class = new $calledClass();
            $results = $model->getAdapter()->fetch( "SELECT * FROM " . $class->_getFullTarget() );
            return \Framework\Extenders\Model\ORM_Collection::build( $calledClass, $results );
            
        }
        
        
        //Public chain methods for fetching
        
        
        /**
         * 
         * @param string $what - Column to search by
         * @param string $operator - Comparison operator to use
         * @param string $value - The value to compaire to
         * @return \Framework\Extenders\Model\ORM
         */
        public static function where( $what, $operator, $value ) {
            
            $calledClass = get_called_class();
            $newInstance = new $calledClass();
            $newInstance->_setSQL("SELECT * FROM ".$newInstance->_getFullTarget()." WHERE ".$what." = ".$newInstance->_bind( $value ));
            return $newInstance;
            
        }
        
        public function _setSQL( $sql ) {
            $this->sql = $sql;
        }
        
        
        
        /**
         * 
         * @param string $what - Column to search by
         * @param string $operator - Comparison operator to use
         * @param string $value - The value to compaire to
         * @return \Framework\Extenders\Model\ORM
         */
        public function and_where( $what, $operator, $value ) {
            
            $this->sql .= " AND ".$what." ".$operator." ".$this->_bind( $value );
            return $this;
            
        }
        
        
        /**
         * 
         * @param string $what - Column to search by
         * @param string $operator - Comparison operator to use
         * @param string $value - The value to compaire to
         * @return \Framework\Extenders\Model\ORM
         */
        public function or_where( $what, $operator, $value ) {
            
            $this->sql .= " OR ".$what." = ".$this->_bind( $value );
            return $this;
            
        }


        public function order_by( $what ) 
        {
            $this->sql .= " ORDER BY ".$what;
            return $this;
        }
        
        
        /**
         * 
         * @param int $limit - Number of records to limit to
         * @return \Framework\Extenders\Model\ORM
         */
        public function limit( $limit = 10 ) {
            
            $this->sql .= " LIMIT ".$this->_bind( (int)$limit );
            return $this;
            
        }
        
        
        /**
         * 
         * @param int $offset - Offset slot
         * @return \Framework\Extenders\Model\ORM
         */
        public function offset( $offset = 0 ) {
            
            $this->sql .= " OFFSET ".$this->_bind( (int)$offset );
            return $this;
            
        }


        public function count( $selector = "*" )
        {
            $sql = str_replace($selector, "COUNT(".$selector.") AS sum", $this->sql);
            $results = $this->getAdapter()->fetch($sql, $this->params);
            return (int)$results[0]["sum"];
        }
        
        
        /**
         * 
         * @return \Framework\Extenders\Model\ORM_Collection
         */
        public function get() {
            
            $class = get_class($this);
            $results = $this->getAdapter()->fetch($this->sql, $this->params);
            return \Framework\Extenders\Model\ORM_Collection::build($class, $results);
            
        }
        
        
        //Public methods for storage manipulation
        
        public function save() {
            
            
            
            if( isset($this->id) && !is_null($this->id) && $this->id > 0) {
                
                $params = $this->dataToUpdateParams( $this->_getSchema(array("id")) );
                array_push($params["values"], $this->id);
                $this->sql = "UPDATE ".$this->_getFullTarget()." SET ".implode(", ", $params["declarations"])." WHERE id = ?";
                return ($this->getAdapter()->query( $this->sql, $params["values"] )) ? true : false;
                
            } else {
                
                //If save needed...
                $params = $this->dataToInsertParams( $this->_getSchema(array("id")) );
                $this->sql = "INSERT INTO ".$this->_getFullTarget()." (".$params["declarations"].") VALUES (".implode(",", $params["parameters"]).")";
                $success = $this->getAdapter()->query( $this->sql, $params["values"] );
                
                if($success) {
                    $this->id = $this->getAdapter()->lastInsertedID();
                }

                return ( $success ) ? true : false;
                
            }
            
        }


        public function destroy()
        {
            $this->sql = "DELETE FROM ".$this->_getFullTarget()." WHERE id = ".$this->id;
            return $this->getAdapter()->query( $this->sql, array() );
        }
        
        private function dataToInsertParams( $schema ) {
            
            $return = array(
                "declarations" => implode( ",", $schema ),
                "parameters" => array(),
                "values" => array()
            );
            
            foreach($schema as $col) {
                array_push($return["parameters"], "?");
                array_push($return["values"], $this->$col);
            }
            
            return $return;
            
        }
        
        private function dataToUpdateParams( $schema ) {
            
            $return = array(
                "declarations" => array(),
                "values" => array()
            );
            
            foreach($schema as $col) {
                array_push($return["declarations"], $col." = ?");
                array_push($return["values"], $this->$col);
            }
            
            return $return;
            
        }
        
        
        
        
        
        
        //Public response methods
        
        public function toArray( $avoid = array() ) {
            
            $returnArray = array();
            
            foreach($this->_getSchema($avoid) as $key => $value) {
                
                if(in_array($value, $avoid)) { continue; }
                
                $returnArray[$value] = $this->$value;
                
            }
            
            return $returnArray;
            
        }


        public function toForm( $formName = null ) {
            
            if(is_null($formName)) {
                $objectName = strtolower(get_class($this));
            } else {
                $objectName = $formName;
            }
            require APPLICATION_PATH."html/forms/".$objectName.".phtml";
            
        }
        
        public function toView( $viewName = null, $data = null ) 
        {
            
            if(is_null($viewName)) {
                $objectName = get_class($this);
            } else {
                $objectName = $viewName;
            }
            require APPLICATION_PATH."html/objects/".ucfirst($objectName).".php";
            
        }
        
        public function toXML( $avoid = array() ) {
            
            return \Framework\Objects\XML::modelToXML($this, $avoid);
            
        }
        
        public function toJSON( $avoid = array() ) {
            
            return \Framework\Objects\JSON::modelToJSON($this, $avoid);
            
        }
        
        
        
        //Public internal methods
        
        public function _bind( $value ) {
            
            $key = ":param".count($this->params);
            $this->params[ $key ] = $value;
            return $key;
            
        }
        
        
        //Public relationship methods
        
        /**
         * 
         * @param ORM $model - Model to relation
         * @return \Framework\Extenders\Model\ORM
         */
        protected function getHasMany( $model ) {
            
            return $model->where( strtolower( get_class($this)."_id" ), "=", $this->id )->get();
            
        }
        
        /**
         * 
         * @param ORM $model - Model to relation
         * @return \Framework\Extenders\Model\ORM
         */
        protected function getBelongsTo( $model ) {
            
            $local_key = strtolower( get_class($model) )."_id";
            $class = get_class($model);
            return new $class( $this->$local_key );
            
        }
        
        
        
        
        //View based methods
        
        
        /**
         * 
         * @return Boolean
         */
        public function examine() {
            
            \Libs\Rosborne\CFDump::dump( $this );
            
        }


        public function exists( $key = "id" )
        {

            return ( isset($this->$key) && !is_null($this->$key) && !empty($this->$key) ) ? true : false;

        }
        
        
        public function populate( $formData, $save = true )
        {
            
            foreach($formData as $key => $value)
            {
                $this->$key = \Framework\Extenders\Model\PropertyFilter::validate($this, $key, $value);
            }
            
            return ($save) ? $this->save() : true;
            
        }
        
        public static function init( $formData = null )
        {
            
            $model = new self();
            if( !is_null($formData) ) {
                $model->populate($formData, false);
            } 
            
            return $model;
            
        }
        
    }
    
    
}