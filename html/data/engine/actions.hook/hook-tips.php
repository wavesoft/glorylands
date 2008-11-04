<?php

function tipshook_complete_operation($operation) {
	global $sql;
	

	// Only if player has not seen the tips
	if (isset($_SESSION[PLAYER][DATA]['tips'])) {
		
		// Build the tips list
		$tplist = '';
		foreach ($_SESSION[PLAYER][DATA]['tips'] as $index => $active) {
			if ($tplist!='') $tplist.=',';
			$tplist.=$index;
		}
		
		// Get the tips that this operation has triggered
		$sql_ans=$sql->query("SELECT * FROM `data_tips` WHERE `index` IN ({$tplist}) AND `trigger_action` = '{$operation}'");
		if (!$sql_ans) debug_error($sql->getError());
		
		// Check and Display results
		while ($row = $sql->fetch_array_fromresults($sql_ans)) {
		
			// Filter only the tips that follow our parameters:
			$trigger = $row['trigger_request'];
			if ($trigger!='') {
				#1 : Expand trigger variables
				foreach ($_REQUEST as $var => $value) {
					$trigger = str_replace('%'.$var.'%', $value, $trigger);
				}
				foreach ($_SESSION[PLAYER][DATA] as $var => $value) {
					$trigger = str_replace('%'.$var.'%', $value, $trigger);
				}
				
				#2 : Build the token list
				$parts=preg_split('/(and|or|>|<|\!|=)/i',$trigger,-1,PREG_SPLIT_DELIM_CAPTURE);
				array_walk($parts, 'trim');
					debug_message("Parts: ".print_r($parts,true));
				
				#3 : Execute the token check
				$i=0; $ans=true; $phase=0;
				for ($i=0; $i<sizeof($parts); $i++) {
					// perform value checking
					$check=$ans;
					if ($parts[$i+1]=='>') $check=(trim($parts[$i])>trim($parts[$i+2])); 
					if ($parts[$i+1]=='<') $check=(trim($parts[$i])<trim($parts[$i+2]));
					if ($parts[$i+1]=='=') $check=(trim($parts[$i])==trim($parts[$i+2]));
					if ($parts[$i+1]=='!') $check=(trim($parts[$i])!=trim($parts[$i+2]));
					
					debug_message("Testing: [{$parts[$i]}] [{$parts[$i+1]}] [{$parts[$i+2]}]");
					
					// perform and/or
					if ($i>0) {
						if ($parts[$i-1] == 'and') {
							$ans=$ans && $check;
						} elseif ($parts[$i-1] == 'or') {
							$ans=$ans || $check;
						}
						debug_message("Using [{$parts[$i-1]}]");
					} else {
						$ans=$check;
						debug_message("Check is missing, using default");
					}
					
					// Feed result
					$i+=3;
				}
			} else {
				$ans = true;
			}
			
			#4 : Run if true
			if ($ans) {
			
				// Display tip message
				relayMessage(MSG_INTERFACE,'POPUP', '<div style="padding: 5px;">'.$row['tip'].'</div>', 'Tip: '.$row['title']);
				
				// Remove this tip from player stack
				unset($_SESSION[PLAYER][DATA]['tips'][$row['index']]);
				gl_update_guid_vars($_SESSION[PLAYER][GUID], array('tips' => $_SESSION[PLAYER][DATA]['tips']));
		
			}
		}
		
		// If the tips array got emptied, remove it
		if (sizeof($_SESSION[PLAYER][DATA]['tips']) == 0) {
			gl_update_guid_vars($_SESSION[PLAYER][GUID], array('tips' => false));
		}
	}
}

?>