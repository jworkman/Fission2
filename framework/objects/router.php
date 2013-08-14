<?php
namespace Framework\Objects {


    class Router {
        
        public $segments   = array();
        public $matched    = false;
        public $scope      = "";
        public $request    = "";
        public $verb       = "GET";
        public $currentSegment = 0;
        public $returnType = "html";
        
        public $params     = array();
        
        public function __construct( $scope = "" ) {
            
            $this->scope = $scope;
            $this->request = $_SERVER['REQUEST_URI'];
            $this->returnType = $this->parseReturnType();
            $this->segments = $this->parseSegments( $this->request );
            $this->verb = $this->getHTTPVerb();
            $this->initRoutes();
            
        }
        
        
        private function parseReturnType()
        {
            //$lastFiveChars = substr($this->request, (strlen($this->request) - 5));
            
            if( @strpos($this->request, ".xml", (strlen($this->request) - 4)) !== false ) {
                
                $this->request = substr($this->request, 0, -4);
                return "xml"; //XML
                
            } elseif( @strpos($this->request, ".json", (strlen($this->request) - 5)) !== false ) {
                
                $this->request = substr($this->request, 0, -5);
                return "json"; //JSON
                
            } else {
                
                return "html"; //HTML
                
            }
            
        }
        
        public function getReturnType()
        {
            return $this->returnType;
        }
        
        private function getHTTPVerb()
        {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }
        
        
        private function parseSegments( $req )
        {
            if(!empty($this->scope) && strpos($req, '/'.$this->scope) === 0) {
                $req = $this->str_replace_once('/'.$this->scope, '', trim($req));
                $chunks = explode( "/", $req );
            } else {
                $chunks = explode( "/", trim($req) );
            }
            
            return $this->array_filter_preserve( $chunks, function($segment){
                if(empty($segment) || is_null($segment)) { return false; } return true;
            });
        }
        
        public function array_filter_preserve($array, $function, $preserve=false)
        {    
            $return = array();
            foreach ($array as $k=>$v)
                {
                    if($function($v)==true) $return[$k]=$v;
                }
                if($preserve) return $return;
                else return array_values($return);
        }
        
        private function str_replace_once($str_pattern, $str_replacement, $string){

            if (strpos($string, $str_pattern) !== false){
                $occurrence = strpos($string, $str_pattern);
                return substr_replace($string, $str_replacement, strpos($string, $str_pattern), strlen($str_pattern));
            }

            return $string;
        }
        
        
        
        //Imports the routes file and it will handle its own routing.
        private function initRoutes() 
        {
            require_once ROOT.CONFIG.'routes.php';
        }
        
        public function validateScope()
        {
            
            if(empty($this->scope)) { return false; }
            
            $chunks = explode('/', $_SERVER['REQUEST_URI']);
            
            $filtered = $this->array_filter_preserve( $chunks, function($segment){
                if(empty($segment) || is_null($segment)) { return false; } return true;
            });
            
            if(isset($filtered[0]) && $filtered[0] == $this->scope) { return false; }
            return true;
            
        }
        
        public function model( $model, $controller = null )
        {
            
            if($this->validateScope()){ return false; }
            $model = strtolower($model);
            $controller = ((is_null($controller)) ? $model : $controller);
            $segmentCount = count($this->segments);
            if($segmentCount > 3) { return false; }
            if( $this->checkCurrentSegment(0) && $this->segments[0] == $model ) 
            {
                
                $action = "index";
                if($segmentCount === 1) {
                    if($this->verb == "GET") { $action = "index"; } 
                    else { $action = "create"; }
                }
                
                if($segmentCount === 2) {
                    if($this->verb == "GET" && $this->segments[1] == "new") 
                    { 
                        $action = "create";
                    } elseif($this->verb == "GET" && !empty($this->segments[1])) 
                    {
                        $action = "show";
                        $this->params['id'] = $this->segments[1];
                    } elseif($this->verb == "POST" && !empty($this->segments[1])) 
                    {
                        $action = "show";
                        $this->params['id'] = $this->segments[1];
                    } 
                }
                
                if($segmentCount === 3) {
                    if($this->verb == "GET" && $this->segments[2] == "delete") 
                    {
                        $action = "delete";
                        $this->params['id'] = $this->segments[1];
                    } elseif($this->verb == "GET" && $this->segments[2] == "edit") 
                    {
                        $action = "edit";
                        $this->params['id'] = $this->segments[1];
                    } else {
                        return false; //Terminate as invalid route
                    }
                }
                
                $this->executeRoute( $controller , $action, $this->verb );
                
            }
            
        }
        
        public function get()
        {
            if( $this->verb == "GET" ) 
            { $this->matched = true; } 
            else 
            { $this->matched = false; }
            return $this;
        }
        
        public function post()
        {
            if( $this->verb == "POST" ) 
            { $this->matched = true; } 
            else 
            { $this->matched = false; }
            return $this;
        }
        
        public function match($uri)
        {
            
            $segments = $this->parseSegments($uri);
            
            for($i = 0, $j = count($segments); $i < $j; $i++) 
            {
                if($this->checkCurrentSegment($this->currentSegment) && $segments[$i] == $this->segments[$this->currentSegment]) {
                    $this->currentSegment++;
                    continue;
                } else {
                    $this->matched = false;
                    return $this;
                }
            }
            
            $this->matched = true;
            return $this;
        }
        
        public function root()
        {
            if( count($this->segments) > 0 || !empty($this->segments) || $this->segments ) 
            { $this->matched = false; } 
            else 
            { $this->matched = true; }
            return $this;
        }
        
        public function constant( $segment )
        {
            if( $this->checkCurrentSegment() 
                && 
                $this->segments[$this->currentSegment] == $segment) 
            { $this->matched = true; $this->currentSegment++; } else 
            { $this->matched = false; }
            return $this;
        }
        
        public function checkCurrentSegment( $iteration = null )
        {
            if(!is_null($iteration)) {
                if(isset($this->segments[$iteration]) 
                && 
                !is_null($this->segments[$iteration]) 
                && 
                !empty($this->segments[$iteration])) {
                    return true;
                } else { return false; }
            }
            
            if($this->currentSegment > 0 && $this->matched === false) { return false; }
            if( isset($this->segments[$this->currentSegment]) 
                && 
                !is_null($this->segments[$this->currentSegment]) 
                && 
                !empty($this->segments[$this->currentSegment]) )
            { return true; } else 
            { return false; }
        }
        
        public function validateSegmentCount()
        {
            
            if( 
                count($this->segments) > 0 &&  
                count($this->segments) != $this->currentSegment
            ) {
                return false;
            }
            return true;
        }
        
        public function dynamic( $param, $filter = null, $flag = null )
        {
            if($this->checkCurrentSegment()) {
                
                //Check filter
                if(!is_null($filter) && $this->matchedFilter($this->segments[$this->currentSegment], $filter, $flag)) {
                    $this->params[ $param ] = $this->segments[$this->currentSegment];
                } elseif( is_null($filter) ) {
                    $this->params[ $param ] = $this->segments[$this->currentSegment];
                } else {
                    $this->matched = false;
                }
                $this->currentSegment++;
            } else {
                $this->matched = false;
            }
            return $this;
        }
        
        public function redirect( $uri )
        {
            if($this->matched && !$this->checkCurrentSegment()) {
                header("location: ".$uri);
                die();
            }
        }
        
        public function getParam( $key )
        {
            
        }
        
        public function scope( $scope, $callback )
        {
            $callback( new Router( $scope ) );
        }
        
        public function to( $controller, $action )
        {
            
            if($this->matched && $this->validateSegmentCount()) {
                $this->executeRoute($controller, $action);
                exit;
            } else {
                $this->params = array();
            }
        }
        
        public function dumpRequest()
        {
            echo "<PRE>";
            var_dump($this->request);
            echo "</PRE>";
        }
        
        public function dumpSegments()
        {
            echo "<PRE>";
            var_dump($this->segments);
            echo "</PRE>";
        }
        
        public function dumpParams()
        {
            echo "<PRE>";
            var_dump($this->params);
            echo "</PRE>";
        }
        
        public function executeRoute($controller, $action)
        {
            
            global $router;
            $router = $this;
            
            $this->controller   = $controller;
            $this->action       = $action;
            $controllerStr      = ucfirst($controller)."Controller";
            
            require_once APPLICATION_PATH.CONTROLLERS.strtolower($controller).'.php';
            
            $calledController = new $controllerStr( $this->params, $this->controller, $this->action, $this->verb, $this->returnType );
            
        }
        
        
        private function matchedFilter( $value, $filter, $flag = null )
        {
            
            if( $filter == NUMERIC && is_numeric($value)) { return true; }
            if( $filter == NON_NUMERIC && !is_numeric($value)) { return true; }
            if( $filter == REGEXPR ) { 
                if(preg_match($flag, $value) > 0) {
                    return true;
                } else {
                    return false;
                }
            }
            if($filter == MULTIPLE && in_array($value, $flag)) { return true; }
            return false;
            
        }
        

    }
    
}