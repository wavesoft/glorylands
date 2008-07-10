<?php

function admin_hook_dropdown(&$menuitems, &$guid) {
	// Interrupts 'interface.dropdown' event

	// If user is admin, add some advanced menus
	if ($_SESSION[PLAYER][PROFILE]['level'] == 'ADMIN') {
	
		$menuitems[] = array('url'=>'?a=admin.delobj&guid='.$guid, 'text'=>'<img src="images/UI/navbtn_abort.gif" border="0" title="Drop Item" />');
		
	}
	return true;
}

?>