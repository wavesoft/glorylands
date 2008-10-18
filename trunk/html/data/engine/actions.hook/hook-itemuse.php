<?php
// We need this library, so include it
include_once(DIROF('ACTION.LIBRARY')."/quickbar.lib.php");

function itemuse_schedule_hook($scheduleid, $data, $user_guid) {	
	if ($scheduleid == 'item.revert') {
				
		// Load user variales
		$user_vars = gl_get_guid_vars($user_guid);

		// Revert modifications
		$modified_vars = array();
		if (isset($data['mod'])) {
			foreach ($data['mod'] as $mod) {
				$modified_vars[$mod['mod']] = $user_vars[$mod['mod']] + $mod['ofs'];
			}
		}
		
		// Save user info
		gl_update_guid_vars($user_guid, $modified_vars);
		
	}
	
	return true;
}

function itemuse_quickbar_init() {
	// When map is rendered, update quickbar
	qb_update_view();
}

function itemuse_guid_deleted($guid) {
	global $sql;

	// When a GUID is deleted, remove the appropriate Quickbar button
	$ans=$sql->query("DELETE FROM `mod_quickbar_slots` WHERE `guid` = ".$guid);
	if (!$ans) debug_error($sql->getError());
	qb_update_view();
}

function itemuse_dropdown(&$data, $guid) {
	// Add pickup handler
	array_push($data, array(
		'url'=>'?a=item.pickup&guid='.$_REQUEST['guid'], 
		'text'=>'<img src="images/UI/navbtn_take.gif" border="0" title="Pickup item" />'
	));
}

?>