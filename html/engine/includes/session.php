<?php
// ===========================================
//   PROJECT: The Glory Lands MMORPG Game
// ===========================================
//   Version: v0.1 Beta
//      File: Session management & environment
//            variables
//                   _______
// _________________| TO DO |_________________
//  -
// ___________________________________________
//   (C) Copyright 2007, John Haralampidis
// ===========================================

global $sql;

### Sesssion-wide constants
define('PLAYER','player');
define('GUID','guid');
define('DATA','data');
define('PROFILE','profile');
define('CHARS','chars');
define('TEMP','temp');
define('DYNAMIC','dynamic');

### Switch session into to MemCache, if available
if ($_CONFIG[MCACHE][ENABLE]) {
	if (extension_loaded('memcache')) {
		$session_save_path = "tcp://".$_CONFIG[MCACHE][HOST].":".$_CONFIG[MCACHE][PORT]."?persistent=1&weight=2&timeout=2&retry_interval=10";
		ini_set('session.save_handler', 'memcache');
		ini_set('session.save_path', $session_save_path);
	}
}

### Start Session
session_start();

### Check and build dynamic data
global $EventChain, $GUIDGroupOf, $GUIDReverseOf;

//#@# Keep this for debug=========
//unset($_SESSION[DATA][DYNAMIC]);
//================================

if (!isset($_SESSION[DATA][DYNAMIC])) {
	// Build all the dynamic data that has to be cached
	// on the session to speed-up the xecution
	$_SESSION[DATA][DYNAMIC] = array();

	// Try to build straight and reverse GUID dictionary data
	$GUIDGroupOf = array();
	$GUIDReverseOf = array();
	$ans=$sql->query("SELECT * FROM `system_dictionaries` WHERE `group` = 'GUID'");
	if (!(!$ans) && !$sql->emptyResults) {
		while ($row = $sql->fetch_array(MYSQL_ASSOC)) {
			$GUIDGroupOf[$row['value']] = strtolower($row['name']);
			$GUIDReverseOf[strtolower($row['name'])] = $row['value'];
		}
	}	

	// Cache it into session
	$_SESSION[DATA][DYNAMIC]['guiddic_nrm'] = $GUIDGroupOf;
	$_SESSION[DATA][DYNAMIC]['guiddic_rev'] = $GUIDReverseOf;
	
} else {
	
	// Restore cache from session
	$EventChain = $_SESSION[DATA][DYNAMIC]['hook'];
	$GUIDGroupOf = $_SESSION[DATA][DYNAMIC]['guiddic_nrm'];
	$GUIDReverseOf = $_SESSION[DATA][DYNAMIC]['guiddic_rev'];
}

?>