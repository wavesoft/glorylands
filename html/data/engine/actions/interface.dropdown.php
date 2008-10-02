<?php

// Get object's variables
$vars = gl_get_guid_vars($_REQUEST['guid']);
if (isset($vars['x'], $vars['y'])) {
	
	$distance = gl_distance($_SESSION[PLAYER][DATA]['x'],$_SESSION[PLAYER][DATA]['y'],$vars['x'],$vars['y']);
	if ($distance > 2) {
		// We are too far away
		$act_result = array(
			'mode' => 'DROPDOWN',
			'text' => '<small><em>(Too far away!)</em></small>'
		);
		return;
	}
}

// Initialize reply stack
$data = array(
	array('url'=>'?a=info.guid&guid='.$_REQUEST['guid'], 'text'=>'<img src="images/UI/navbtn_help.gif" border="0" title="View information" />'),
	array('url'=>'?a=interface.container&guid='.$_REQUEST['guid'], 'text'=>'<img src="images/UI/navbtn_explore.gif" border="0" title="Search Object" />')
);

// Dropdown system is mostly based on the interrupt system
if (callEvent('interface.dropdown', $data, $_REQUEST['guid'])) {

	// Build text
	$text = '';
	foreach ($data as $entry) {

		// If we have an URL, display the link
		if (isset($entry['url'])) {
			$text.="<a href=\"javascript:void(0)\" onclick=\"disposeDropDown(); gloryIO('".$entry['url']."');\">".$entry['text']."</a >";

		// If we have only the text, show a text entry
		} elseif (isset($entry['text'])) {
			$text.="<span>".$entry['text']."</span>";
		}
	}

	// Reply data
	$act_result = array(
		'mode' => 'DROPDOWN',
		'text' => "Called by ".$_REQUEST['pos']." and parent ".$_REQUEST['parent']." <br />".$text
	);
	
} else {
	
	// Do nothing if some interrupt returned false
	$act_result = array(
		'mode' => 'DROPDOWN',
		'text' => '<small><em>(Nothing here)</em></small>'
	);
}

?>
