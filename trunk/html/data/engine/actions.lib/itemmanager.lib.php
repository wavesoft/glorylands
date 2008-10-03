<?php

// Cross-action function to pick-up an item into a container
function item_pickup($src_guid, $dst_guid) {
	// Perform the pick-up achknowledge through plugins
	// There is no direct script to prevent the operation
	if (callEvent('item.pickup', $src_guid, $dst_guid)) {
	
		// Find the item's old parent
		$parent = gl_get_guid_parent($src_guid);

		// Swap parent
		gl_update_guid_vars($src_guid, array('parent' => $dst_guid));
		
		// Notify updates on both containers
		if ($parent!=0) gl_dynupdate_update($parent);
		gl_dynupdate_update($dst_guid);
		
		return true;
	} else {	
		return false;
	}		
}

?>