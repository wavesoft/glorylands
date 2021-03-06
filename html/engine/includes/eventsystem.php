<?php
$EventChain = array();

function gl_init_events() {
	global $sql;
	// Load the hook files
	$ans=$sql->query("SELECT * FROM `system_hooks` WHERE `active` = 'YES'");
	if (!(!$ans) && !$sql->emptyResults) {
		while ($row = $sql->fetch_array(MYSQL_ASSOC)) {
			include_once(DIROF('DATA.HOOK').$row['filename']);
		}
	}
}

function registerEvent($function, $event_or_class, $event=false) {
	global $EventChain;
	
	if ($event===false) {
		$event = $event_or_class;
	} else {
		$function = array($function, $event_or_class);		
	}
	
	if (!isset($EventChain[$event])) $EventChain[$event]=array();
	$EventChain[$event][] = $function;
}

// -------------------------------------------------------
//                     Call Chained Evens
// -------------------------------------------------------
// Call method:
//   callEvent('<event name>', <up to 10 parameters>);
// -------------------------------------------------------
function callEvent($event,&$a=false,&$b=false,&$c=false,&$d=false,&$e=false,&$f=false,&$g=false,&$h=false,&$i=false,&$j=false) {
						/* ^ Bug Fix: func_get_args() does not return reference vars */
						
	global $EventChain, $_CONFIG;
		
	// No chained events? 
	// Allow execution
	if (!isset($EventChain[$event])) {
		return true;
	}
	
	// Prepare parameters array
	$parm = array(&$a,&$b,&$c,&$d,&$e,&$f,&$g,&$h,&$i,&$j);
	
	// Start chaining
	foreach ($EventChain[$event] as $calee) {
		//include_once(DIROF('DATA.HOOK').$calee[0]);
		$ans = true;
		$ans = call_user_func_array($calee, $parm);
		if ($ans === false) return false;
	}
	
	// Everything is ok by default
	return true;
}

// -------------------------------------------------------
//                UI Message stacking system
// -------------------------------------------------------
// Call method:
//    sendMessage(<type>, <parameters>);
// -------------------------------------------------------
define('MSG_INTERFACE',0); // Message is delivered to the User Interface Javascript Engine
						 // This category contains all the visual messages displaied to user
define('MSG_INTERNAL',1);	 // Message is delivered to the internal system 

// A common function used by all the other message functions
function message_backend($type, $user_guid, $data, $once) {
	global $sql;
	$row['type'] = $type;
	$row['user'] = $user_guid;
	$row['data'] = serialize($data);
	if ($once) {
		$sql->query("DELETE FROM `system_messages` WHERE `onceid` = '$once' AND `user` = $user_guid");
		$row['onceid'] = $once;
	}
	$sql->addRow('system_messages', $row);
}

// Stack a message on the relay Queue. This is a temporary
// queue that is destroied when the session expires
function relayMessage($type) {
	if (!isset($_SESSION['RELAYQ'])) $_SESSION['RELAYQ'] = array();
	if (!isset($_SESSION['RELAYQ'][$type])) $_SESSION['RELAYQ'][$type] = array();
	$data = func_get_args();
	array_shift($data);	
	array_push($_SESSION['RELAYQ'][$type],$data);
}

// Stack a message on user's main message queue. Theese messages
// are storedn on SQL and do not expire with session.
function sendMessage($type) {
	$user_guid = $_SESSION[PLAYER][GUID];
	$data = func_get_args();
	array_shift($data);	
	message_backend($type, $user_guid, $data, false);
}

// Same as above, but can send a message to a specific user
function postMessage($type, $user_guid) {
	$data = func_get_args();
	array_shift($data);	
	array_shift($data);	
	message_backend($type, $user_guid, $data, false);
}

// Same as above, but makes sure only one message exists for the user
function postMessage_once($type, $user_guid, $once_id) {
	$data = func_get_args();
	array_shift($data);	
	array_shift($data);	
	array_shift($data);	
	message_backend($type, $user_guid, $data, $once_id);
}

// Return and erase all messages stacked up by now
function popMessages($type, $message=false) {
	global $sql;

	// First, dump messages from session (higher priority)
	$result = array();
	if (isset($_SESSION['RELAYQ'][$type])) {
		$result = $_SESSION['RELAYQ'][$type];
		unset($_SESSION['RELAYQ'][$type]);
	}		
	
	// Then, dump messages from SQL
	$user_guid = $_SESSION[PLAYER][GUID];
	$erase_stack = array();
	$ans=$sql->query("SELECT `index`, `data` FROM `system_messages` WHERE `type` = $type AND `user` = $user_guid ORDER BY `index` ASC");
	if (!$ans) debug_error($sql->getError(),ERR_CRITICAL);
	if (!$sql->emptyResults) {
		while ($row = $sql->fetch_array(MYSQL_NUM)) {
			$data = unserialize($row[1]);

			// If we don't have to pop an expicit message, just stack them all
			if ($message===false) {
				$result[] = $data;
				
			// On the other hand, we must check the message
			} else {
				if ($data[0] == $message) {
					// Store it,
					$result[] = $data;
					// and explicitly erase it
					$erase_stack[] = $row[0];
				}
			}
		}
		
		// If we don't have to pop an expicit message, remove them all
		if ($message===false) {
			$ans=$sql->query("DELETE FROM `system_messages` WHERE `type` = $type AND `user` = $user_guid");
		// If we do, erase only selected
		} else {
			$ans=$sql->query("DELETE FROM `system_messages` WHERE `index` in (".implode(",",$erase_stack).")");		
		}
		if (!$ans) debug_error($sql->getError(),ERR_CRITICAL);
	}
	
	// Return result
	return $result;
}

// Return but keep all messages stacked up by now
function peekMessages($type, $message=false) {
	global $sql;

	// First, dump messages from session (higher priority)
	$result = array();
	if (isset($_SESSION['RELAYQ'][$type])) {
		$result = $_SESSION['RELAYQ'][$type];
	}
	
	// Then, dump messages from SQL
	$user_guid = $_SESSION[PLAYER][GUID];
	$ans=$sql->query("SELECT `data` FROM `system_messages` WHERE `type` = $type AND `user` = $user_guid ORDER BY `index` ASC");
	if (!$ans) debug_error($sql->getError(),ERR_CRITICAL);
	if (!$sql->emptyResults) {
		while ($row = $sql->fetch_array(MYSQL_NUM)) {
			$data = unserialize($row[0]);
									
			// If we don't have to pop an expicit message, just stack them all
			if ($message===false) {
				$result[] = $data;
				
			// On the other hand, we must check the message
			} else {
				if ($data[0] == $message) {
					// Store it
					$result[] = $data;
				}
			}
		}
	}
	
	// Return result
	return $result;
}

// Return messages in JSON-Friendly format
function jsonPopMessages($type) {
	$ans = popMessages($type);
	return array('count' => count($ans), 'message' => $ans);
}

?>