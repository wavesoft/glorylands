<?php

global $result;
$result = array();

function attack_hit($x,$y,$walk_trace) {
	global $result, $sql;	
	
	// Build answer
	$ans = array('x' => $x, 'y' => $y, 'id' => $id, 'color'=>'#FF9900');

	// Store the result		
	array_push($result, $ans);
}	

// Generate attack range
range_calculate($_SESSION[PLAYER][DATA]['x'], $_SESSION[PLAYER][DATA]['y'], 2, 'attack_hit');

// Display attack grid
relayMessage(MSG_INTERFACE, 'ACTIONGRID', array('base'=>'?a=fight.main', 'grid'=>$result));
?>