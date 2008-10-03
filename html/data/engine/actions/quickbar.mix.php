<?php

// Check for provided information
if (!isset($_REQUEST['guid1'])) return;
if (!isset($_REQUEST['guid2'])) return;
if (!isset($_REQUEST['slot'])) return;

// Obdain the two GUID information
$src_guid = $_REQUEST['guid1'];
$src_owner = gl_traceback_owner($src_guid);
$src_vars = gl_get_guid_vars($src_guid);
$dst_guid = $_REQUEST['guid2'];
$dst_vars = gl_get_guid_vars($dst_guid);

// If the target is container, try to place the object there
if ($dst_vars['class'] == 'CONTAINER') {
	
	// If we do not hold the item, try to acquire it
	if ($src_owner != $_SESSION[PLAYER][GUID]) {
	
		// Perform an item pick-up through the cross-action library function
		if (item_pickup($src_guid, $dst_guid)) {
		
			// Update the hosting window of the source object (ex. to remove the item from a container)
			//qb_update_hostview($_REQUEST['host'], $_REQUEST['view']);
			
		}
		
	} else {
	
		// If we already hold the item, try to mix them
	
	}
}

//relayMessage(MSG_INTERFACE,'MSGBOX', 'Mixing '.$_REQUEST['guid1'].' with '.$_REQUEST['guid2'].' on that will affect container '.$_REQUEST['host'].' being shown as '.$_REQUEST['view']);

qb_update_view();

?>