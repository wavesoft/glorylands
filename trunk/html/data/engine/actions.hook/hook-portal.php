<?php

function portal_map_move(&$player_guid, &$from_x, &$from_y, &$from_map, &$to_x, &$to_y, &$to_map, $by_script) {
	global $sql;

	$sql->query("SELECT * FROM `data_maps_teleports` WHERE `x` = $to_x AND `y` = $to_y AND `map` = $to_map");
	if (!$sql->emptyResults) {
		$row = $sql->fetch_array();
		if (callEvent('map.portal.teleported',$player_guid, $row['index'], $from_x, $from_y, $from_map, $to_x, $to_y, $to_map, $by_script)) {
			$to_x = $row['to_x'];
			$to_y = $row['to_y'];
			$to_map = $row['to_map'];
			relayMessage(MSG_INTERFACE, 'CHAT', "<font color=\"#FF0000\">{$row['message']}</font>", 'system');
		}
	}
	return true;
}

?>