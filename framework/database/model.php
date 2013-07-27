<?php
namespace Framework\Database {
    
    $adapter = new \stdClass();
    $adapterSet = false;
    
    /**
    * Mysql_PDO
    * 
    * @description - Extender for any model ORM or Gateway
    * 
    * @package    Framework.Database.Adapters
    * @author     Justin Workman <jworkmandevelopment@gmail.com>
    */
    class Model extends \Config\Database {
        
        
        /**
         *
         * @var \Framework\Database\Adapters\Mysql_PDO
         */
        private $adapter;
        

        public function __construct() 
        {
            
            parent::__construct();
            //Setup the DB
            $this->adapter = $this->setAdapter( $this->getConfigKey( "adapter" ) );
            
        }
        
        public function setAdapter( $adapterStr ) 
        {
            
            global $adapter, $adapterSet;
            
            if( $adapterSet ) {
                $this->adapter = $adapter;
                return $this->adapter;
            }
            
            $adapterString = "\\Framework\\Database\\Adapters\\".$adapterStr;
            $this->adapter = new $adapterString;
            //Setup adapter connection
            $this->adapter->_setHost( $this->getConfigKey( "host" ) );
            $this->adapter->_setUser( $this->getConfigKey( "user" ) );
            $this->adapter->_setPassword( $this->getConfigKey( "pass" ) );
            $this->adapter->_setName( $this->getConfigKey( "name" ) );
            
            //Connect to the DB
            $this->adapter->_connect();
            
            $adapter = $this->adapter;
            $adapterSet = true;
            return $this->adapter;
        }
        
        public function getAdapter()
        {
            return $this->adapter;
        }

        public static function getStaticAdapter()
        {
            $model = new self();
            return $model->getAdapter();
        }
    
    }
    
    
}




