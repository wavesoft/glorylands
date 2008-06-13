<?php

// Obdain current player's inventory GUID
$pvars = gl_get_guid_vars($_SESSION[PLAYER][GUID]);
if ($pvars) {
	gl_do('interface.container', array('guid'=>$pvars['inventory_bag']));
}
?>