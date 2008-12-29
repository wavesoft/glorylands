<?php
// We need this library, so include it
include_once(DIROF('ACTION.LIBRARY')."/actionrange.lib.php");

function renderRange(&$data) {
	global $result, $mapper;
	$result = array();
	$mapper = array();

	// Used to update walkID trace with the shortest animation path
	function walk_gridfix($x,$y,$walk_trace) {
		global $result, $mapper;	
		$my_steps = sizeof($walk_trace);
		$id = $x.','.$y;
		if (isset($mapper[$id])) {
			$index = $mapper[$id];
			$entry = $result[$index];
			
			$steps = sizeof($_SESSION[DATA]['WALKID'][$entry['id']]['steps']);
			if ($my_steps < $steps) {
				$_SESSION[DATA]['WALKID'][$entry['id']]['steps'] = $walk_trace;
			}
		}
	}
	
	// Used to mark the positions the player can enter
	function walk_hit($x,$y,$walk_trace) {
		global $result, $sql, $mapper;	
		
		// Map XY-ID
		$id = sizeof($_SESSION[DATA]['WALKID']);
		$_SESSION[DATA]['WALKID'][$id] = array(
			'x'=>$x,
			'y'=>$y,
			'steps' => $walk_trace /* The steps the algorithm used to reach this point (used for animation) */
		);
		
		// Build answer
		$ans = array('x' => $x, 'y' => $y, 'id' => $id);

		// Check if the position we hit has something interesting, like teleport point	
		if ($sql->poll("SELECT `index` FROM `data_maps_teleports` WHERE `x` = $x AND `y` = $y AND `map` = ".$_SESSION[PLAYER][DATA]['map'])) {
			// Change tile color
			$ans['color'] = '#FF0000';
			$ans['title'] = 'Teleport point';
		}
				
		// Store the mapper index for quickly obdaining the index
		$index = $x.','.$y;
		$mapper[$index] = sizeof($result);

		// Store the result		
		array_push($result, $ans);		
	}	
	
	// Extract (if existing) the walk key steps from WALKID stack
	$steps = false;
	if (isset($_SESSION[DATA]['WALKID']['steps'])) $steps = $_SESSION[DATA]['WALKID']['steps'];
	
	// Cleanup temporary ID mapper
	$_SESSION[DATA]['WALKID'] = array();

	// Generate walk range
	range_calculate($_SESSION[PLAYER][DATA]['x'], $_SESSION[PLAYER][DATA]['y'], 5, 'walk_hit', 'walk_gridfix');
	
	// Store the result in the range field of the player's map object
	foreach ($data['objects'] as $id => $obj) {
		if ($data['objects'][$id]['guid'] == $_SESSION[PLAYER][GUID]) {
		
			// Store the range grid
			$data['objects'][$id]['range'] = array('grid'=>$result, 'base' => '?a=map.grid.get');
			
			// If we have the previous steps used, use them to animate the user
			if (is_array($steps)) {
				$data['objects'][$id]['fx_move'] = 'path';
				$data['objects'][$id]['fx_path'] = $steps;
			}
		}
	}
	
	return true;
}

// Hooks system.init_operation to map request value "id" into "x","y" coordinates
function opinitTranslateID($lastop, $newop) {
	if ($newop == 'map.grid.get') {
		if (isset($_REQUEST['id']) && isset($_SESSION[DATA]['WALKID'])) {
		
			// Extract x/y coordinates
			$_REQUEST['x'] = $_SESSION[DATA]['WALKID'][$_REQUEST['id']]['x'];
			$_REQUEST['y'] = $_SESSION[DATA]['WALKID'][$_REQUEST['id']]['y'];
			
			// Extract walk steps
			$steps = $_SESSION[DATA]['WALKID'][$_REQUEST['id']]['steps'];
			
			// Store only the walk steps here. This is used by the renderRange
			// function above, in order to send to the browser the animation 
			// keyframes
			$_SESSION[DATA]['WALKID'] = array('steps' => $steps);
			
			// Cleanup
			unset($_REQUEST['id']);
		}
	}

	// Continue with next message
	return true;
}

?>
 