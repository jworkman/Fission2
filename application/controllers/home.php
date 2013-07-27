<?php

class HomeController extends Framework\Extenders\Controller\Base {
    
    //public $before = "sayController";
    
    public function sayController()
    {
        echo "Home<br/>";
    }
    
    public function index() {
        $this->stars = Star::all();
        $this->all( $this->stars );
    }
    
    public function show() {
        echo "Show";
    }
    
    public function get_edit() {
        echo "Edit";
    }
    
    public function post_edit() {
        echo "Update";
    }
    
    public function get_create() {
        echo "New";
    }
    
    public function post_create() {
        echo "Create";
    }
    
    public function delete() {
        echo "Delete";
    }
    
}