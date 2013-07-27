<?php

function __autoload($request) {
        
	$request = strtolower($request);
	// Check first if it is namespaced with a path
	if(strstr($request, "\\") !== false) {

		$file = "../".strtolower(str_replace("\\", "/", $request)).".php";
		if(file_exists($file)) { require_once $file; return true; }

	}

	global $bootstrap;
        
        
	for($i = 0, $j = count($bootstrap); $i < $j; $i++) {
		if(file_exists($bootstrap[$i].$request.".php")) { require_once $bootstrap[$i].$request.".php"; return true; }
		continue;
	}

}