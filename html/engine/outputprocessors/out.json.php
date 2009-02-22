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
	
	// Translate variables
	$tpl_output = json_encode($act_result);	
	$tpl_output = preg_replace_callback('/{#([^#]*)#}/i', 
		create_function(
			'$matches',
			'global $smarty; return $smarty->get_config_vars($matches[1]);'
		), 
		$tpl_output);
		
	// Send results
	echo $tpl_output;
	
} else {

	// In case no valid data have arrived, just reply an error response
	echo json_encode(array(
		'mode'=>'ERROR',
		'error'=>'Action blocked in '.$act_invalid_position.' check'
	));

}

?>