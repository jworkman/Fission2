<?php
namespace Framework\Extenders\Controller {
    
    class Base {
        
        protected $params = array();
        private $_http_verb;
        
        protected $template;
        protected $view;
        
        private $_controllerName;
        private $_actionName;
        private $_view;
        private $_template;
        private $_templateDisabled = false;
        private $_return_type;

        public function __construct( $params = array(), $controller = "index", $action = "index", $verb = "GET", $return_type = "html", $additionalParams = array() ) {
            
            $this->params = $params;
            $this->_http_verb = $verb;
            $this->setController($controller);
            $this->setAction($action);
            $this->setView( $action );
            $this->setTemplate();
            $this->setAdditionalParams( $additionalParams );
            $this->_return_type = $return_type;
            $this->addToParams( $_POST );
            $this->addToParams( $_GET );
            
            if( $this->_http_verb == "GET" && method_exists($this, "get_".$action) ) {
                $action = "get_".$action;
            } elseif( $this->_http_verb == "POST" && method_exists($this, "post_".$action) ) {
                $action = "post_".$action;
            }
            
            if( isset($this->before) && gettype($this->before) == "array" ) {
                
                foreach($this->before as $key => $value) {
                    if(in_array($action, $value)) {
                        $this->$key();
                    }
                }
                
            } elseif( isset($this->before) && gettype($this->before) == "string" ) {
                
                $beforeStr = $this->before;
                $this->$beforeStr();
                
            }
            
            $this->$action();
            
            if( isset($this->after) && gettype($this->after) == "array" ) {
                
                foreach($this->after as $key => $value) {
                    if(in_array($action, $value)) {
                        $this->$key();
                    }
                }
                
            } elseif( isset($this->after) && gettype($this->after) == "string" ) {
                
                $afterStr = $this->after;
                $this->$afterStr();
                
            }
            
        }
        
        private function addToParams( $array )
        {
            
            foreach(array_keys($array) as $key) 
            {
                if( is_numeric($key) ) { continue; } 
                $this->params[$key] = $array[$key];
            }
            
        }
        
        private function inject()
        {
            
            if( strstr($this->_view, "/") !== false ) {
                require_once ROOT.APPLICATION.'html/'.VIEWS.$this->_view.'.phtml';
                return true;
            }
            
            require_once ROOT.APPLICATION.'html/'.VIEWS.strtolower($this->_controllerName)."/".strtolower($this->_view).".phtml";
            return true;
            
        }
        
        public function respondWithView( $view = null, $template = null )
        {
            
            //First lets check if $view is a model
            if( $view instanceof \Framework\Extenders\Model\ORM ) {
                //Model
                $view = $this->_actionName;
            } elseif( $view instanceof \Framework\Extenders\Model\ORM_Collection ) {
                //Collection
                $view = $this->_actionName;
            }
            
            
            if(!is_null($template)) {
                if($template === true) { $this->enableTemplate(); }
                if($template === false) { $this->disableTemplate(); }
                if($template !== true && $template !== false) { $this->setTemplate($template); }
            }
            
            if(!is_null($view)) { $this->setView( $view ); }
            
            if( !$this->_templateDisabled ) {
                require_once ROOT.APPLICATION.'html/templates/'.$this->_template.'.php';
            } else {
                $this->inject();
            }
            
            return $this;
        }
        
        public function respondWithXML( $xml, $headers = true )
        {
            
            if($headers) { header("Content-type: text/xml"); }
            echo "<?xml version='1.0' encoding='ISO-8859-1'?>";
            
            
            if( $xml instanceof \Framework\Objects\XML ) {
                //If the object is already in xml object form
                echo $xml->getRawXML();
            } elseif(gettype($xml) == "string" ) {
                //If the object is in a string form
                echo $xml;
            } elseif( $xml instanceof \Framework\Extenders\Model\ORM ) {
                //If the object is a single ORM model
                echo \Framework\Objects\XML::modelToXML($xml)->getRawXML();
            } elseif( $xml instanceof \Framework\Extenders\Model\ORM_Collection ) {
                echo \Framework\Objects\XML::collectionToXML($xml)->getRawXML();
            }
            
            return $this;
        }
        
        public function respondWithJSON( $json, $headers = true )
        {
            
            if($headers) { 
                header('Cache-Control: no-cache, must-revalidate');
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Content-type: application/json');
            }
            
            if( $json instanceof \Framework\Objects\JSON ) {
                //If the object is already in xml object form
                echo $json->getRawJSON();
            } elseif(gettype($json) == "string" ) {
                //If the object is in a string form
                echo $json;
            } elseif( $json instanceof \Framework\Extenders\Model\ORM ) {
                //If the object is a single ORM model
                echo \Framework\Objects\JSON::modelToJSON($json)->getRawJSON();
            } elseif( $json instanceof \Framework\Extenders\Model\ORM_Collection ) {
                echo \Framework\Objects\JSON::collectionToJSON($json)->getRawJSON();
            } elseif( gettype($json) == "array" ) {
                echo json_encode($json);
            }
            
            return $this;
        }
        
        public function partial( $partial, $properties = array() )
        {
            return $this;
        }
        
        public function disableTemplate()
        {
            $this->_templateDisabled = true; return $this;
        }
        
        public function enableTemplate()
        {
            $this->_templateDisabled = false; return $this;
        }
        
        public function setTemplate( $name = "application" )
        {
            $this->_template = $name; return $this;
        }
        
        public function setView( $name = "index" )
        {
            $this->_view = $name; return $this;
        }
        
        public function redirectTo( $path, $flash = null )
        {
            header("location: ".$path); exit;
        }
        
        /**
         * 
         * @param string $controller - Controller name
         * @param string $action - Action to request
         * @param string $params - Parameters to pass to the controller
         * @return \Framework\Extenders\Controller\Base
         */
        public function invokeController( $controller, $action = "index", $params = array() )
        {
            $controllerStr      = ucfirst($controller)."Controller";
            require_once APPLICATION_PATH.CONTROLLERS.strtolower($controller).'.php';
            $calledController = new $controllerStr( $this->params, $controller, $action, "GET", $this->_return_type, $params );
            return $calledController;
        }
        
        
        public function terminateRequest()
        {
            exit;
        }
        
        
        
        public function setController( $name = "index" )
        {
            $this->_controllerName = $name; return $this;
        }
        
        public function setAction( $name = "index" )
        {
            $this->_actionName = $name; return $this;
        }
        
        private function setAdditionalParams($params) {
            
            foreach($params as $key => $value) {
                $this->$key = $value;
            }
            
        }
        
        
        protected function json( $json ) 
        {
            if($this->_return_type !== "json") { return false; }
            $this->respondWithJSON($json);
        }
        
        protected function xml( $xml ) 
        {
            if($this->_return_type !== "xml") { return false; }
            $this->respondWithXML($xml);
        }
        
        protected function html( $view = null, $template = null ) 
        {
            if($this->_return_type !== "html") { return false; }
            $this->respondWithView($view, $template);
        }
        
        protected function all( $model )
        {
            
            if($this->_return_type === "xml") {
                return $this->respondWithXML($model);
            }
            
            if($this->_return_type === "json") {
                return $this->respondWithJSON($model);
            }
            
            if($this->_return_type === "html") { 
                return $this->respondWithView($model);
            }
            
        }
    
    }
    
}