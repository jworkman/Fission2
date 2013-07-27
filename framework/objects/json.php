<?php

namespace Framework\Objects {
    
    class JSON {
        
        private $_json;

        public function __construct( $json ) {
            $this->_json = $json;
        }
        
        public function getRawJSON() {
            return json_encode($this->_json);
        }
        
        
        /**
         * 
         * @param \Framework\Extenders\Model\ORM $model - ORM model to turn to JSON
         * @return \Framework\Objects\JSON
         */
        public static function modelToJSON( $model, $avoid = array() ) {
            
            return new self( $model->toArray($avoid) );
            
        }
        
        
        /**
         * 
         * @param \Framework\Extenders\Model\ORM_Collection $collection - ORM collection to turn to JSON
         * @return \Framework\Objects\JSON
         */
        public static function collectionToJSON( $collection, $avoid = array() ) {
            
            if(count($avoid) > 0) {
                
                $returnJSON = array();
                $jsonCache;
                foreach($collection->each as $model) {
                    
                    /*$jsonCache = array();
                    
                    foreach($model as $key => $value) {
                        
                        if(in_array($key, $avoid)) { continue; }
                        $jsonCache[$key] = $value;
                        
                    }
                    
                    array_push($returnJSON, $jsonCache);*/
                    
                    array_push($returnJSON, array_flip(array_filter(array_flip($model), function ($key) use ($avoid)
                    {
                        return !in_array($key, $avoid);
                    })));
                    
                    
                }
                
                return new self( $returnJSON );
                
            } 
            
            return new self( $collection->each );
            
        }
        
    }

}