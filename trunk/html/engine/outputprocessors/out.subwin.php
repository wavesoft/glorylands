<?php
// ===========================================
//   PROJECT: The Glory Lands MMORPG Game
// ===========================================
//   Version: v0.1 Beta
//	   Class: Output processor
//      File: Main interface subwindow feeder
//            to AJAX
//                   _______
// _________________| TO DO |_________________
//  -
// ___________________________________________
//   (C) Copyright 2007, John Haralampidis
// ===========================================

if ($act_valid) {

	// Feeder read format : <x>|<y>|<width>|<height>|<title>|<module name>|<window name>|<content>

	// Default variables
	$xPos = "100";
	$yPos = "100";
	$width = "320";
	$height = "120";
	$title = "Glory Lands";
	$name = "";
	$module = "win";
	$text = "Glory Lands MMORPG Game";

	// If defined, load parameters and then display and send positioning
	if (isset($act_result['win_x'])) $xPos = $act_result['win_x'];
	if (isset($act_result['win_y'])) $yPos = $act_result['win_y'];
	if (isset($act_result['win_width'])) $width = $act_result['win_width'];
	if (isset($act_result['win_height'])) $height = $act_result['win_height'];
	echo $xPos."|".$yPos."|".$width."|".$height."|";	
	
	// Format and send title, module and content
	if (isset($act_result['win_title'])) $title = $act_result['win_title'];
	if (isset($act_result['win_module'])) $module = $act_result['win_module'];
	if (isset($act_result['win_name'])) $name = $act_result['win_name'];
	if (isset($act_result['win_text'])) $text = $act_result['win_text'];
	echo $title."|".$module."|".$name."|".$text;
}
?>
