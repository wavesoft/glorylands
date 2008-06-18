<?php

// Require configuration
require_once "config/config.php";
require_once "config/diralias.php";

// Do we have a clean index request?
if (( (substr($_SERVER['REQUEST_URI'],-9) == 'index.php') || 
	  (substr($_SERVER['REQUEST_URI'],-1) == '/') )
 	   && !isset($_REQUEST['a'])) {
	// Load default index interface
	$_REQUEST['a'] = $_CONFIG[GAME][INDX_INTERFACE];
}

// Process the event
require_once "engine/eventprocess.php";
?>