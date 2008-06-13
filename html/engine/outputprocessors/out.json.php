<?php
// ===========================================
//   PROJECT: The Glory Lands MMORPG Game
// ===========================================
//   Version: v1.1
//	   Class: Output processor
//      File: JSON Output packing to be used
//			  by the interactive JS interface
//               ______________
// _____________| REVISION LOG |_________________
//  - Resut changed to match gl-API-1.0 I/O Engine
//                   _______
// _________________| TO DO |_________________
//  - 
// ___________________________________________
//   (C) Copyright 2007, John Haralampidis
// ===========================================

if ($act_valid) {

	// Always append JSON messages to the output
	$extra = array(
		'messages'=>jsonPopMessages(MSG_INTERFACE)
	);
	$act_result = array_merge($act_result, $extra);
	
	// Send results
	echo json_encode($act_result);
	
} else {

	// In case no valid data have arrived, just reply an error response
	echo json_encode(array(
		'mode'=>'ERROR',
		'error'=>'Action blocked in '.$act_invalid_position.' check'
	));

}

?>