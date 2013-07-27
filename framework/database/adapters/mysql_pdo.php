<?php
namespace Framework\Database\Adapters {
    
    
    /*
     * Mysql_PDO
     * 
     * @description - Allows you to format all communication between
     * model and datase via PDO communication format.
     * 
     * @package    Framework.Database.Adapters
     * @author     Justin Workman <jworkmandevelopment@gmail.com>
    */
    
    class Mysql_PDO extends \Config\Database implements \Framework\Database\Interfaces\iAdapter {
        
        
        private $user;
        private $host;
        private $name;
        private $pass;
        private $dsn;
        
        /**
         *
         * @var \PDO
         */
        protected $connection;

        
        public function __construct( $dsn = "", $user = "root", $pass = "" ) {
            
            parent::__construct();

            global $connection;
            
            if($connection !== false) { 
                $this->connection = $connection;
                return true; 
            }
            
            if( strlen($dsn) > 0 ) { 
                $this->user = $user;
                $this->pass = $pass;
                $this->dsn = $dsn; 
                $connection = new \PDO( $dsn, $user, $pass );
                $this->connection = $connection;
            }
            
        }
        
        public function fetch( $sql, $arguments = array() ) { 
            
            $query = $this->connection->prepare( $sql );
            $result = $query->execute( $arguments );
            if( $result ) { return $query->fetchAll( \PDO::FETCH_ASSOC ); }
            return array();
            
        }
        
        public function query( $sql, $arguments = array() ) { 
            
            return $this->connection->prepare( $sql )->execute( $arguments );
            
        }
        
        public function lastInsertedID() {
            return $this->connection->lastInsertId();
        }
        
        public function _setHost( $host ) {
            $this->host = $host;
        }
        
        public function _setPassword( $pass ) {
            $this->pass = $pass;
        }
        
        public function _setUser( $user ) {
            $this->user = $user;
        }
        
        public function _setName( $name ) {
            $this->name = $name;
        }
        
        public function _connect() {

            global $connection;
            
            try {
            
                if($connection !== false) { 
                    $this->connection = $connection;
                } else {
                    $connection = new \PDO($this->_buildDSN(), $this->user, $this->pass);
                    $this->connection = $connection;
                }
                
                return true;
                
            } catch (Exception $e) {
                
                \Framework\Objects\Error::throwError($e);
                return false;
                
            }
            
        }

        private function _buildDSN() 
        {
            
            return "mysql:host=".$this->host.";dbname=".$this->name;

        }
        
    }
    
    
}

