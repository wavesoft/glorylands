<?php

// Obdain GUID information
if (!isset($_REQUEST['guid'])) {
	$act_result = array('mode'=>'NONE');
	return;
}
if (!gl_guid_valid($_REQUEST['guid'])) {
	$act_result = array('mode'=>'NONE');
	return;
}
$guidinfo = gl_analyze_guid($_REQUEST['guid']);

// Obdain GUID Variables
$vars = gl_get_guid_vars($_REQUEST['guid']);

// Translate GUID variables
$transvar = gl_translate_vars($guidinfo['group'], $vars, 2);

// Process event operations to add/remove any variables
callEvent('interface.guidinfo', $_REQUEST['guid'], $vars, $transvar, $vars['icon']);

// Calculate icon dimensions
if (isset($vars['icon'])) {
	list($width, $height, $type, $attr) = getimagesize("img/flag.jpg");

	if ($width > 120) $height = ($height * 120) / $width;		
	if ($height > 120) $width = ($width * 120) / $height;

	$icon = $vars['icon'];
	$icon_width = $width;
	$icon_height = $height;
	
} else {
	$icon = '';
	$icon_width = 1;
	$icon_height = 1;
}

// Return the result
$act_result = array(
	'mode' => 'POPUP',
	'title' => 'Information for '.$vars['name'].' (#'.$_REQUEST['guid'].')',
	'width' => 430,
	'guid' => $_REQUEST['guid'],
	
	'_my' => array(
		'name' => gl_ucfirst($vars['name']),
		'desc' => $vars['description'],
		'info' => $transvar,
		'icon' => $icon,
		'icon_width' => $ico_w,
		'icon_height' => $ico_h,
		'guid' => $_REQUEST['guid']
	)
);

// This window has a dynamic update possibility, so register it
// on the DynUPDATE system
gl_dynupdate_create($_REQUEST['guid'], '?a=info.guid&guid='.$_REQUEST['guid']);

?>