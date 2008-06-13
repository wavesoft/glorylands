<?php
// ===========================================
//   PROJECT: The Glory Lands MMORPG Game
// ===========================================
//   Version: v0.1 Beta
//	   Class: Output processor
//      File: URL-Encoded variable return
//                   _______
// _________________| TO DO |_________________
//  -
// ___________________________________________
//   (C) Copyright 2007, John Haralampidis
// ===========================================

// Processor functions
function processVariable($name, $var) {
	if (is_array($var)) {
		foreach ($var as $vname => $vvar) {
			processVariable($name."_".$vname,$vvar);
		}	
	} elseif (is_object($var)) {
		return;
	} else {
		echo "&{$name}=".urlencode($var);
	}
}

// Invalid request/operation?
if ($act_valid === false) {
	echo "er=1&ed=Action+not+valid";
	return;
}

//ob_start();

// Check for i/o scheme selection
if ($act_interface == "default") {
	// Reformat each variable and output it
	echo "er=0";
	foreach ($act_result as $name => $value) {
		echo processVariable($name, $value);
	}
} else {
	// Use i/o scheme provided (if exists)
	if (!file_exists(DIROF('OUTPUT.FILE')."ioscheme/{$act_interface}.php")) {
		echo "er=1&ed=Interface+does+not+exists";
		return;
	} else {
		// Use the script from that scheme
		include DIROF('OUTPUT.FILE')."ioscheme/{$act_interface}.php";
	}
}

//$buf = ob_get_contents();
//ob_end_clean();

//parse_str($buf, $ans);
//echo "<pre>".print_r($ans,true)."</pre>";
?>