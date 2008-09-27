<?php

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

?>