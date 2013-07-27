<?php

namespace Framework\Objects {
    
    class XML {

        private $_xml;
        
        public function __construct( $xml = "" ) {
            
            $this->_xml = $xml;
            
        }
        
        public function getRawXML()
        {
            return $this->_xml;
        }
        
        
        /**
         * 
         * @param \Framework\Extenders\Model\ORM_Collection $ormCollection - ORM collection to turn to XML
         * @return \Framework\Objects\XML
         */
        public static function collectionToXML( $ormCollection, $avoid = array() )
        {
            
            
            $className = strtolower($ormCollection->getTargetClass());
            $xml = "";
            $xml .= "<".plurilize($className).">";
            
                foreach( $ormCollection->each as $model ) {
                    
                    $xml .= "<".singular($className).">";
                    
                    foreach( $model as $key => $value ) 
                    {
                        if(in_array($key, $avoid)) { continue; }
                        $xml .= "<".$key.">";
                            $xml .= $value;
                        $xml .= "</".$key.">";
                    }
                    
                    $xml .= "</".singular($className).">";
                    
                }
            
            $xml .= "</".plurilize($className).">";
            
            return new self($xml);
            
        }
        
        /**
         * 
         * @param \Framework\Extenders\Model\ORM $model - ORM model to turn to XML
         * @return \Framework\Objects\XML
         */
        public static function modelToXML( $model, $avoid = array() )
        {
            
            $className = strtolower(get_class($model));
            $xml = "";
            $xml .= "<".singular($className).">";
                foreach($model->toArray($avoid) as $key => $value) { 
                    if(in_array($key, $avoid)) { continue; }
                    $xml .= "<".$key.">";
                        $xml .= $value;
                    $xml .= "</".$key.">";
                }
            $xml .= "</".singular($className).">";
            
            return new self($xml);
            
        }
        
        
    }

}
