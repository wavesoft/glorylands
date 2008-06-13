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

	function d($x1,$y1,$x2,$y2) {
		return sqrt(pow(($x1-$x2),2) + pow(($y1-$y2),2));
	}
	
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

?>
