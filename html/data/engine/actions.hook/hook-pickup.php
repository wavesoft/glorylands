<?php

function pickuphook_check_compatibility($source, $dest) {
	global $sql;
	
	$src = gl_get_guid_vars($source);
	$dst = gl_get_guid_vars($dest);
	$src_parm = gl_analyze_guid($source);
	$dst_parm = gl_analyze_guid($dest);
	$src_owner = gl_traceback_owner($source);
	$dst_owner = gl_traceback_owner($dest);
	$src_owner_parm = gl_analyze_guid($src_owner);

	if (($dst['class'] == 'CONTAINER') && ($src['class'] == 'CONTAINER')) {
		relayMessage(MSG_INTERFACE,'MSGBOX','You cannot place a container inside another container!');
		return false;
	} elseif ($src['group'] == 'char') {
		relayMessage(MSG_INTERFACE,'MSGBOX','You cannot pick up a player!');
		return false;
	} elseif (($src_owner_parm['group'] == 'char') && ($src_owner != $_SESSION[PLAYER][GUID])) {
		relayMessage(MSG_INTERFACE,'MSGBOX','You take another player\'s item!');
		return false;		
	}

	return true;
}

?>