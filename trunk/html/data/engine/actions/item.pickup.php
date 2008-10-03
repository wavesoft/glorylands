<?php

// Check for provided information
if (!isset($_REQUEST['guid'])) return;

// Prepare variables
$src_guid = $_REQUEST['guid'];
$dst_guid = false;

// Find all player's bags
$ans=$sql->query("SELECT * FROM `item_instance` WHERE `parent` = ".$_SESSION[PLAYER][GUID]);
while ($row = $sql->fetch_array_fromresults($ans, MYSQL_ASSOC)) {
	
	// Get object variables
	$vars = gl_get_guid_vars($row['guid']);
	
	// Is it a container?
	if ($vars['class'] == 'CONTAINER') {
		
		// Check if it has a free slot
		$children = gl_count_guid_children($vars['guid']);
		$slots = $vars['slots'];
		if (!$slots) $slots=100;

		// If it has a free slot, use this item for storing the picked up one
		if ($children<$slots) {
			$dst_guid = $row['guid'];
			break;
		}
	}
	
}

// Pickup the item
if (!$dst_guid) return;
item_pickup($src_guid, $dst_guid);

?>