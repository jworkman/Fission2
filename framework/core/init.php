<?php

try {
        ini_set('display_errors',1); 
        error_reporting(E_ALL);
        
        require_once LIBRARY_PATH."inflictor/inflictor.php";
        require_once ROOT.FRAMEWORK.CORE.'globals.php';
    
        //Import the bootstrap loader
        require_once ROOT.FRAMEWORK.AUTOLOADERS.'bootstrap.php';
    
	//Import Configuration Extenders
        require_once ROOT.CONFIG.'bootstrap.php';
        require_once ROOT.CONFIG.'application.php';
        require_once ROOT.CONFIG.'database.php';
        require_once ROOT.CONFIG.'system.php';
        
        //Load in the core MVC functionality
        require_once ROOT.FRAMEWORK.DATABASE.'model.php';
        require_once ROOT.FRAMEWORK.EXTENDERS.CONTROLLER.'base.php';
        
        
        //Load all important objects
        require_once ROOT.FRAMEWORK.OBJECTS.'router.php';
        require_once ROOT.FRAMEWORK.OBJECTS.'form.php';
        
        //Functions
        require_once ROOT.FRAMEWORK.DEFINE.'functions.php';
        require_once ROOT.FRAMEWORK.DEFINE.'objects.php';
        
        //Get the request to the correct place
        $app = new \Framework\Objects\Router();

        
        
} catch(Exception $e) {

    echo $e->getMessage();

}

