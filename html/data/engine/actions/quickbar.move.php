<?php

// Check if we have all the data
if (!isset($_REQUEST['guid'])) return;
if (!isset($_REQUEST['slot'])) return;

// Function to traceback to the original owner of the GUID
function traceback_owner($guid) {
	$vars = gl_get_guid_vars($guid);
	$owner = $guid;
	
	if (!isset($vars['parent'])) return $owner;
	if ($vars['parent']!=0) {
		$owner = traceback_owner($vars['parent']);
	}
	return $owner;
}

// Check if we own this item
$guid = $_REQUEST['guid'];
$slot = $_REQUEST['slot'];
$vars = gl_get_guid_vars($_REQUEST['guid']);
$owner = traceback_owner($_REQUEST['guid']);
if ($owner != $_SESSION[PLAYER][GUID]) {
	relayMessage(MSG_INTERFACE,'POPUP', "<Table><tr><img src=\"images/UI/msgbox-critical.gif\" /><td></td><td valign=\"top\"> You can only place objects that you own on the quick access bar!</td></tr></table>",'Error');
	qb_update_view();
	return;
}

$sql->query("DELETE FROM `mod_quickbar_slots` WHERE `guid` = $guid");
$sql->query("DELETE FROM `mod_quickbar_slots` WHERE `slot` = $slot");
$sql->addRow('mod_quickbar_slots', array(
	'player' => $_SESSION[PLAYER][GUID],
	'slot' => $slot,
	'guid' => $guid,
));

qb_update_view();

?>