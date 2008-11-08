<?php

// Class definition
class mgr_unit {

	// Initialize unit
	// ---------------------------
	// Init string can be:
	//  1) <num>					= Database unit ID
	//  2) "M"						= Current player unit
	//  3) <x>,<y>,<map>			= The player at the given map pos
	
	var $unitID = 0;
	var $instanceRow = array();
	
	function mgr_unit($initstring) {
		
		// fetch the proper data row from the SQL based on the
		// initialization string.
		
		if (is_numeric($initstring)) {
			// Initiated with direct unit GUID
			$this->unitID = $initstring;
			// Get the data row
			$ans=getObject($this->unitID);
			if (!$ans) {debug_error($sql->getError()); return false; }
			$this->instanceRow = $ans;

		} elseif ($initstring == "m") {
			// Initiated with current player Unit ID
			$this->unitID = $_SESSION[PLAYER][GUID];
			
			// Get the data row
			$ans=getObject($this->unitID);
			if (!$ans) { return false; debug_error($sql->getError()); }
			$this->instanceRow = $ans;


		} elseif (substr($initstring,0,1) == "t") {
			// Initiated with template ID
			$this->unitID = 0;
			$initstring = substr($initstring,1);
			
			// Get the data row
			$ans=getObjectTemplate('unit',$initstring);
			if (!$ans) {debug_error($sql->getError()); return false; }
			$this->instanceRow = $ans;
			
		} else {
			// Unit on position : Do we have all the parameters?
			$parm = explode(",",$initstring);
			if (sizeof($parm) != 3) { // No three parameters??? Fail!
				return false;
			}
			// Get the units that fulify the request
			$ans = getObjects('unit', "`{x}` = {$parm[0]} AND `{y}` = {$parm[1]} AND `{map}` = {$parm[2]} LIMIT 0,1");
			if (!$ans) {debug_error($sql->getError()); return false; }
			// Get the first (only) item
			$row=$ans[0];
			$this->instanceRow=$row;
			$this->unitID=$row['guid'];
		}		
	}
	
	// Get display info
	function getInfo() {
		$info = getVarDefinition('unit', $this->instanceRow);
		$detail='';
		foreach ($info as $var) {
			$detail.= $var['name'].": <b>".$var['value']."</b><br>\n";
		}
		return array(
			"title"=>$this->instanceRow['name'], 
			"desc"=>$this->instanceRow['description'],
			"icon"=>$this->instanceRow['icon'],
			"details"=>$detail
		);
	}

}

// Retun information
$inf['class'] = 'mgr_unit';
$inf['name'] = 'unit';
$inf['lib'] = array('instance');
return $inf;

?>