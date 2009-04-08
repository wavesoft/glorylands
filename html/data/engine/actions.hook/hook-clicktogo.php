<?php

$c2g_path = array();
$c2g_log = '';

function c2g_distance($x1,$y1,$x2,$y2) {
	return sqrt(pow(($y2-$y1),2)+pow(($x2-$x1),2));
}

function c2g_sort_function($a, $b) {
    if ($a[2] == $b[2]) return 0;
    return ($a[2] < $b[2]) ? -1 : 1;
}

function c2g_sort_directions($from_x, $from_y, $to_x, $to_y, &$directions) {	
	// Store the distances for all the directions
	for ($i=0; $i<sizeof($directions); $i++) {
		$directions[$i][2] = c2g_distance($from_x+$directions[$i][0], $from_y+$directions[$i][1], $to_x, $to_y);
	}	
	// Sort the directions, based on the distance
	usort($directions, 'c2g_sort_function');
}

function c2g_pathwalk($from_x, $from_y, $to_x, $to_y, $speed) {
	global $c2g_log;
	
	$c2g_log .= "Entering $from_x,$from_y with speed $speed\n";
	
	### Check if we have enough speed left ###
	$grid = gl_cache_get('grid','zmap',CACHE_SESSION);
	if (!$grid[$from_y]) return false; // Cannot enter
	if (!$grid[$from_y][$from_x]) return false; // Cannot enter
	$attennuation = $grid[$from_y][$from_x];
	$speed -= $attennuation;
	
	// If the speed is excausted, consider it as the final position
	// Prepare the return stack and quit
	if ($speed <= 0) {
		$c2g_log .= "Speed exhausted. Returning ($from_x,$from_y)\n";
		return array(array('x'=>$from_x, 'y'=>$from_y));
	} else {
		$c2g_log .= "Still here with speed $speed\n";	
	}
	
	### Initialize Directions ###
	$directions = array(
		              array(0,-1),
		array(-1,0) ,              array(1,0),
		              array(0,1) 
	);
	$direction_count = 8;
	c2g_sort_directions($from_x, $from_y, $to_x, $to_y, $directions);		
	$c2g_log .= "Directions sorted:".print_r($directions,true)."\n";
	
	### Start walking towards ($to_x,$to_y), using the best directions ###
	for ($i=0; $i<$direction_count; $i++) {
		$test_x = $from_x+$directions[$i][0];
		$test_y = $from_y+$directions[$i][1];
		
		// If the next step is the target, we are done!
		if (($test_x == $to_x) && ($test_y == $to_y)) {
			$c2g_log .= "Reached the end. Returning ($to_x,$to_y), ($from_x, $from_y)\n";
			return array(array('x'=>$to_x, 'y'=>$to_y),array('x'=>$from_x, 'y'=>$from_y));
		}
		
		// Try to walk this direction
		$result = c2g_pathwalk($test_x, $test_y, $to_x, $to_y, $speed);
		
		// If the next step successfully reaches the target, stack
		// our position on the return stack and quit
		if ($result !== false) {
			$c2g_log .= "Found completion. Returning ($from_x,$from_y)\n";
			array_push($result, array('x'=>$from_x, 'y'=>$from_y));
			return $result;
		}
	}
	
	// If we reach this point.. something was not successfull...
	return false;
}

registerEvent('c2g_move', 'map.move');
function c2g_move($guid, $from_x, $from_y, $from_map, &$to_x, &$to_y, $to_map) {
	global $c2g_path;
	global $c2g_log;

	// Inter-map movement is free
	if ($from_map != $to_map) return true;
	
	// If we move from a position to another, we should check what path to use
	$path = c2g_pathwalk($from_x, $from_y, $to_x, $to_y, 50);
	
	if ($path!==false) {
		$to_x = $path[0]['x'];
		$to_y = $path[0]['y'];
		$c2g_path = array_reverse($path);
	} else {
	//	relayMessage(MSG_INTERFACE,'MSGBOX','Cannot detect best path!');
	}
	
	$c2g_log.='Stack: '.print_r($c2g_path,true);
	//relayMessage(MSG_INTERFACE,'POPUP','<pre>'.$c2g_log.'</pre>','Log');
}

registerEvent('c2g_render_player', 'map.render.player');
function c2g_render_player(&$data, $guid, $row) {	
	global $c2g_path;
	if (sizeof($c2g_path)>0) {	
		// If we have found a path, inject it to render result
		$data['fx_move'] = 'path';
		$data['fx_path'] = $c2g_path;
		
		// Also store it as my motion track.
		// It will be distributed to ther players on map
		gl_cache_set('paths', $guid, $c2g_path, CACHE_GLOBAL);
	}
}

registerEvent('c2g_render_char', 'map.render.char');
function c2g_render_char(&$data, $guid, $row) {
	// If this player has a motion track, send it now
	$track = gl_cache_get('paths', $guid, CACHE_GLOBAL);
	if (is_null($track)) return true;
		
	$data['fx_move'] = 'path';
	$data['fx_path'] = $track;
}

?>