<?php

function hb_dynamic_grid_alter($ignore_guid, $x, $y, $map) {

	global $sql, $_CONFIG;

	// Search players in region using SQL Query that is 
	// a LOT faster than using PHP script to do the same job
	
	$scrW = $_CONFIG[TUNE][MAP_GRID_W];
	$scrH = $_CONFIG[TUNE][MAP_GRID_H];
	$x1 = $x-ceil($scrW/2);
	$y1 = $y-ceil($scrH/2);
	$x2 = $x+ceil($scrW/2);
	$y2 = $y+ceil($scrH/2);
	
	$ans = $sql->query("SELECT
						char_instance.guid
						FROM
						char_instance
						Inner Join users_accounts ON char_instance.account = users_accounts.`index`
						WHERE
						char_instance.map =  '$map' AND
						char_instance.x >= $x1 AND
						char_instance.x <= $x2 AND
						char_instance.y >= $y1 AND
						char_instance.y <= $y2 AND
						char_instance.online = 1 AND
						users_accounts.online =  '1'
						");
	if (!$ans) {
		relayMessage(MSG_INTERFACE, 'MSGBOX', 'Error: '+$sql->getError());
		return;
	};
	
	// Post message to all the affiliated users
	while ($row = $sql->fetch_array_fromresults($ans, MYSQL_NUM)) {
		if ($row[0]!=$ignore_guid) {
			// Tell the client to update the grid
			postMessage_once(MSG_INTERFACE, $row[0], 'UPDATEGRID');
		}
	}
	
}

?>