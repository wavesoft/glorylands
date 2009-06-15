<?php
//relayMessage(MSG_INTERFACE,'POPUP','<pre>'.print_r($_REQUEST,true).'</pre>','Dragdrop Debug');

$action = 'CANCEL';
if ($_REQUEST['mode'] == 'move') {

	// Call custom handlers on item.move
	if (callEvent('item.move', $_REQUEST['guid'], $_REQUEST['container'], $_REQUEST['slot'], $_REQUEST['fromslot'], $_REQUEST['fromcontainer'], $_REQUEST['count'], $action)) {	

		// Check for some invalid moves
		if ($_REQUEST['guid'] == $_REQUEST['container']) {
			relayMessage(MSG_INTERFACE, 'MSGBOX', 'You cannot place an object inside itself!');
			$action = 'CANCEL';
		} else {
	
			// If we are moving from/to quickbar, move the item
			if (($_REQUEST['fromcontainer'] == 0) && ($_REQUEST['container'] == 0)) {
				$action='MOVE'; /* Move will now copy the item. The item will be removed after the update */
				$ans=$sql->editRow('mod_quickbar_slots', '`guid` = '.$_REQUEST['guid'].' AND `player` = '.$_SESSION[PLAYER][GUID], array(
					'slot' => $_REQUEST['slot']
				));
								
			// If we are moving from something else into the quick bar, copy the item	
			} elseif ($_REQUEST['container'] == 0) {
				$action='CANCEL'; /* Move will now copy the item. The item will be removed after the update */
				$owner = gl_traceback_owner($_REQUEST['guid']);
				if ($owner != $_SESSION[PLAYER][GUID]) {
					relayMessage(MSG_INTERFACE,'POPUP', "<Table><tr><img src=\"images/UI/msgbox-critical.gif\" /><td></td><td valign=\"top\"> You can only place objects that you own on the quick access bar!</td></tr></table>",'Error');
				} else {
					$sql->query('DELETE FROM `mod_quickbar_slots` WHERE `player` = '.$_SESSION[PLAYER][GUID].' AND `guid` = '.$_REQUEST['guid']);
					$ans=$sql->addRow('mod_quickbar_slots', array(
						'guid' => $_REQUEST['guid'],
						'player' => $_SESSION[PLAYER][GUID],
						'slot' => $_REQUEST['slot']
					));
					qb_update_view();
				}
						
			// If quickbar is not in neither of the previous, do normal move
			} else {

				// Get some default variables			
				$src = gl_get_guid_vars($_REQUEST['guid']);
				$dst = gl_analyze_guid($_REQUEST['container']);
				$vdst = gl_get_guid_vars($_REQUEST['container']);
				$ok = true;

				// Check if the target is player
				// If true, accept only bags for items			
				if ($dst['group'] == 'char') {
					if ($src['class'] != 'CONTAINER') {
						relayMessage(MSG_INTERFACE,'MSGBOX','You can only place bags here!');
						$ok=false;
					}
				} else if ($vdst['class'] == 'CONTAINER') {				
					if (!bag_validate_move($_REQUEST['guid'], $_REQUEST['container'])) {
						$ok=false;
					}
				}
				
				
				// Elseways, perform normal move
				if ($ok) {
					$count = $_REQUEST['count'];
					if ($count<1) $count=1;
					gl_guid_change_parent($_REQUEST['guid'], $_REQUEST['container'], $count, array(
						'slot' => $_REQUEST['slot']
					));
					$action='MOVE';	
				} else {
					$action='CANCEL';
				}
			}
	
		}
		
	}
	
} elseif ($_REQUEST['mode'] == 'remove') {

	// Handle quickbar actions
	if ($_REQUEST['container'] == 0) {
	
		// Remove item
		$sql->query('DELETE FROM `mod_quickbar_slots` WHERE `player` = '.$_SESSION[PLAYER][GUID].' AND `guid` = '.$_REQUEST['guid']);
		$action = 'DELETE';
	
	} else {
	
		// Call custom handlers on item.remove
		if (callEvent('item.remove', $_REQUEST['guid'], $_REQUEST['slot'], $_REQUEST['container'], $_REQUEST['count'])) {
			$action = 'DELETE';
		}

	}
	
} elseif ($_REQUEST['mode'] == 'mix') {

	// Call custom handlers on item.mix
	if (callEvent('item.mix', $_REQUEST['guid'], $_REQUEST['target'], $_REQUEST['slot'], $_REQUEST['container'])) {			
		// Do the default actions
		
		// If the target item is a bag, move the first item into the second
		$dst = gl_get_guid_vars($_REQUEST['target']);
		if ($dst['class'] == 'CONTAINER') {
			if (bag_validate_move($_REQUEST['guid'], $_REQUEST['target'])) {
				$count = $_REQUEST['count'];
				if ($count<1) $count=1;
				//relayMessage(MSG_INTERFACE, 'MSGBOX', 'Moving '.$count.' item(s) starting at '.$_REQUEST['guid'].' into '.$_REQUEST['target']);
				gl_guid_change_parent($_REQUEST['guid'], $_REQUEST['target'], $count);
			}
		}
			
		$action = 'CANCEL';
	}

} elseif ($_REQUEST['mode'] == 'click') {

	// Call custom handlers on item.click
	if (callEvent('item.click', $_REQUEST['guid'], $_REQUEST['slot'], $_REQUEST['container'])) {	
		// Do the default actions, if no handler interrupted us
		
		// Get item information
		$vars = gl_get_guid_vars($_REQUEST['guid']);
		
		// Check for known item types
		if ($vars['class'] == 'CONTAINER') {
			// If the item is container, open it
			gl_do('interface.container', array('guid' => $_REQUEST['guid']));				
		} else {
			gl_do('info.guid', array('guid' => $_REQUEST['guid']));	
		}
		
	}

}

$act_result['mode'] = 'DRAG';
$act_result['action'] = $action;

?>