<?php
/**
  * <h3>GloryLands Debug System</h3>
  *
  * The debug system is used to handle and archive internal errors. This also hides
  * the error from the end-user and notifies the administrators
  *
  * @package GloryLands
  * @subpackage Engine
  * @author John Haralampidis <johnys2@gmail.com>
  * @copyright Copyright (C) 2007-2008, John Haralampidis
  * @version 1.0
  */

/**
  * Store an error on the debug system
  *  
  * This function handles internal errors. Depending on the situation, it either hides the error
  * from the user and continues the normal execution, or breaks the execution and
  * displays an error message. 
  *
  * @param string $description 	The error description
  * @param bool $critical		TRUE if the error is not recoverable and the execution must stop
  * @return array|bool 			Returns the data chunk in an array format or false if an error occured
  *
  */
function debug_error($description, $critical) {
	global $act_result, $outmode;
	
	// Construct the debug information
	$errinfo = array(
		'trace' => debug_backtrace(),
		'critical' => $critical,
		'description' => $description
	)
	
	// Find out in which way we should render the error message
	$err_show = true;
	$err_show_mode = 'HTML';
	
	// We are inside a process execution. 
	// The format of the data being sent to the browser depends on the output mode selected
	if (defined('IN_PROCESS')) {
		$err_show = true;
		$err_show_mode = strtoupper($outmode);
	
	// We are inside a message feeding utility
	// The format of the data being sent is always JSON
	} elseif (defined('IN_MSGFEED')) {
		$err_show = true;
		$err_show_mode = 'JSON';
	
	// We have no active stream 
	// We should not display anything
	} else {
		$err_show = false;
	}
	
	// Log the error
	/* ... */
	
	// Display the error message or the debug console
	/* ... */

	// If the error is critical, break execution
	if ($critical) die();
}
  
?>