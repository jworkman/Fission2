<?php

namespace HTML {

    class Form {

        private $model;
        private $csrf = true;
        private $action;
        private $method = "POST";
        private $namespace;

        
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
                $form->action = show_path( $form->model );
            } else {
                $form->action = create_path( $form->model );
            }
            
            $form->namespace = strtolower(get_class($form->model));
            
            return $form;
            
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
        
        public function getCaptcha( $difficulty = "medium" )
        {
            
        }
        
        public function validateCaptcha()
        {
            
        }

    }

}