<?php

// Check for provided information
if (!isset($_REQUEST['guid1'])) return;
if (!isset($_REQUEST['guid2'])) return;
if (!isset($_REQUEST['slot'])) return;

// Obdain the two GUID information
$src_vars = gl_get_guid_vars($_REQUEST['guid1']);
$dst_vars = gl_get_guid_vars($_REQUEST['guid2']);

// If the target is container, try to place the object there
if ($dst_vars['class'] == 'CONTAINER') {
	
}

$sql->query("DELETE FROM `mod_quickbar_slots` WHERE `guid` = ".$_REQUEST['guid']." AND `slot` = ".$_REQUEST['slot']);

qb_update_view();

?>