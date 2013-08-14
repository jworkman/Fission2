<?php

namespace HTML {

    class Form {

        private $model;
        private $csrf = true;
        private $action;
        private $method = "POST";
        private $namespace;
        private $elements = array();
        private $attributes = array();

        
        /**
         * 
         * @param \Framework\Extenders\Model\ORM_Collection $model - ORM Model to turn to form
         * @return \HTML\Form
         */
        public static function model( $model )
        {
            
            $form = new self();
            $form->model = $model;
            
            //If the model already exists
            if( $form->model->exists() ) {
                $form->action = update_path( $form->model );
            } else {
                $form->action = create_path( $form->model );
            }
            
            $form->namespace = strtolower(get_class($form->model));
            
            return $form;
            
        }
        
        public function attributes( $attr = array() )
        {
            $this->attributes = $attr;
        }


        public function getValue( $name ) 
        {
            
            if( isset($_POST[$this->namespace]) && isset($_POST[$this->namespace][$name]) ) {
                
                if( !empty($_POST[$this->namespace][$name]) ) {
                    return $_POST[$this->namespace][$name];
                }
                
            }
            
            if( is_null($this->namespace) || empty($this->namespace) ) {
                
                if( !empty($_POST[$this->namespace]) ) {
                    return $_POST[$this->namespace];
                }
                
            }
            
            return (isset($this->model->$name)) ? $this->model->$name : null;
            
        }
        
        
        public function getNamespace()
        {
            return (is_null($this->namespace)) ? "" : $this->namespace;
        }
        
        public function getInputname( $fieldName )
        {
            $nsp = $this->getNamespace();
            if(empty($nsp)) {
                return $fieldName;
            }
            return $nsp."[".$fieldName."]";
        }
        
        public function setNamespace( $namespace )
        {
            $this->namespace = $namespace;
        }
        
        public function setAction( $action )
        {
            $this->action = $action;
        }
        
        public function setMethod( $method = "GET" )
        {
            $this->method = $method;
        }
        
        public function setModel( $model )
        {
            $this->model = $model;
        }
        
        public function getModel()
        {
            return $this->model;
        }
        
        public function getMethod()
        {
            return $this->method;
        }
        
        public function getAction()
        {
            return $this->action;
        }
        
        public function enableCSRFProtection()
        {
            $this->csrf = true;
            return $this;
        }
        
        public function disableCSRFProtection()
        {
            $this->csrf = false;
            return $this;
        }
        
        /**
         * 
         * @param String $name - Name of property on ORM object
         * @return \HTML\TextField
         */
        public function textField( $name )
        {
            array_push($this->elements, \HTML\TextField::init( $this->getInputname($name) )
                    ->value( $this->getValue($name) ));
            return $this->elements[ count($this->elements) - 1 ];
        }
        
        /**
         * 
         * @param String $name - Name of property on ORM object
         * @return \HTML\TextArea
         */
        public function textArea( $name )
        {
            array_push($this->elements, \HTML\TextArea::init( $this->getInputname($name) )
                    ->value( $this->getValue($name) ));
            return $this->elements[ count($this->elements) - 1 ];
        }
        
        /**
         * 
         * @param String $name - Name of property on ORM object
         * @return \HTML\Submit
         */
        public function submit( $name = "Submit" )
        {
            array_push($this->elements, \HTML\Submit::init( $this->getInputname($name) )
                    ->value( $this->getValue($name) ));
            return $this->elements[ count($this->elements) - 1 ];
        }
        
        
        public function create()
        {
            
            echo "<form method=\"".$this->method."\" action=\"".$this->action."\"".\HTML\FormElement::createAttributes($this->attributes).">\r\n";
            
                foreach($this->elements as $element) 
                {
                    echo $element->create();
                }
            
            return $this;
            
        }
        
        public function close()
        {
            return "\r\n</form>";
        }

    }
    
    class FormElement {
        
        private $name;
        private $value;
        private $attr = array();
        
        public static function createAttributes( $attr = array() )
        {
            
            $str = "";
            
            foreach($attr as $key => $value)
            {
                $str .= " ".$key."=\"".$value."\"";
            }
            
            return $str;
            
        }
        
        public function __construct( $name ) 
        {
            $this->name = $name;
        }
        
        public static function init( $name )
        {
            $class = get_called_class();
            return new $class( $name );
        }
        
        public function name( $name ) 
        {
            $this->attr["name"] = $name;
            return $this;
        }
        
        public function value( $value ) 
        {   
            //$this->attr["value"] = $value;
            $this->value = $value;
            return $this;
        }
        
        public function getValue() 
        {   
            return $this->value;
        }
        
        public function attributes( $attr = array() )
        {
            $this->attr = $attr;
            return $this;
        }
        
        public function create()
        {
            if(!$this::VALUE_IN_TAG) {
                $this->attr["value"] = $this->value;
                $this->attr["type"] = $this::ELEMENT_TYPE;
            }
            
            $this->attr["name"] = $this->name;
            
            $str = "<".$this::ELEMENT_TAG_NAME.$this::createAttributes($this->attr);
            $str .= ($this::SELF_CLOSING) ? "/>" : ">" ;
            $str .= ($this::VALUE_IN_TAG) ? $this->value : "" ;
            $str .= ($this::SELF_CLOSING) ? "" : "</".$this::ELEMENT_TAG_NAME.">";
            
            return $str;
        }
        
    }
    
    class TextField extends \HTML\FormElement {
        
        const ELEMENT_TAG_NAME = "input";
        const ELEMENT_TYPE = "text";
        const SELF_CLOSING = true;
        const VALUE_IN_TAG = false;
        
    }
    
    class TextArea extends \HTML\FormElement {
        
        const ELEMENT_TAG_NAME = "textarea";
        const ELEMENT_TYPE = "";
        const SELF_CLOSING = false;
        const VALUE_IN_TAG = true;
        
    }
    
    class RadioButton extends \HTML\FormElement {
        
        const ELEMENT_TAG_NAME = "input";
        const ELEMENT_TYPE = "radio";
        const SELF_CLOSING = true;
        const VALUE_IN_TAG = false;
        
    }
    
    class CheckBox extends \HTML\FormElement {
        
        const ELEMENT_TAG_NAME = "input";
        const ELEMENT_TYPE = "checkbox";
        const SELF_CLOSING = true;
        const VALUE_IN_TAG = false;
        
    }
    
    class Submit extends \HTML\FormElement {
        
        const ELEMENT_TAG_NAME = "input";
        const ELEMENT_TYPE = "submit";
        const SELF_CLOSING = true;
        const VALUE_IN_TAG = false;
        
    }

}