<?php
include_once(DIROF('ACTION.LIBRARY')."/inventory.lib.php");

// Inventory, pickup checks and drag-drop interrupt providing routines

registerEvent('pickuphook_check_compatibility', 'item.pickup');
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

registerEvent('inventory_hook_move', 'item.move');
function inventory_hook_move($guid, $container, $slot, $fromslot, $fromcontainer, $count, &$action) {	
	if ($container == -1) {

		// Check for the validity of the action
		$valid = false;		
		$slot_alias = array('NONE','HEAD','BACK','NECK','AMMO','HAND1','CHEST','HAND2','LEGS','HANDS','FEET','POUCHE');
		
		// Get the item variables
		$vars = gl_get_guid_vars($guid);
		
		if (($vars['equip'] == 'NONE') || ($vars['equip'] == '')) {
			// Can the item be quipped?
			relayMessage(MSG_INTERFACE,'MSGBOX','{#YOU_CAN_NOT_EQUIP_THIS_ITEM#}');
			$action = 'CANCEL';
			return;			
		} elseif ($vars['equip'] == $slot_alias[$slot]) {
			// Can the item be quipped here?
			$valid = true;
		}	

		// Provide an overridable check of item validity
		if (!callEvent('inventory.validate.equip', $guid, $vars, $slot, $valid)) $valid=false;
		
		
		// Update action
		if ($valid) {
			if ($fromcontainer == -1) {
				$action = 'MOVE';
				
				$equip = $_SESSION[PLAYER][DATA]['equip'];
				unset($equip[$slot_alias[$fromslot]]);
				$equip[$slot_alias[$slot]] = $guid;
				gl_update_guid_vars($_SESSION[PLAYER][GUID], array('equip' => $equip));
				
			} else {
				$action = 'COPY';
				
				$equip = $_SESSION[PLAYER][DATA]['equip'];
				$equip[$slot_alias[$slot]] = $guid;
				gl_update_guid_vars($_SESSION[PLAYER][GUID], array('equip' => $equip));
			}
		} else {
			$action = 'CANCEL';
			relayMessage(MSG_INTERFACE,'MSGBOX',"{#YOU_CAN_NOT_EQUIP_THIS_ITEM_HERE#}\n{#THIS_ITEM_IS_EQUIPPED_ONLY_ON#} {#".$vars['equip'].'#}  {#AND_NOT_ON#} {#'.$slot_alias[$slot].'#}');
		}
		
		return false;
	}
}

registerEvent('inventory_hook_remove', 'item.remove');
function inventory_hook_remove($guid,$slot,$container,$count) {
	if ($container == -1) {

		$slot_alias = array('NONE','HEAD','BACK','NECK','AMMO','HAND1','CHEST','HAND2','LEGS','HANDS','FEET','POUCHE');
		$equip = $_SESSION[PLAYER][DATA]['equip'];
		if ($equip[$slot_alias[$slot]] = $guid) {
			unset($equip[$slot_alias[$slot]]);
			gl_update_guid_vars($_SESSION[PLAYER][GUID], array('equip' => $equip));
		}
		
		// Remove the item
		return true;
	}
}

registerEvent('inventory_hook_init', 'interface.main');
function inventory_hook_init() {
	inventory_update_view();
}

?>