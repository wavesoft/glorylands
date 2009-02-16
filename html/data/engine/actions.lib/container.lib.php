<?php

function bag_validate_move($item, $bag) {
	$src = gl_get_guid_vars($item);
	$idst = gl_analyze_guid($bag);
	$dst = gl_get_guid_vars($bag);

	// If we are placing a bag inside another bag, make sure it is empty
	if (($dst['class'] == 'CONTAINER') && ($src['class'] == 'CONTAINER')) {
		$children = gl_count_guid_children($item, 'item');
		if ($children > 0) {
			relayMessage(MSG_INTERFACE,'MSGBOX','You can only place empty bags inside another ones!');
			return false;
		}
	} else

	// If we are moving an item inside a bag that is inside another bag, deny it				
	if ($dst['class'] == 'CONTAINER') {
		$parent = gl_get_guid_parent($bag);
		if ($parent) {
			$vars = gl_get_guid_vars($parent);						
			if ($vars['class'] == 'CONTAINER') {
				relayMessage(MSG_INTERFACE,'MSGBOX','You cannot use a bag if it is inside another one!');
				return false;
			}
		}					
	}	
	
	// No errors till here, we are ok!
	return true;
}

?>