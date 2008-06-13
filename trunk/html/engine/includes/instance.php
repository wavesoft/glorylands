<?php
// ===========================================
//   PROJECT: The Glory Lands MMORPG Game
// ===========================================
//   Version: v0.2 Beta
//      File: Object instancing manager
//                   _______
// _________________| TO DO |_________________
//  -
// ___________________________________________
//   (C) Copyright 2007, John Haralampidis
// ===========================================

// Return true if the guid specified is valid
function gl_guid_valid($guid) {
	$parts = gl_analyze_guid($guid);
	return ($parts['index']>0) && ($parts['group']!=false);
}

// Generate a GUID based on information provided
function gl_make_guid($index, $instance = true, $group = 0) {
	global $GUIDReverseOf;
	$guid = 0;
	
	// Append index
	$v = $index << 8;
	$guid = $guid | $v;
	
	// If group name specified, search for group name
	if (!is_numeric($group)) {
		if (isset($GUIDReverseOf[strtolower($group)])) {
			$group = $GUIDReverseOf[strtolower($group)];
		} else {
			return false;
		}
	}
	
	// Append group
	$v = $group << 1;
	$guid = $guid | $v;
	
	// Append instance
	if ($instance) $guid = $guid | 1;
	
	return $guid;
}

// Analyze a GUID and return it's information
function gl_analyze_guid($GUID) {
	global $GUIDGroupOf;

	// First bit: Instance/Template
	$mode = $GUID & 0x01;
	
	// Remaining first byte: Category ID
	$lo	= ($GUID & 0xFE);
	$lo = $lo >> 1;
	
	// Remaining bytes: Index
	$hi = $GUID >> 8;

	// If a GUID Group name is assigned with this ID,
	// load it
	$guidgroup = false;
	if (isset($GUIDGroupOf[$lo])) $guidgroup = $GUIDGroupOf[$lo];

	// Pack results
	return array(
		'instance' => ($mode == 1),
		'template' => ($mode == 0),
		'index' => $hi,
		'group_id' => $lo,
		'group' => $guidgroup
	);
}

// Obdain a guid's template group
function gl_get_guid_template($guid) {
	global $sql;

	// Analyze guid
	$parts = gl_analyze_guid($guid);
	if (!$parts['group']) return false;	// Group not exists? Cannot continue...
	
	// If GUID is already template, return it
	if ($parts['template']) return $guid;
	
	// Search for guid's template
	$ans = $sql->query("SELECT `temlpate` FROM `{$parts['group']}_template`");
	if (!$ans) return false;
	if ($sql->emptyResults) return false;
	$row = $sql->fetch_array(MYSQL_NUM);	
	
	// Generate and return the GUID
	return gl_make_guid($row[0],false,$parts['group_id']);
}

// Load object data from instance and instanciate a new object
function gl_instance_object($guid, $vars = false) {
	global $sql, $TableInstanceFields;
	
	// Analyze guid
	$parts = gl_analyze_guid($guid);
	if (!$parts['group']) return false;	// Group not exists? Cannot continue...

	// If group is instance, create a copy
	if ($parts['instance']) {
	
		// Clear "instance" flag
		$guid = $guid >> 1;
		$guid = $guid << 1;
	
		// Obdain instance structure
		$ans = $sql->query("SELECT * FROM `{$parts['group']}_instance` WHERE `guid` = $guid");
		if (!$ans) return;
		if ($sql->emptyResults) return;
		$row = $sql->fetch_array(MYSQL_ASSOC);
		
		// Update row entries based on the variables posted
		if (!($vars===false)) {
			foreach ($vars as $vname => $value) {
				$row[$vname] = $value;
			}
		}
		
		// Import everything except the index (It is auto incremented)
		unset($row['index']);
		$sql->addRow($parts['group']."_instance", $row);
		
		// Get the last index inserted
		$ans = $sql->query("SELECT `index` FROM `{$parts['group']}_instance` ORDER BY `index` DESC LIMIT 0,1");
		if (!$ans) return;
		$row = $sql->fetch_array(MYSQL_NUM);
		
		// Update guid
		$guid = gl_make_guid($row[0], false, $parts['group']);
		$ans = $sql->query("UPDATE `{$parts['group']}_instance` SET `guid` = {$guid} WHERE `index` = {$row[0]}");
		if (!$ans) return false;
		
		// Return GUID
		return $guid;
		
	} else {

		// Load guid template
		$ans = $sql->query("SELECT * FROM `{$parts['group']}_template` WHERE `template` = {$parts['index']}");
		if (!$ans) return false;
		if ($sql->emptyResults) return false;

		// Analyze schema and create data to import
		$row = $sql->fetch_array();
		parse_str($row['schema'], $sch_vars);
		
		// Merge/update variables
		if (!$vars) $vars = array();
		$vars = array_merge($sch_vars, $vars);

		// Separate pure variables from  chunked ones
		$v_pure = array();
		$v_cache = array();
		foreach ($vars as $var => $value) {
			// If varisable is inside cache, store in on pure stack
			if (in_array($var, $TableInstanceFields[$parts['group']])) {
				$v_pure[$var] = $value;
			} else {
				$v_cache[$var] = $value;
			}
		}
		
		// Prepare the cache stack
		$v_cache = serialize($v_cache);
		$v_pure['data'] = $v_cache;
		$v_pure['template'] = $parts['index'];
		
		// Import data
		unset($v_pure['index']); /* Just in case it was declared */
		$sql->addRow($parts['group']."_instance", $v_pure);
		
		// Get the last index inserted
		$ans = $sql->query("SELECT `index` FROM `{$parts['group']}_instance` ORDER BY `index` DESC LIMIT 0,1");
		if (!$ans) return;
		$row = $sql->fetch_array(MYSQL_NUM);
		
		// Update guid
		$guid = gl_make_guid($row[0], true, $parts['group']);
		$ans = $sql->query("UPDATE `{$parts['group']}_instance` SET `guid` = {$guid} WHERE `index` = {$row[0]}");
		if (!$ans) return false;
		
		// Return GUID
		return $guid;
		
	}
}

// Decode variables based on index
// Return the decoded variables...
function gl_decode_variable($var,$type,$schema, $default = '') {
	global $sql;
	
	switch ($type) {
	
		case 'RAW':		return $var;
						break;
		
		case 'ALIAS':	parse_str($schema, $values);
						if (!isset($values[strtolower($var)])) {
							return $default;
						} else {
							return $values[strtolower($var)];
						}
						break;
	
		case 'SCRIPT':	return eval($schema);
						break;

		case 'GUID':	$info = gl_get_guid_vars($var);
						return "<a href=\"javascript:display('a=guidinfo&guid=".$var."');\">".$info['name']."</a>";
						break;

		case 'IMAGE':	return "<img src=\"images/".$var."\">".$info['name']."</a>";
						break;

		case 'MONEY':	$gold = floor($var / 10000);
						$silver = floor($var / 100);
						$copper = $var - $gold*1000 - $silver*100;
						return "<span class=\"money_gold\">$gold</span> <span class=\"money_silver\">$silver</span> <span class=\"money_copper\">$copper</span>";

		case 'QUERY':	$ans = $sql->query(str_replace('$var',$var,$schema));
						if (!$ans) fatalError($sql->getError());
						$row = $sql->fetch_array(MYSQL_NUM);
						$s = '';
						foreach ($row as $value) {
							if ($s!='') $s.= ", ";
							$s.=$value;
						}
						return $s;
						break;
	}
}

// Load template variable description and translate variables
// Returns a two-dimensional array containing the detail name and value
function gl_translate_vars($type, $vars) {
	global $sql;
	
	// Initialize result
	$result = array();
	
	// Load template information
	$objects = $sql->query("SELECT * FROM `".$type."_vardesc`");
	if (!$objects) fatalError($sql->getError());
	if ($sql->emptyResults) return false;

	// Create/Translate variables
	while ($row = $sql->fetch_array_fromresults($objects, MYSQL_ASSOC)) {
		
		$info = array();
		if (isset($vars[$row['variable']])) {
		
			// Some sanity checks
			$ok = true;
			if ($row['mode']=='GUID') {
				if (!gl_guid_valid($vars[$row['variable']])) $ok=false;
			}
			
			// Everything ok? Add a respond
			if ($ok) {
				$info['name'] = $row['name'];
				$info['value'] = gl_decode_variable($vars[$row['variable']],$row['mode'],$row['translation'], $row['default']);
				array_push($result, $info);
			}			
			
		} elseif ($row['showmissing'] == 1) {
			$info['name'] = $row['name'];
			$info['value'] = gl_decode_variable($row['default'],$row['mode'],$row['translation'], $row['default']);
			array_push($result, $info);
		}
		
	}
	
	// Return the translated array
	return $result;
}

// Get all the GUID's variables
function gl_get_guid_vars($guid) {
	global $sql;

	// Analyze guid
	$parts = gl_analyze_guid($guid);
	if (!$parts['group']) return false;		// Group not exists? Cannot continue...
	if (!$parts['instance']) return false;	// Is not an instance? Template variables 
											// are not obdained from this function!

	// Get element from group's instance storage
	$ans = $sql->query("SELECT * FROM `{$parts['group']}_instance` WHERE `guid` = {$guid}");
	if (!$ans) return false;
	if ($sql->emptyResults) return false;
	$row = $sql->fetch_array(MYSQL_ASSOC);
	
	// Exclude system fields and expand data field
	$template = $row['template'];
	$data_vars = unserialize($row['data']);
	unset($row['index']);
	unset($row['guid']);
	unset($row['template']);
	unset($row['data']);
	$data_vars = array_merge($row, $data_vars);
	
	// Include template variables
	$ans = $sql->query("SELECT * FROM `{$parts['group']}_template` WHERE `template` = {$template}");
	if (!$ans) return false;
	if ($sql->emptyResults) return false;
	$row = $sql->fetch_array(MYSQL_ASSOC);
	unset($row['template']);
	unset($row['schema']);
	$data_vars = array_merge($row, $data_vars);

	// Return result
	return $data_vars;	
}	

// Update any of the GUID's variables
function gl_update_guid_vars($guid, $vars) {
	global $sql, $TableInstanceFields;

	// Analyze guid
	$parts = gl_analyze_guid($guid);
	if (!$parts['group']) return false;		// Group not exists? Cannot continue...
	if (!$parts['instance']) return false;	// Is not an instance? Template variables 
											// are not obdained from this function!

	// Notify system messenger and abort if canceled
	if (!callEvent('system.guid.update', $guid, $parts['group'], $vars)) return false;
	

	// Get element from group's instance storage
	$ans = $sql->query("SELECT * FROM `{$parts['group']}_instance` WHERE `guid` = {$guid}");
	if (!$ans) return false;
	if ($sql->emptyResults) return false;
	$row = $sql->fetch_array(MYSQL_ASSOC);
	
	// Exclude system fields from variable list
	unset($vars['index']);
	unset($vars['guid']);
	unset($vars['template']);
	unset($vars['data']);
	
	// Expand data variable
	$data_vars = unserialize($row['data']);

	// Create the result array and data chunk
	$import = array();
	foreach ($vars as $var => $value) {
		if (in_array($var ,$TableInstanceFields[$parts['group']])) {
			$import[$var] = $value;
		} else {
			if (($value == '') || ($value === false)) {
				if (isset($data_vars[$var])) unset($data_vars[$var]);
			} else {
				$data_vars[$var] = $value;
			}
		}
	}
	$import['data'] = serialize($data_vars);

	// Notify system messenger for possible final modifications
	callEvent('system.guid.update_end', $guid, $parts['group'], $vars, $import, $row);

	// Import result
	return $sql->editRow("{$parts['group']}_instance", "`guid` = {$guid}", $import);
		
}	

?>
