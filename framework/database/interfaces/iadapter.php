<?php

namespace Framework\Database\Interfaces {

    /*
     * To change this template, choose Tools | Templates
     * and open the template in the editor.
     */

    /**
     *
     * @author justinworkman
     */
    interface iAdapter {
        
        public function _setHost( $host );
        
        public function _setPassword( $pass );
        
        public function _setUser( $user );
        
        public function _setName( $name );
        
    }

}