<?php
/**
  * <h3>Dynamic Update System</h3>
  *
  * This file contains all the functions used by the DynUpdate system.
  * This system updates the client's windows when the object they display is affected
  *
  * This file uses the following tables:
  * <ul>
  *   <li>interface_openwin 	: Contains the information about the currently open windows.
  * </ul>
  *
  * This file provides the following hooks:
  * <ul>
  * </ul>
  *
  * This file handles the following hooks:
  * <ul>
  * </ul>
  *
  * This file requires the following files:
  * <ul>
  * </ul>
  *
  * @package GloryLands
  * @subpackage Engine
  * @author John Haralampidis <johnys2@gmail.com>
  * @copyright Copyright (C) 2007-2008, John Haralampidis
  * @version 1.2
  */


/**
  * Mark a window as OPEN
  *  
  * This function marks a window associated with the specified GUID, as OPEN
  * an OPEN window receives updates when the associated GUID is changed
  *
  */
function gl_dynupdate_create($guid, $updateurl) {
	global $sql;
	$userguid = $_SESSION[PLAYER][GUID];
	
	// Update SQL entry
	$ans=$sql->query("DELETE FROM `interface_openwin` WHERE `player` =  $userguid AND `guid` = $guid");
	if (!$ans) { debug_error($sql->getError()); return false; }
	$ans=$sql->query("INSERT INTO `interface_openwin` (`player`, `guid`, `updateurl`) VALUES ($userguid, $guid, '".mysql_escape_string($updateurl)."')");
	if (!$ans) { debug_error($sql->getError()); return false; }
	
	// Everything went OK
	return true;
}


/**
  * Dispose a window
  *  
  * This function removes a window from the table
  *
  */
function gl_dynupdate_dispose($guid) {
	global $sql;
	$userguid = $_SESSION[PLAYER][GUID];
	
	// Delete SQL entry
	$ans=$sql->query("DELETE FROM `interface_openwin` WHERE `player` = $userguid AND `guid` = $guid");
	if (!$ans) return false;
	
	// Everything went OK
	return true;
}

/**
  * Clear user dynamic updates
  *  
  * This function removes all the dynamic updates that are registered for the user.
  * This is usually used when the user logs out.
  *
  */
function gl_dynupdate_cleanup() {
	global $sql;
	$userguid = $_SESSION[PLAYER][GUID];
	
	// Delete SQL entries
	$ans=$sql->query("DELETE FROM `interface_openwin` WHERE `player` = $userguid");
	if (!$ans) { debug_error($sql->getError()); return false; }
	
	// Everything went OK
	return true;
}

/**
  * Norify a GUID change
  *  
  * This function sends updates on the browser if a GUID is affected
  *
  */
function gl_dynupdate_update($guid) {
	global $sql;
	
	if ($guid==0) return true;

	// Get SQL entry
	$ans=$sql->query("SELECT `updateurl`,`player` FROM `interface_openwin` WHERE `guid` = $guid");
	if (!$ans) return false;
	
	// No entries? Nothing to do...
	if ($sql->emptyResults) return true;
	
	// Notify player's browser to update it's window
	while ($row=$sql->fetch_array_fromresults($ans, MYSQL_NUM)) {		
	
		// Send a message on the appropriate player
		postMessage_once(MSG_INTERFACE,$row[1],'DYNUP:'.$guid,'CALL', $row[0]);
	}
	
	// Everything went OK
	return true;
}

?>