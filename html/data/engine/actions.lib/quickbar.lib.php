<?php

function qb_update_hostview($host, $viewmode) {
	gl_do($viewmode, array('guid' => $host));
}

function qb_update_view() {
	global $sql;
	
	$slot = array();
	for ($i=1; $i<=21; $i++) {
		$slot[$i]=array();
	}
	
	$ans=$sql->query("SELECT * FROM `mod_quickbar_slots` WHERE `player` = ".$_SESSION[PLAYER][GUID]);
	while ($row = $sql->fetch_array_fromresults($ans)) {
		$vars = gl_get_guid_vars($row['guid']);
		$slot[$row['slot']] = array(
			'image' => 'images/'.$vars['icon'], 
			'guid' => $row['guid'], 
			'name' => $vars['name'],
			'tip' => '<b>'.$vars['name'].'</b><br />'.$vars['description']
		);
	}
		
	// Feed data
	relayMessage(MSG_INTERFACE, 'QBAR', $slot);
}

?>