<?php

/**
  * Every second, check the npc animation paths
  */
registerEvent('npcwalk_update', 'timesync.second');
function npcwalk_update() {	
	global $sql, $_CONFIG;
	
	$npcwalk_times = gl_cache_get('npcwalk', 'times', CACHE_GLOBAL);
	if (is_null($npcwalk_times)) $npcwalk_times = array();
	
	$ans = $sql->query("SELECT * FROM `data_npc_walk` WHERE `status` = 'WALK'");
	if (!$ans) return true;
	if ($sql->emptyResults) return true;

	$npcs_modified = array();

	while ($row = $sql->fetch_array_fromresults($ans, MYSQL_ASSOC)) {
		$guid = $row['guid'];
		if (!isset($npcwalk_times[$guid])) $npcwalk_times[$guid]=0;
		$npcwalk_times[$guid]++;
		if ($npcwalk_times[$guid] >= $row['step_delay']) {
			//debug_message($npcwalk_times[$guid].' seconds are passed for '.$guid.' We are moving to the next pos');
			$npcwalk_times[$guid] = 0;
			$pos = $row['current_pos'];
			$path = unserialize($row['path']);			
			$pos++;
			if ($pos>=sizeof($path)) $pos=0;			
			$sql->query("UPDATE `data_npc_walk` SET `current_pos` = $pos WHERE `guid` = $guid");
			
			$row = gl_get_guid_vars($guid);
			$npcs_modified[$guid] = array(
				'x'=>$row['x'], 
				'y'=>$row['y'], 
				'map'=>$row['map'],
				'path'=>$path[$pos]
			);
		}
	}	
	
	foreach ($npcs_modified as $guid => $coord) {
		$map = $coord['map'];
		$x1 = $coord['x']-ceil($_CONFIG[TUNE][MAP_GRID_W]/2);
		$x2 = $coord['x']+ceil($_CONFIG[TUNE][MAP_GRID_W]/2);
		$y1 = $coord['y']-ceil($_CONFIG[TUNE][MAP_GRID_H]/2);
		$y2 = $coord['y']+ceil($_CONFIG[TUNE][MAP_GRID_H]/2);
		$target = $coord['path'][sizeof($coord['path'])-1];
		//debug_message("Distributing NPC $guid with path ".print_r($coord['path'],true));
		
		// Convert the path to script-friendly format
		$walk_path = array();
		foreach ($path[$pos] as $step) {
			$walk_path[] = array('x'=>$step[0], 'y'=>$step[1]);
		}
		
		// Notify nearby users for the animation
		$ans = $sql->query("SELECT `guid` FROM `char_instance` WHERE `map` = $map AND `x` >= $x1 AND `x` <= $x2 AND `y` >= $y1 AND `y` <= $y2");
		while ($row = $sql->fetch_array_fromresults($ans, MYSQL_NUM)) {
			$p_guid = $row[0];
			//debug_message("Updating $p_guid with path ".print_r($coord['path'],true));
			postMessage(MSG_INTERFACE, $p_guid, 'ALTER', array(
				'x' =>$target[0],
				'y' =>$target[1],
				'fx_move' => 'path',
				'fx_path' => $walk_path,
				'guid' => $guid
			));
		}
		
		// Update the guid
		gl_update_guid_vars($guid, array('x' => $target[0], 'y' => $target[1]));
	}
	
	gl_cache_set('npcwalk', 'times', $npcwalk_times, CACHE_GLOBAL | CACHE_FAST);
}

?>