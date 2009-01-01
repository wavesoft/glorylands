<?php

$objects = array();

// Search objects with the same root
if (isset($_REQUEST['guid'])) {
	$root_guid = $_REQUEST['guid'];
} else {
	$act_result = array('mode'=>'NONE');
	return;
}

$guids = gl_get_guid_children($root_guid, 'item', STACK_AUTO);
foreach ($guids as $guid => $count) {
	$vars = gl_get_guid_vars($guid);

	$data = array(
		'name' => $vars['name'], 
		'image' => $vars['icon'], 
		'guid' => $guid, 
		'desc' => $vars['description'], 
		'cost' => $vars['sell_price'],
		'count' => $count,
		'handler' => 'info.guid'
	);
	
	// Do some special handlings for some special items (instead of just displaying it's info)
	if ($vars['class'] == 'CONTAINER') $data['handler']='interface.container';
	
	// Stack the object
	array_push($objects,$data);
}

// Get the object variabls
$object_vars = gl_get_guid_vars($root_guid);

// Build the result
$act_result = array(
	'mode' => 'POPUP',
	'title' => 'Contents of '.$object_vars['name'].' <small>(#'.$root_guid.')</small>',
	'width' => 230,
	'guid' => $root_guid,
	
	'_my'=>array(
		'objects' => $objects,
		'parent' => $root_guid
	)
);

// This window has a dynamic update possibility, so register it
// on the DynUPDATE system
gl_dynupdate_create($root_guid, '?a=interface.container&guid='.$root_guid);

?>