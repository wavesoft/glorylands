<?php

if (!isset($_REQUEST['guid'])) return;
if (!isset($_REQUEST['slot'])) return;

// Obdain guid info and variables
$guid = $_REQUEST['guid'];
$info = gl_analyze_guid($guid);
$vars = gl_get_guid_vars($guid);
$kind = strtolower($info['group']);

//relayMessage(MSG_INTERFACE, 'MSGBOX', print_r($info,true)); 

// Check for the known object types
if ($kind == 'char') {
	
	// The player 'used' a charachter
	// This will display the charachter's info
	gl_do('info.guid', array('guid'=>$guid));
	
} elseif ($kind == 'item') {
	
	// Check class
	if ($vars['class'] == 'CONTAINER') {
		gl_do('interface.container', array('guid'=>$guid));
	} elseif ($vars['class'] == 'CONSUMABLE') {
		gl_use_item($guid);
	} else {
		gl_do('info.guid', array('guid'=>$guid));
	}
	

} elseif ($kind == 'gameobject') {

}


?>