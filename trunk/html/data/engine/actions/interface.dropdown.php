<?php

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
		'text' => $text
	);
	
} else {
	
	// Do nothing if some interrupt returned false
	$act_result = array(
		'mode' => 'DROPDOWN',
		'text' => '<small><em>(Nothing here)</em></small>'
	);
}

?>
