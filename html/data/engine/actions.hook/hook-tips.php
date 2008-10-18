<?php

function tipshook_complete_operation($operation) {
	global $sql;
	
	// Only if player has not seen the tips
	if (isset($_SESSION[PLAYER][DATA]['tips'])) {
		
		// Build the tips list
		$tplist = '';
		foreach ($_SESSION[PLAYER][DATA]['tips'] as $index) {
			if ($tplist!='') $tplist.=',';
			$tplist.=$index;
		}
		
		// Get those tips
		$ans=$sql->query("SELECT * FROM `data_tips` WHERE `index` IN ({$tplist}) AND `trigger_action` = '{$operation}'");
		if (!$ans) debug_error($sql->getError());
			
	}
}

?>