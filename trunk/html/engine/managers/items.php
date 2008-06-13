<?php

// Class definition
class mgr_items {
	
	// Initialize item
	// --------------------------
	// Initialization string is the item index on database	
	function mgr_items($parm) {
		global $sql;
		if (is_numeric($parm)) {
			// It is unit ID
			$sql->query("SELECT * FROM `item_template` WHERE `index` = $parm");
		} else {
			// It is unit name
			$sql->query("SELECT * FROM `item_template` WHERE `name` = '$parm'");
		}
	}

}

// Retun information
$inf['class'] = 'mgr_items';
$inf['name'] = 'item';
return $inf;

?>