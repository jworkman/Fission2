<?php

class GalaxyController extends Framework\Extenders\Controller\Base {
    
    // GET List Of All Galaxies
    public function get_index() {
        
        $this->galaxies = Galaxy::all();
        $this->all($this->galaxies);
        
    }
    
    // POST Create New Galaxy
    public function create() {
        
        $this->galaxy = new Galaxy();
        
        if( $this->galaxy->populate( $this->params['galaxy'] ) ) {
            $this->redirectTo( edit_path($this->galaxy) );
        }
        
        $this->redirectTo( new_path( "Galaxy" ) );
        
    }
    
    // GET One Galaxy
    public function get_show() {
        
        $this->galaxy = new Galaxy( $this->params['id'] );
        $this->all( $this->galaxy );
        
    }
    
    // POST Update One Galaxy
    public function post_show() {
        
        
        $this->galaxy = new Galaxy( $this->params['id'] );
        
        if($this->galaxy->populate( $this->params['galaxy'] )) {
            $this->invokeController('Galaxy', 'show', array('flash' => 'Galaxy updated successfully!'));
        } 
        
        $this->invokeController( 'Galaxy', 'get_edit', array('flash' => 'Galaxy failed to update! Please try again.') );
        
    }
    
    // GET Form For Existing Galaxy
    public function get_edit() {
        
        $this->galaxy = new Galaxy( $this->params['id'] );
        $this->html();
        
    }
    
    // GET Form For New Galaxy
    public function get_create() {
        
        $this->galaxy = new Galaxy();
        $this->html();
        
    }
    
    // GET Remove One Galaxy
    public function get_delete() {
        
        if( Galaxy::delete( $this->params['id'] ) ) {
            $this->invokeController('Galaxy', 'index', array('flash' => 'Galaxy deleted successfully!'));
        } 
        
        $this->invokeController('Galaxy', 'index', array('flash' => 'Galaxy could not be deleted! Please try again.'));
        
    }
    
}