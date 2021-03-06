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

	// Load Smarty engine (just to load the translation)
	$smarty = new Smarty;
	$smarty->template_dir = DIROF('DATA.INTERFACE',true);
	$smarty->compile_dir = DIROF('OUTPUT.PROCESSOR')."interfaces/cache";
	$smarty->config_dir = DIROF('DATA.LANG');
	$smarty->compile_check = false;
	$smarty->debugging = false;
	$smarty->config_load($_CONFIG[GAME][LANG].'.dat');

	// Send results
	$tpl_output = gl_translate(json_encode($act_result));
	echo $tpl_output;
	
} else {

	// In case no valid data have arrived, just reply an error response
	debug_message('Invalid action. Rejected on '.$act_invalid_position.'. Player session: '.print_r($_SESSION,true));	
	echo json_encode(array(
 		'mode'=>'ERROR',
		'number' => 101,
		'error'=>'Session lost'
	));

}

?>