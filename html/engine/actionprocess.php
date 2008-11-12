<?php
// ===========================================
//   PROJECT: The Glory Lands MMORPG Game
// ===========================================
//   Version: v0.1 Beta
//      File: Action processor
//                   _______
// _________________| TO DO |_________________
//  1) The DIR on the action manager loading is quite slow. Create a cache file with the file information
//     and update it dynamically on each directory change.
//  2) The LIB loading system might be even faster if the includes were made just while loading the profile info
// ___________________________________________
//   (C) Copyright 2007, John Haralampidis
// ===========================================

### Check if the requested action exists on action archive
if (!file_exists(DIROF('ACTION')."{$act_operation}.php")) {
	$act_result = false;
	$act_valid = false;
	$act_invalid_position='existance';
	return;
}

### Load all the action managers
global $act_profile;
if (!isset($act_profile['lib'])) $act_profile['lib'] = array();

if (!isset($act_profile['managers'])) { /* DEFINITELY NOT FAST! */
	// If 'managers' is not defined on the profile, load all the default managers
	$d = dir(DIROF('SYSTEM.MANAGER',true));
	$managers = array();
	while (false !== ($entry = $d->read())) {
		if (substr($entry,-4)==".php") {
			$info = include(DIROF('SYSTEM.MANAGER')."$entry"); /* The manager returns an array with it's info */
			$managers[substr($entry,0,-4)] = $info;
		}
	}
	$d->close(); 
} elseif ($act_profile['managers'] === false) {
	// If 'managers' is set to false, no management will be used
	$managers = false;
} else {
	// In any other case, load the managers from the profile (The quickest way)
	$managers = array();
	foreach ($act_profile['managers'] as $entry) {
		$info = include(DIROF('SYSTEM.MANAGER')."{$entry}.php"); /* The manager returns an array with it's info */
		$managers[substr($entry,0,-4)] = $info;
		
		// Find out any extra libs we must include
		if (isset($info['lib'])) {
			$act_profile['lib'] = array_merge($act_profile['lib'], $info['lib']);
			$act_profile['lib'] = array_unique($act_profile['lib']);
		}
	};
}

### Then, include all the files that we are requested from the profile and the managers
if (isset($act_profile['lib'])) {
	if (!$act_profile['lib']===false) {
		foreach ($act_profile['lib'] as $lib) {
			include_once DIROF('ACTION.LIBRARY')."{$lib}.php";
		}
	}
}

### Create all the objects required by the action
/* ====================================== Developer's Commentary =======================================
	Loipon.. epeidi ellhnikoulia den exei.. gia na grapsw ti xreiazetai....
	1)	Theloume ena kati to opoio tha orizetai apo to request kai vash aftou
		tha dhmiourgountai classes enos h perissoterwn apo tous parapanw managers
		kai tha ginontai public wste me dyo kiniseis h opoiadipote action na
		ektelei functions epi aftwn. 
		Px.1. 	Otan kaneis click se ena item sto inventory, na yparxei mia parameter item0=<inventory id>
				Etsi, tha fortwthei mia class 'item' apo ton manager items.php (O opoios sta info epestrepse
				ws class name to 'item'), tha arxikopoiithei me index to <inventory id>, kai tha einai etoimh
				na xrisimopoiithei apo tin action ws $classes['item0'].

	Edw thelei arketh douleia.... Prepei na mpoun ola ta pithana interface pou mporei na xeiastoun se mia action
	toso gia elegxo timwn oso kai gia apothikefsh metavlhtwn. Olo afto volevei na paketaristei se class objects
	kai na stalei gia tis actions me ena eniaio onoma.... h estw me global classes.... 
	Loipon... symplirwse tin lista me oti nomizeis oti tha xreiastei mia action:
	
	1) MySQL
	2) Session
	3) Environment
	4) Manager class(es) = Items, Map elements, Units ...
	5) System configuration
	6) Unit creating class
	
	Katalhgoume:
	Synta3h metavlhtwn eisodou:	<Objectname><index>=<Preparation string>
/* ========================================================================================================  */

### Detect and load all classes that are requested. The request must have the follow format:
###  <Class Name><Index starting from zero>=<Class initialization string>
### Ex. "unit0=1x3x3&unit1=m"  Note that indexing must not have missing sequence numbers!
global $act_classes;
$act_classes = array();

//  Only if managers are not disabled
if (!($managers===false)) {
	foreach ($managers as $manager) {
		$varnum = 0;
		// If a manager appears on the URL
		while (isset($_REQUEST[$manager['name'].$varnum])) {
			$cname = $manager['class'];	
			// Instance and initialize
			$act_classes[$manager['name'].$varnum] = new $cname($_REQUEST[$manager['name'].$varnum]);
			$varnum++;
		}
	}
}

###  Instance all the helper classes that was included in the
###  whole include tree. Helper classes have name: hpr_<name> and are refered with
###  their <name> only.

if (!isset($act_profile['helpers'])) { /* DEFINITELY NOT FAST! */
	// No helpers defined? Load all the known ones
	include DIROF('SYSTEM.INCLUDE')."action_classes.php";
	$classes = get_declared_classes();
	foreach ($classes as $classname) {
		if (substr($classname,0,4) == 'hpr_') {
			$act_classes[substr($classname,4)] = new $classname;
		}
	}
} elseif ($act_profile['helpers']===false) {
	// Disable helpers? Do not load anything...
} else {
	// Load all the helpers defined from the profile
	include DIROF('SYSTEM.INCLUDE')."action_classes.php";
	foreach ($act_profile['helpers'] as $helper) {
		$classname = "hpr_".$helper;
		$act_classes[substr($classname,4)] = new $classname;
	}
}

###  Link some of the environment classes into the class array
$act_classes['sql'] = &$sql;

### Expose variables
extract($act_classes, EXTR_SKIP | EXTR_REFS);

### And now process the action
include DIROF('ACTION')."{$act_operation}.php";

?>