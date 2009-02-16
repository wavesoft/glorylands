<?php

$objects = array();

// Search objects with the same root
if (isset($_REQUEST['guid'])) {
	$root_guid = $_REQUEST['guid'];
} else {
	$act_result = array('mode'=>'NONE');
	return;
}

// Search for an empty item
function free_slot($array) {
	$min=100000000;
	$max=0;
	
	// Calculate bounds
	foreach ($array as $index => $data) {
		if ($index<$min) $min=$index;
		if ($index>$max) $max=$index;
	}
	
	// Search for free slot
	for ($i=$min; $i<=$max; $i++) {
		if (!isset($array[$i])) {
			return $i;
		}
	}
	
	// Not found by now? Create new
	return ($max+1);
}

// Get the object variabls
$object_vars = gl_get_guid_vars($root_guid);

// Calculate the maximum number of slots
if (isset($object_vars['slots'])) {
	$object_slots = $object_vars['slots'];
} else {
	$object_slots = 100; // Defaults to 100
}

$guids = gl_get_guid_children($root_guid, 'item', STACK_AUTO);
$maxslots = 0; // Slots used
foreach ($guids as $guid => $count) {
	$vars = gl_get_guid_vars($guid);
	$desc = gl_translate_vars('item', $vars, 3);
	$desc_html = '<table>';
	foreach ($desc as $var) {
		$desc_html.='<tr><td>'.$var['name'].'</td><td>'.$var['value']."</td></tr>\n";		
	}
	$desc_html.="</table>\n";		
	
	$data = array(
		'name' => $vars['name'], 
		'image' => $vars['icon'], 
		'guid' => $guid, 
		'desc' => $vars['description'], 
		'cost' => $vars['sell_price'],
		'count' => $count,
		'tip' => htmlspecialchars('<b>'.$vars['name'].'</b><br />'.$vars['description'].'<br />'.$desc_html),
		'handler' => 'info.guid'
	);
	
	// Calculate slot if it is not defined
	$slot=$vars['slot'];
	if ($slot=='') {
		$slot=sizeof($objects)+1;	
	} elseif ($slot > $object_slots) {
		// Does this item has a slot ID out of current slot space?
		// Reset it
		$slot = free_slot($objects);
		
	} else {
		// Check if the slot defined is already occupied
		if (isset($objects[$slot])) {
			// Find a free slot if it is
			$slot = free_slot($objects);
		}
	}
	if ($slot>$maxslots) $maxslots=$slot;
	
	// Stack the object
	$objects[$slot] = $data;
}

// Check and prepare slots
if (!isset($object_vars['slots'])) {
	$slots = 5;
} else {
	if ($object_vars['slots'] == '') {	
		$slots = 5;
	} else {
		$slots = $object_vars['slots']; 
	}
}

// Build the result
$act_result = array(
	'mode' => 'POPUP',
	'title' => 'Contents of '.$object_vars['name'].' <small>(#'.$root_guid.')</small>',
	'width' => 230,
	'guid' => $root_guid,
	
	'_my'=>array(
		'objects' => $objects,
		'parent' => $root_guid,
		'slots' => $slots
	)
);

// This window has a dynamic update possibility, so register it
// on the DynUPDATE system
gl_dynupdate_create($root_guid, '?a=interface.container&guid='.$root_guid);

?>