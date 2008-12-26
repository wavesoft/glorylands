<?php

function admin_hook_dropdown(&$menuitems, &$guid) {
	// Interrupts 'interface.dropdown' event

	// If user is admin, add some advanced menus
	if ($_SESSION[PLAYER][PROFILE]['level'] == 'ADMIN') {
	
		$parent = gl_traceback_owner($guid);
	
		$menuitems[] = array('url'=>'?a=admin.delobj&guid='.$guid, 'text'=>'Drop Item', 'icon'=>'images/UI/piemenu/erase.gif');
		$menuitems[] = array('url'=>'?a=fight.attack', 'text'=>'Attack', 'icon'=>'images/UI/piemenu/copy.gif');
		
	}
	return true;
}

?>