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

	// Try to build event chain
	$EventChain = array();
	$ans=$sql->query("SELECT * FROM `system_hooks` WHERE `active` = 'YES'");
	if (!(!$ans) && !$sql->emptyResults) {
		while ($row = $sql->fetch_array(MYSQL_ASSOC)) {
			if (!isset($EventChain[$row['hook']])) $EventChain[$row['hook']] = array();
			array_push($EventChain[$row['hook']], array(
				$row['filename'], $row['function']
			));
		}
	}	
	
	// Cache it into session
	$_SESSION[DATA][DYNAMIC]['hook'] = $EventChain;

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