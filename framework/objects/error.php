<?php
namespace Framework\Objects {
    
    
    class Error {
        
        public static function throwError( Exception $error ) {
            
            echo $error->getMessage();
            
        }
        
    }
    
    
}


