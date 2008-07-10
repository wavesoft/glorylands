<?php

	if ($_SESSION[PLAYER][PROFILE]['level']!='ADMIN') { relayMessage(MSG_INTERFACE,'MSGBOX','Sorry, only administrators can use this function!'); return; };

	if (isset($_REQUEST['guid'])) {
		
		if (!gl_delete_guid($_REQUEST['guid'])) {		
			relayMessage(MSG_INTERFACE,'MSGBOX','Cannot delete GUID '.$_REQUEST['guid']);
		}
		
	}

	gl_do('map.grid.get');

?>