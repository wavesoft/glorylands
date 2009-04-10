<?php
// ===========================================
//   PROJECT: The Glory Lands MMORPG Game
// ===========================================
//   Version: v0.1.2 Beta
//      File: Main system-wide action processor
//                   _______
// _________________| TO DO |_________________
//  -
// ___________________________________________
//   (C) Copyright 2007, John Haralampidis
// ===========================================

### Require some basic includes
require_once($_CONFIG[GAME][BASE]."/engine/includes/base.php");

### Detect the action that is about to be performed
define("IN_PROCESS",true);
global $operationstack, $operation;
$operationstack = array();
$operation = $_REQUEST["a"];
if (!$operation) {
	/* Nothing to do */
	die();
}

// Expire old users and set online current user
gl_user_action();
gl_expire_users();

// Prepare the return elements
global $act_result, $act_operation, $act_interface, $act_profile, $_CONFIG;

// Detect JSON input and merge those variables with the request array
if (isset($_REQUEST['json'])) {
	$arr = json_decode(stripslashes($_REQUEST['json']), true);
	if (is_array($arr)) $_REQUEST = array_merge($_REQUEST, $arr);
}


// Prepare default values for some missing variables
if (!isset($_REQUEST['m'])) $_REQUEST['m']=false;

#[E]# Operation initiation
callEvent('system.init_operation', $last_operation, $operation);

// Loop is taken only if the operation is changed
$last_operation = "";
while ($operation != $last_operation) {
	$last_operation = $operation;
	
	// Load the action profile if exists.
	if (file_exists(DIROF('ACTION.MANIFEST').$operation.".php")) {
		$act_profile = include(DIROF('ACTION.MANIFEST').$operation.".php");
	} else {
		$act_profile = array();
	}
	
	### Initialize action parameters
	
	// Do we have a forced-override interface from the manifest?
	if (!isset($act_profile['forced_interface'])) {
		// Do we have specific interface information from URL?
		if (isset($_REQUEST['iface'])) {
			// Use this interface
			$act_interface = $_REQUEST['iface'];
		} else {
			// Load the default interface from the action profile
			if (isset($act_profile['default_interface'])) {
				$act_interface = $act_profile['default_interface'];
			} else {
				$act_interface = 'default';
			}
		}
	} else {
		$act_interface = $act_profile['forced_interface'];
	}
	$act_operation = $operation;
	
	### Check if the operation is valid to be executed
	global $act_valid, $act_invalid_position;
	$act_valid = include(DIROF('SYSTEM.ENGINE')."security-check.php");
	if (!$act_valid) $act_invalid_position='security';
	if ($act_valid) {
		$act_valid = include(DIROF('SYSTEM.ENGINE')."validity-check.php");
		if (!$act_valid) $act_invalid_position='validity';
	}
	
	### Initialize the output manager's variables
	### Those variables might be changed during functon execution
	
	// Check for overriden output
	if ($_REQUEST['m']=='debug') {
		$outmode = "debug";
	} elseif (isset($act_profile['forced_outmode'])) {
		$outmode = $act_profile['forced_outmode'];
	} else {
		$outmode = $_REQUEST["m"];
		if (!$outmode) {
			if (isset($act_profile['default_outmode'])) {
				$outmode = $act_profile['default_outmode']; /* Use profile wanted from the manifest */
			} else {
				$outmode = "echo"; 							/* Defaut output processor is the Echo */
			}
		}
	}
	
	### Ok, the action can be executed, so.. Run it!
	if ($act_valid) {
		$act_result = array();
		
		// Include any custom pre-run result array
		if (isset($act_profile['default_result'])) {
			if (is_array($act_profile['default_result'])) {
				$act_result = $act_profile['default_result']; /* Set default result from manifest */
			}
		}		

		// Run the action		
		include DIROF('SYSTEM.ENGINE')."actionprocess.php";

		// Include any custom post-run result array
		if (isset($act_profile['post_result'])) {
			if (is_array($act_profile['post_result'])) {
				$act_result = array_merge($act_result, $act_profile['post_result']); /* Update result */
			}
		}

	} else {
		$act_result = false;
	}
	
	// If we have entries in operation stack, feed them and restart
	if (sizeof($operationstack)>0) {
		// FIFO Mode Mode
		$opinfo=array_shift($operationstack);
		
		#[E]# Operation is switched by script
		if (callEvent('system.switch_operation', $last_operation, $opinfo['op'], $opinfo['parm'])) {
		
			// Update information
			$operation=$opinfo['op'];
			if ($opinfo['parm']) {
				$_REQUEST = array_merge($_REQUEST, $opinfo['parm']);
			}

			continue;
		}		
	}

	// Inform any code that must send data now
	callEvent('system.complete_operation', $operation);
	
	// If we don't have blank output..
	if ($outmode!='blank') {
	
		// Do we have a request or a profile rule for buffered output? 
		// (Buffered outputs uses normal output processors for pre-processing and
		//  then it passes the result on a secondary output processor)
		$useBuf = false;
		if ($outmode=='debug') {
			$useBuf = false;
		} elseif (isset($act_profile['post_processor'])) {
			if (file_exists(DIROF('OUTPUT.PROCESSOR')."out.".$act_profile['post_processor'].".php")) {
				ob_start();
				$useBuf = $act_profile['post_processor'];
			}
		} elseif (isset($_REQUEST['buffered']) && file_exists(DIROF('OUTPUT.PROCESSOR')."out.".$_REQUEST['buffered'].".php")) {
			ob_start();
			$useBuf = $_REQUEST['buffered'];
		}
		
			// Check if the output processor exists
			if (file_exists(DIROF('OUTPUT.PROCESSOR')."out.{$outmode}.php")) {
				// It exists, so pass the information to handle on it
				include DIROF('OUTPUT.PROCESSOR')."out.{$outmode}.php";
			}
		
		// If we have used Buffered Output, process the 
		// second layer
		if ($useBuf) {
			/* Feed buffer on output buffer variable */
			$act_result['text'] = ob_get_contents();
			ob_end_clean();
			
			/* Unset variables that are not required to reach output buffer */
			if (isset($act_result['_my'])) unset($act_result['_my']);
			
			/* Feed result */
			include DIROF('OUTPUT.PROCESSOR')."out.".$useBuf.".php";
		}
		
		### Display time resuts
		$time_end = microtime(true);
		$time = $time_end - $script_time;
		
		### IF in debug mode, or timestamp is requested, display the page creation statistics
		if ($outmode == 'debug' || isset($_REQUEST['timestamp'])) {
			$ms = number_format($time*1000,1);
			echo "<hr><font face=arial size=1>Processed in <i>$time</i> sec ($ms msec), {$sql->totQueries} Queries</font>";
		}
		
		### Store some statistics used by the debug console
		$_SESSION['stats'] = array(
			'Parsed Files' => sizeof(get_included_files()),
			'Memory Usage' => number_format(memory_get_usage()/1024,2).' Kb',
			'Peak Memory Usage' => number_format(memory_get_peak_usage()/1024,2).' Kb',
			'Script Time' => number_format($time*1000, 2).' ms',
			'MySQL Queries' => $sql->totQueries,
			'MySQL Time' => number_format($sql->totTime*1000, 2).' ms',
		);
		if (defined('GLOB_DEBUG')) {
			$_SESSION['stats']['MySQL Queries'] = '<pre>'.print_r($sql->queryList,true).'</pre>';
		}
		
	}
}

?>