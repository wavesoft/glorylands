<?php

function range_calculate($x, $y, $distance, $callback, $callback_always) {
	global $enter_grid;
	$enter_grid = array();
	
	function walk($x,$y,$range_left,$direction,$walk_trace, $callback, $callback_always) {
		global $enter_grid;
		
		try{		
			// If we have not enough range, quit
			if ($range_left<1) return false;
			
			// No attennuation exists? We cannot enter there...
			$grid = &gl_cache_get('grid','zmap',CACHE_SESSION);
			if (!$grid[$y]) return false;
			if (!$grid[$y][$x]) return false;
			
			// By reaching this point, it means we can access this position. 
			// Mark it as 'accessible'...
			$id=$x.'_'.$y;
			if (!isset($enter_grid[$id])) {
				$enter_grid[$id]=true;
				call_user_func($callback, $x,$y,$walk_trace);
			} else {
				// Do we hit the same position again? Use the other callback
				if (isset($callback_always)) {
					call_user_func($callback_always, $x,$y,$walk_trace);
				}
			}
					
			// Handle the attennuation effect for the 2nd tile and further
			$att = $grid[$y][$x];
			if ($direction!=-1) {
				$new_range = $range_left-$att;
			} else {
				$new_range = $range_left;
			}
			
			// Try entering the other grid points
			$range_spots = array(
				array('x'=>0,	'y'=>-1,	'd'=>1),
				array('x'=>0,	'y'=>1,  	'd'=>0),
				array('x'=>1,	'y'=>0,  	'd'=>3),
				array('x'=>-1,	'y'=>0,  	'd'=>2)
			);		
			
			for ($i=0; $i<4; $i++) {
				if ($range_spots[$i]['d']!=$direction) { // Do not go back, from the direction we came from
					$nx=$range_spots[$i]['x'];
					$ny=$range_spots[$i]['y'];
					$nx+=$x; $ny+=$y;
					
					$stack = $walk_trace;
					array_push($stack, array('x'=>$nx,'y'=>$ny));
					walk($nx,$ny,$new_range,$i,$stack,$callback,$callback_always);
				}
			}
			
		} catch (Exception $e){
		}
	}
	
	// Generate walk range
	$stack = array(array('x'=>$_SESSION[PLAYER][DATA]['x'],'y'=>$_SESSION[PLAYER][DATA]['y']));
	walk($x, $y, $distance, -1, $stack, $callback, $callback_always);

}

?>