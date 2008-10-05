<?php

function distance_map_move(&$player_guid, &$from_x, &$from_y, &$from_map, &$to_x, &$to_y, &$to_map, $by_script) {

	if (!$from_x || !$from_y || (($from_x==$to_x) && ($from_y==$to_y)) ) {
		return true;
	}

	// Store modifiers used by walk function
	global $mod;
	$mod[0] = array('x'=>1, 'y'=>-1);
	$mod[1] = array('x'=>0, 'y'=>-1);
	$mod[2] = array('x'=>-1, 'y'=>-1);
	$mod[3] = array('x'=>-1, 'y'=>0);
	$mod[4] = array('x'=>-1, 'y'=>1);
	$mod[5] = array('x'=>0, 'y'=>1);
	$mod[6] = array('x'=>1, 'y'=>1);
	$mod[7] = array('x'=>1, 'y'=>0);
	
	global $log;
	$log = '';

	function walk($range_left, $x, $y, &$target_x, &$target_y, $first = true) {
		global $mod, $log;
	
		if (!$first) {
			// If no more range left, return false
			$modifier = $_SESSION['GRID']['ZID'][$y][$x];
			if (!isset($modifier)) $modifier=30;
			if ($modifier<30) $modifier=30;
	$log.= "<pre>walk($range_left, $x, $y) - Modifier: $modifier\n";
			if ($modifier>0) {
				$new_range = $range_left*((100-$modifier)/100);
			} else {
				$new_range = $range_left;		
			}
	$log.= "New Range: $new_range";
			//$new_range = $range_left-1;
			if ($new_range < 1) {
	$log.= " (Underflown)\n</pre>\n";
				return false;
			}
	$log.= "\n";
		} else {
	$log.= "<pre>walk($range_left, $x, $y) - Modifier: $modifier\n[Entry Point]\n";
			$new_range=$range_left;
		}
	
		## Shortest Patch traversing ##
	
		// Calculate the distance from alll the 8 possible
		// moving corners
		for ($i=0; $i<=7; $i++) {
			$dist[$i] = d($x+$mod[$i]['x'],$y+$mod[$i]['y'], $target_x,$target_y);
		}
		asort($dist, SORT_NUMERIC);
	$log.= "Distances: ".print_r($dist,true);
		
		// Starting from the finest corner, start walking towards
		// the target
		foreach ($dist as $m => $weight) {
			$nx=$x+$mod[$m]['x'];
			$ny=$y+$mod[$m]['y'];
	$log.= "Walking to ($nx,$ny) Using #$m (x:{$mod[$m]['x']}, y:{$mod[$m]['y']}): \n";
			if (walk($new_range,$nx,$ny, $target_x,$target_y,false)) {
	$log.= "New Spot: ($target_x, $target_y)\n</pre>";
				return true;
			}
		}
		
		// Nothing found? We reached our limit
		$target_x=$x;
		$target_y=$y;
	$log.= "No More: ($target_x, $target_y)\n</pre>";
		return true;
	}

	/* -- VERSION 1 -- 

	// Calculate distance
	$dist = sqrt(pow(($from_x-$to_x),2) + pow(($from_y-$to_y),2));
	$dist = ceil($dist);
	
	$range=3;
	// If distance is more than <range> cells, move just by the <range>'d
	if ($dist>$range) {
		$dist_x = $to_x-$from_x;
		$dist_y = $to_y-$from_y;
		
		$stepx = ceil(($range * $dist_x) / $dist);
		$stepy = ceil(($range * $dist_y) / $dist);

		$to_x = $from_x + $stepx;
		$to_y = $from_y + $stepy;
		
		//relayMessage(MSG_INTERFACE,'MSGBOX',"Distance is: $dist, Range is $range, XDiff is: $dist_x, YDiff is: $dist_y, StepX is: $stepx, StepY is: $stepy\nMoved from $from_x,$from_y to $to_x,$to_y");
	}
	
	
	relayMessage(MSG_INTERFACE,'MSGBOX',"Distance is: $dist cells");
	if ($_SESSION['GRID']['ZID'][$to_y][$to_x]>50) {
	}
	*/

	$log = '<div style="overflow: scroll; width: 100%; height: 400px"><pre>Log the walk'.": ($from_x, $from_y) =&gt ($to_x, $to_y)\n";
	$dist = 3;
	$ans = walk($dist, $from_x, $from_y, $to_x, $to_y);
	$log .= '</pre></div>';
	//relayMessage(MSG_INTERFACE,'POPUP',$log,'Debug Log', 500);

	
	return $ans;
}


function renderPhase() {
		
	global $dgrid;
	$dgrid = array();

	// Store modifiers used by walk function
	global $mod;
	$mod[0] = array('x'=>1, 'y'=>-1);
	$mod[1] = array('x'=>1, 'y'=>0);
	$mod[2] = array('x'=>1, 'y'=>1);
	$mod[3] = array('x'=>-1, 'y'=>-1);
	$mod[4] = array('x'=>-1, 'y'=>0);
	$mod[5] = array('x'=>-1, 'y'=>1);
	$mod[6] = array('x'=>0, 'y'=>-1);
	$mod[7] = array('x'=>0, 'y'=>1);

	// Walking function. This marks the hit points
	
	// Note : Must be as quick as possible because it uses
	//        high-level recursion
	
	global $minX, $maxX, $minY, $maxY, $first, $log, $dgrid;
	$minX = 0; $minY = 0; $first = true;
	$maxX = 0; $maxY = 0; $log='';

	function walk($range_left_ref, $by_x, $by_y, $to_x, $to_y, $initial=false) {
		global $minX, $maxX, $minY, $maxY, $first, $log, $dgrid;
		global $mod;
	
		// Shortcuts
		$x = &$to_x;
		$y = &$to_y;
		
		// Get the attennuation modifier
		$modifier = $_SESSION['GRID']['ZID'][$y][$x]/100;
		
		// Check if we have any steps left
		$range_left = $range_left_ref;
		$range_left = $range_left * (1-$modifier);
		$range_left--;
	
		if ($range_left < 0.5) {
			return false;
		}
		
		// Mark the grid location as "visitable"
		$dgrid[$x][$y] = true;
		
		// Update the extends
		if ($first) {
			$minX = $x;
			$minY = $y;
			$maxX = $x;
			$maxY = $y;
			$first = false;
		} else {
			if ($x<$minX) $minX=$x;
			if ($y<$minY) $minY=$y;
			if ($x>$maxX) $maxX=$x;
			if ($y>$maxY) $maxY=$y;
		}
	
		// Further walk to all the directions
		for ($i=0; $i<8; $i++) {
			$nx = $x + $mod[$i]['x'];
			$ny = $y + $mod[$i]['y'];
			if (!(($nx==$by_x) && ($ny==$by_y))) {
				$ans = walk($range_left, $x, $y, $nx, $ny);				
			}
		}
		
		// Ok. we have something marked!
		return true;
	}
	
	// Obdain walking power and apply any modifiers
	$walk_power = 5;
	$var='walk.steps';
	callEvent('var.modifier', $var, $walk_power);
	
	// Create a visual walking grid
/*
	chunk.grid  = [x,y]	(.i .c) : Contains the grid information
	chunk.show	= (.x .y)		: Contains the X/Y coordinates of the mouse location that will
								  display the region object
	chunk.x.m	= (int)			: Minimum X Value
	chunk.x.M	= (int)			: Maximum X Value
	chunk.y.m	= (int)			: Minimum Y Value
	chunk.y.M	= (int)			: Maximum Y Value
	chunk.center.x = (int)		: Center X offset
	chunk.center.y = (int)		: Center Y offset	
	chunk.action = (str)		: The base url
*/
	$visualgrid = array();

	$visualgrid['grid'] = array();
	$visualgrid['show'] = array('x'=>$_SESSION[PLAYER][DATA]['x'],'y'=>$_SESSION[PLAYER][DATA]['y']);
	$visualgrid['x'] = array('m'=>0,'M'=>0);
	$visualgrid['y'] = array('m'=>0,'M'=>0);
	$visualgrid['action'] = 'map.grid.get';

	for ($i=0; $i<8; $i++) {
		$nx = $_SESSION[PLAYER][DATA]['x']+$mod[$i]['x'];
		$ny = $_SESSION[PLAYER][DATA]['y']+$mod[$i]['y'];
		
		$ans=walk($walk_power, $_SESSION[PLAYER][DATA]['x'], $_SESSION[PLAYER][DATA]['y'], 
						  $nx, $ny, true
			 );

		// In case we cannot move anywere, since this is the first
		// step we are about to take, make sure this is not a wall
		// (100% Attennuation). Otherwise, take the step anyways
		if (!$ans) {
			if ($_SESSION['GRID']['ZID'][$ny][$nx]!=100) {
				$dgrid[$nx][$ny] = true;
			}
		}
	}
	
	// Prepare local cache for secure use
	$_SESSION[DATA]['WALKID'] = array();
	
	// Build visual grid
	$log.="Range: x=[$minX~$maxX] y=[$minY~$maxY]";
	$first = false;
	$vgrid = array();
	for ($x=$minX; $x<$maxX; $x++) {
		for ($y=$minY; $y<$maxY; $y++) {
			if ($dgrid[$x][$y]) {
			
				$log.="DGrid @$x,$y - ";
				
				// In order to avoid code exploits from
				// client-side script, the new location is not
				// received by the browser, but it is stored
				// on the local session. The value is mapped
				// with an ID that client send us back
				
				$id = sizeof($_SESSION[DATA]['WALKID']);
				$id++;
				$_SESSION[DATA]['WALKID'][$id] = array('x'=>$x, 'y'=>$y);
						
				// Store grid information
				$gx = $x-$minX;
				$gy = $y-$minY;				
				$vgrid[$gy][$gx] = array('i'=>$id, 'c'=>'#66FF66');
				$log.="VGrid @$gx,$gy<br />";
				
				// Calculate visualgrid extends
				if ($first) {
					$visualgrid['x']['m']=$gx;
					$visualgrid['x']['M']=$gx;
					$visualgrid['y']['m']=$gx;
					$visualgrid['y']['M']=$gx;
					$first=false;
				} else {
					if ($visualgrid['x']['m']>$gx) $visualgrid['x']['m']=$gx;
					if ($visualgrid['y']['m']>$gy) $visualgrid['y']['m']=$gy;
					if ($visualgrid['x']['M']<$gx) $visualgrid['x']['M']=$gx;
					if ($visualgrid['y']['M']<$gy) $visualgrid['y']['M']=$gy;
				}
			}
		}
	}
	
	// Center Offset Calculation
	$visualgrid['point'] = array('x' => $minX , 'y' => $minY);
	$visualgrid['grid'] = $vgrid;
	
	// Send the visual grid	
	$log.="<br />\n Result:<br />\n<pre>".print_r($dgrid,true)."</pre><br />\n VGrid:<br />\n<pre>".print_r($visualgrid,true)."</pre>";
	//relayMessage(MSG_INTERFACE, 'POPUP', $log, 'Debug Range');
	relayMessage(MSG_INTERFACE, 'RANGE', $visualgrid);
	
	// Continue with next message
	return true;
}

// Hooks system.init_operation to translate request value "id" into "x","y"
function opinitTranslateID($lastop, $newop) {
	if ($newop == 'map.grid.get') {
		if (isset($_REQUEST['id']) && isset($_SESSION[DATA]['WALKID'])) {
			$_REQUEST['x'] = $_SESSION[DATA]['WALKID'][$_REQUEST['id']]['x'];
			$_REQUEST['y'] = $_SESSION[DATA]['WALKID'][$_REQUEST['id']]['y'];
			unset($_SESSION[DATA]['WALKID']);
			unset($_REQUEST['id']);
		}
	}

	// Continue with next message
	return true;
}

?>
