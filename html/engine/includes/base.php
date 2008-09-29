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
define('IN_SCRIPT',true);
error_reporting(E_ALL);

### Basic includes
include_once $_CONFIG[GAME][BASE]."/config/diralias.php";
include_once $_CONFIG[GAME][BASE]."/ver.php";
//include_once DIROF('DATA.ENGINE')."guid_dictionary.php";
include_once DIROF('DATA.ENGINE')."template_dictionary.php";
include_once DIROF('SYSTEM.INCLUDE')."mysql.php";
include_once DIROF('SYSTEM.INCLUDE')."errors.php";
include_once DIROF('SYSTEM.INCLUDE')."eventsystem.php";
include_once DIROF('SYSTEM.INCLUDE')."instance.php";
include_once DIROF('SYSTEM.INCLUDE')."glfunctions.php";
include_once DIROF('SYSTEM.INCLUDE')."scheduler.php";
include_once DIROF('SYSTEM.INCLUDE')."itemmix.php";

### Connect to DB
global $sql;
$sql = new db($_CONFIG[DB][DATABASE], $_CONFIG[DB][HOST], $_CONFIG[DB][USER], $_CONFIG[DB][PASSWORD], true);

### Initialize session
include_once DIROF('SYSTEM.INCLUDE')."session.php";

### Process any scheduled evens
gl_process_schedules();

### Start output compression
if (!defined("NOZIP")) {
	ob_start("ob_gzhandler");
}

?>