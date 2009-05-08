<?php
// ===========================================
//   PROJECT: The Glory Lands MMORPG Game
// ===========================================
//   Version: v0.1 Beta
//      File: Base includes and initializations
//                   _______
// _________________| TO DO |_________________
//  -
// ___________________________________________
//   (C) Copyright 2007, John Haralampidis
// ===========================================

### Notify any included objects that they are included by script
global $script_time;
$script_time = microtime(true);
define('IN_SCRIPT',true);
error_reporting(E_ALL ^ E_NOTICE);

### Basic includes
include_once $_CONFIG[GAME][BASE]."/config/diralias.php";
include_once $_CONFIG[GAME][BASE]."/ver.php";
//include_once DIROF('DATA.ENGINE')."guid_dictionary.php";
include_once DIROF('OUTPUT.FILE')."interfaces/libs/Smarty.class.php";
include_once DIROF('DATA.ENGINE')."template_dictionary.php";
include_once DIROF('SYSTEM.INCLUDE')."mysql.php";
include_once DIROF('SYSTEM.INCLUDE')."errors.php";
include_once DIROF('SYSTEM.INCLUDE')."eventsystem.php";
include_once DIROF('SYSTEM.INCLUDE')."instance.php";
include_once DIROF('SYSTEM.INCLUDE')."glfunctions.php";
include_once DIROF('SYSTEM.INCLUDE')."scheduler.php";
include_once DIROF('SYSTEM.INCLUDE')."itemmix.php";
include_once DIROF('SYSTEM.INCLUDE')."spawnsystem.php";
include_once DIROF('SYSTEM.INCLUDE')."dynupdate.php";
include_once DIROF('SYSTEM.INCLUDE')."debugsystem.php";
include_once DIROF('SYSTEM.INCLUDE')."unicode.php";
include_once DIROF('SYSTEM.INCLUDE')."cache.php";
include_once DIROF('SYSTEM.INCLUDE')."translate.php";

### Connect to DB
global $sql;
$sql = new db($_CONFIG[DB][DATABASE], $_CONFIG[DB][HOST], $_CONFIG[DB][USER], $_CONFIG[DB][PASSWORD], true);

### Initialize UNICODE UTF-8 support
mb_internal_encoding('utf-8');

### Initialize session
include_once DIROF('SYSTEM.INCLUDE')."session.php";

### Initialize event system ###
gl_init_events();

### Process any scheduled evens
gl_process_schedules();
gl_process_timesync();
gl_spawn_check();

### Start output compression
if (!defined("NOZIP")) {
	$zip = gl_get_compatible_zip_handler();
	if (!$zip) {
		ob_start();
	} else {
		ob_start($zip);
	}
}

?>