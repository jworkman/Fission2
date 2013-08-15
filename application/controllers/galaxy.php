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
            $this->redirectTo( show_path($this->galaxy), "Galaxy created successfully!" );
        }
        
        $this->redirectTo( new_path( "Galaxy" ), "Galaxy created successfully!" );
        
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
            $this->redirectTo( edit_path($this->galaxy), 'Galaxy updated successfully!' );
        } 
        
        $this->redirectTo( edit_path($this->galaxy), 'Galaxy failed to update! Please try again.' );
        
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
        
        if( Galaxy::find( $this->params['id'] )->destroy() ) {
            $this->redirectTo( index_path("Galaxy"), 'Galaxy deleted successfully!' );
        } 
        
        $this->redirectTo( 'Galaxy', 'Galaxy could not be deleted! Please try again.' );
        
    }
    
}