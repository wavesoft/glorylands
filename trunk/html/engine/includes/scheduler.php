<?php
// ===========================================
//   PROJECT: The Glory Lands MMORPG Game
// ===========================================
//   Version: v0.1 Beta
//      File: Scheduling management
//                   _______
// _________________| TO DO |_________________
// ___________________________________________
//   (C) Copyright 2007, John Haralampidis
// ===========================================

// Put an event on the schedule chain
function gl_schedule_event($event_id, $description, $timeout, $event_data = false,  $userid = false) {
	global $sql;
	
	// Get current user ID, if user id is not set
	if ($userid === false) {
		if (isset($_SESSION[PLAYER][GUID])) {
			$userid = $_SESSION[PLAYER][GUID];
		} else {
			$userid = 0;
		}
	}
	
	// Calculate new time
	$triggertime = time();
	$triggertime += $timeout;
	
	// Append the event into schedule stack
	$ans=$sql->query("INSERT INTO `system_scheduler` (`timestamp`,`user`, `eventid`,  `description`, `data`) VALUES 
				 ($triggertime, $userid, '".mysql_escape_string($event_id)."', '".mysql_escape_string($description)."', '".mysql_escape_string(serialize($event_data))."')");
	return ($ans!=false);
}

// Return the description of the specific event
function gl_get_schedule_description($event_id, $userid = false) {
	global $sql;
	
	// Get current user ID, if user id is not set
	if ($userid === false) {
		if (isset($_SESSION[PLAYER][GUID])) {
			$userid = $_SESSION[PLAYER][GUID];
		} else {
			$userid = 0;
		}
	}
	
	// Get the description of the specific event
	$ans=$sql->query("SELECT `description` FROM `system_scheduler` WHERE 
				     `user` = $userid AND
					 `eventid` = '".mysql_escape_string($event_id)."')");
	if (!$ans || $sql->emptyResults) return false;
	
	// Return the description
	$row = $sql->fetch_array(MYSQL_NUM);
	return $row[0];
}

// Return (and not delete) all the schedules from the specifc user
function gl_get_schedulees($userid = false) {
	global $sql;
	
	// Get current user ID, if user id is not set
	if ($userid === false) {
		if (isset($_SESSION[PLAYER][GUID])) {
			$userid = $_SESSION[PLAYER][GUID];
		} else {
			$userid = 0;
		}
	}
	
	// Get the information of the specific events
	$ans=$sql->query("SELECT `description`, `index`, `data`, `timestamp` FROM `system_scheduler` WHERE `user` = $userid");
	if (!$ans || $sql->emptyResults) return false;

	// Prepare return array and return data
	$ans = array();	
	while ($row = $sql->fetch_array(MYSQL_ASSOC)) {
		// Calculate time left
		$timeleft = (int) $row['timestamp'];
		$timeleft =  $timeleft - time();
		
		// Only if it is not expired, stack it on result
		if ($timeleft>0) {
			array_push($ans, array('id' => $row['index'], 'description' => $row['description'], 'time' => $timeleft, 'data'=>unserialize($row['data'])));
		}
	}
	return $ans;
}

// Return and delete all expired schedules
function gl_pop_schedules() {
	global $sql;
	
	// Store and use script start time in case the time
	// changes during the process
	$ctime = time();
	
	// Get the index of the expired events
	$ans=$sql->query("SELECT `index`,`eventid`,`user`,`data` FROM `system_scheduler` WHERE `timestamp` <= $ctime");
	if (!$ans) {debug_error($sql->getError());  return false; }
	if ($sql->emptyResults) return false;

	// Get all the results
	$buf=$sql->fetch_array_all(MYSQL_ASSOC);

	// Delete expired events
	$ans=$sql->query("DELETE FROM `system_scheduler` WHERE `timestamp` <= $ctime");
	if (!$ans) {debug_error($sql->getError()); return false; }
	
	// Return buffer
	return $buf;
}

// Process all the scheduled events
function gl_process_schedules() {
	global $sql;
	
	// Get expired events
	$ev = gl_pop_schedules();
	if (!($ev === false)) {
		foreach ($ev as $event) {
			callEvent('system.schedule', $event['eventid'], unserialize($event['data']), $event['user']);
		}
	}
}

?>