<?php

function inventory_update_view() {
	global $sql;

	$slot = array();

	$equip = $_SESSION[PLAYER][DATA]["equip"];
	if (!isset($equip)) return;
	if (sizeof($equip) == 0) return;

	$slot_alias = array('NONE','HEAD','BACK','NECK','AMMO','HAND1','CHEST','HAND2','LEGS','HANDS','FEET','POUCHE');
	foreach ($equp as $slot => $guid) {
		$id = array_search($slot);
		if ($id) {
			$vars = gl_get_guid_vars($guid);
			$slot[$id] = array(
				'image' => 'images/'.$vars['icon'], 
				'guid' => $guid, 
				'name' => $vars['name'],
				'tip' => '<b>'.$vars['name'].'</b><br />'.$vars['description']			
			);
		}
	}		
			
	// Feed data
	relayMessage(MSG_INTERFACE, 'MSGBOX', print_r($slot,true));
	relayMessage(MSG_INTERFACE, 'INVENTORY', $slot);
}

?>