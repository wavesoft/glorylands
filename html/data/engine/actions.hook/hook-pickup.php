<?php

function pickuphook_check_compatibility($source, $dest) {
	global $sql;
	
	$src = gl_get_guid_vars($source);
	$dst = gl_get_guid_vars($dest);
	$src_parm = gl_analyze_guid($source);

	if (($dst['class'] == 'CONTAINER') && ($src['class'] == 'CONTAINER')) {
		relayMessage(MSG_INTERFACE,'MSGBOX','You cannot place a container inside another container!');
		return false;
	} elseif ($src_parm['group'] == 'char') {
		relayMessage(MSG_INTERFACE,'MSGBOX','You cannot pick up a player!');
		return false;
	}

	return true;
}

?>