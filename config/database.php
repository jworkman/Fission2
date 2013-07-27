<?php
namespace Config {
    
    
    class Database {
        
        private $_user      = "root";
        private $_pass      = "";
        private $_host      = "127.0.0.1";
        private $_name      = "fission_dev";
        private $_adapter   = "mysql_pdo";
        
        
        protected function getConfigKey( $key ) 
        {
            $str = "_".$key;
            return $this->$str;
        }
        
        
        private function _production()
        {
            $this->_user    = "root";
            $this->_pass    = "";
            $this->_host    = "127.0.0.1";
            $this->_name    = "fission_dev";
            $this->_adapter = "mysql_pdo";
        }
        
        private function _staging()
        {
            $this->_user    = "root";
            $this->_pass    = "";
            $this->_host    = "127.0.0.1";
            $this->_name    = "fission_dev";
            $this->_adapter = "mysql_pdo";
        }
        
        private function _development()
        {
            $this->_user    = "root";
            $this->_pass    = "";
            $this->_host    = "127.0.0.1";
            $this->_name    = "fission_dev";
            $this->_adapter = "mysql_pdo";
        }
        

        public function __construct() 
        {
            switch (APPLICATION_ENV) {
                
                case 'production':
                case 'prod':
                case 'live':
                    $this->_production();
                    break;
                
                case 'qa':
                case 'staging':
                case 'testing':
                case 'stage':
                    $this->_staging();
                    break;
                
                case 'local':
                case 'dev':
                case 'development':
                    $this->_development();
                    break;
                
                
                default:
                    $this->_production();
                    break;
                    
            }
        }
    
    
    }
    
    
}


