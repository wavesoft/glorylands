<?php

/**
  * <h3>Object instancing manager</h3>
  *
  * This file contains all the functions used by the instancing system.
  * The instancing system uses a GUID-Reference system. Every instance in the game as a unique number (GUID).
  * this number contains three parameters: The instancing table prefixes, the reference type and the template 
  * tabe index for the instance.
  *
  * GUID Analysis: 
  * <pre>
  * GUID is a 32-bit integer value. It's bits are the following:
  *    
  *    xxxxxxxxxxxxxxxxxxxxxxxx     nnnnnnn            t
  *     24 bits (16777216 max)  7 bits (128 max)   1 bit (2 max)
  *           ITEM INDEX         CATEGORY INDEX     GUID TYPE
  *
  * GUID Type is: 	0 - for Template reference (used to create instances)
  *	                 1 - for Instance reference (an instanced, playable object)
  *
  * </pre>
  *
  * An instance can store an infinite number of parameters. Some of them are static and template-wide (they are the
  * same for all the instances of a template), others can be can be used in SQL queries and others are just kept in
  * storage. In general:
  * <ul>
  *  <li>The table <i>xxxx_template</i> contains all the template-wide cariables, among with some initialization ones</li>
  *  <li>The table <i>xxxx_instance</i> contains all the variables that can be used in SQL queries</li>
  *  <li>The field `data` of the table <i>xxxx_instance</i> contains the rest of the variables, stored in an serialized format</li>
  * </ul>
  *
  * @package GloryLands
  * @subpackage Engine
  * @author John Haralampidis <johnys2@gmail.com>
  * @copyright Copyright (C) 2007-2008, John Haralampidis
  * @version 1.2
  */

/**
  * Find the parent GUID
  *  
  * Instead of getting all the GUID variables and then isolating the PARENT, this 
  * function quickly detects the item's parent
  *
  * @param int $guid 	The GUID whose parent is to be found
  * @return int 		Returns the parent GUID
  */
function gl_get_guid_parent($guid) {
	global $sql;
	
	// Detect GUID group table
	$info = gl_analyze_guid($guid);

	$ans=$sql->query("SELECT `parent` FROM `{$info['group']}_instance` WHERE `guid` = $guid");
	if (!$ans) return 0;
	if ($sql->emptyResults) return 0;
	
	// Return the parent
	$row = $sql->fetch_array_fromresults($ans,MYSQL_NUM);
	return $row[0];
	
}
/**
  * Find the root GUID
  *  
  * This function traverses through the item's parents till the root item is found
  *
  * @param int $guid 	The source GUID whose root is to be found
  * @return int 		Returns the root GUID
  */
function gl_traceback_owner($guid) {
	$vars = gl_get_guid_vars($guid);
	$owner = $guid;
	
	if (!isset($vars['parent'])) return $owner;
	if ($vars['parent']!=0) {
		$owner = gl_traceback_owner($vars['parent']);
	}
	return $owner;
}

/**
  * Check if the given number is a valid GUID
  *  
  * The check is performed by checking if the number contains an existing 
  * `category index` field, and a  valid `index` field
  *
  * @param int $guid The number to be checked for GUID validity
  * @return bool Returns true if the given number is a valid GUID
  */
function gl_guid_valid($guid) {
	$parts = gl_analyze_guid($guid);
	return ($parts['index']>0) && ($parts['group']!=false);
}

/**
  * Generate a GUID based on information provided
  *
  * @param int $index 		The value of the `index` field of the generated GUID
  * @param bool $instance	If true, defines the GUID as INSTANCE (`type` flag)
  * @param string|int $group	The category name or the category index that will be stored in the `category` field
  * @return int 			The generated GUID
  */
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

/**
  * Analyze a GUID and return it's information
  *
  * Returns an array with the following fields:
  * <ul>
  * <li><b>instance</b> (bool) : True if the GUID is an instance</li>
  * <li><b>template</b> (bool) : True if the GUID is a template</li>
  * <li><b>index</b> (int) : The object index stored in GUID</li>
  * <li><b>group_id</b> (int) : The category field</li>
  * <li><b>group</b> (string) : The category field, translated to string</li>
  * </ul>
  *
  * @param int $guid 		The GUID to analyze
  * @return array			The guid information
  */
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


/**
  * Count the number of the children in that have the same parent GUID
  *  
  * This function automatically detects the instance table by the specified GUID and
  * searches for a matched parent field. If you don't want automatic detection, you
  * can specify a custom group
  *
  * @param int $parent 		The parent GUID whose children are to be counted
  * @param string $group	An optional parameter that specifies the instance group to check for children
  * @return int 			Returns the number of children
  */
function gl_count_guid_children($parent, $group=false) {
	global $sql;
	
	// Detect group if not specified
	if (!$group) {
	
		// Analyze GUID
		$parts = gl_analyze_guid($parent);
		if (!$parts['group']) return false;
		
		// Set group
		$group = $parts['group'];
	
	} 
	
	// Search the GUID table for items
	$ans=$sql->query("SELECT COUNT(*) FROM `{$group}_instance` WHERE `parent` = ".$parent);
	if (!$ans) return false;
	$row=$sql->fetch_array(MYSQL_NUM);
	
	// Return the result
	return $row[0];
}


/**
  * Obdain a guid's template group
  *
  * @param int $guid 		A GUID of an instanced entry
  * @return int 			The GUID of the template entry
  */
function gl_get_guid_template($guid) {
	global $sql;

	// Analyze guid
	$parts = gl_analyze_guid($guid);
	if (!$parts['group']) return false;	// Group not exists? Cannot continue...
	
	// If GUID is already template, return it
	if ($parts['template']) return $guid;
	
	// Search for guid's template
	$ans = $sql->query("SELECT `template` FROM `{$parts['group']}_instance` WHERE `index` = ".$parts['index']);
	if (!$ans) return false;
	if ($sql->emptyResults) return false;
	$row = $sql->fetch_array(MYSQL_NUM);	
	
	// Generate and return the GUID
	return gl_make_guid($row[0],false,$parts['group_id']);
}

/**
  * Create a new instance based on the GUID given
  *
  * If the given GUID is a template, the function will generate a new instance based on 
  * the template. 
  * If the given GUID is an instance, this function will duplicate the instance
  *
  * @param int $guid 		A GUID of an instance or a template entry
  * @param bool $vars		A single-dimensional array that contains the parameter names and values to store into the instance
  * @return int 			The newly generated GUID
  */
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
		if (substr($row,0,2)=='a:') {
			// Serialize mode
			$sch_vars = unserialize($row['schema']);
		} else {
			// URL-Encoded mode
			parse_str($row['schema'], $sch_vars);
		}
		
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

/**
  * Convert a variable into a visual result based on the type and schema provided
  *
  * @param mixed $var 		The variable to convert into visual result
  * @param string $type		The visual convertion mode to use
  * @param string $schema	Supporting information for type
  * @param string $default	The default value to use in case the variable is empty
  * @return string 			A visual result that is ready to be echoed into the browser
  */
function gl_decode_variable($var,$type,$schema,$default = '',$guid) {
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
						return "<a href=\"javascript:display('a=info.guid&guid=".$var."');\">".$info['name']."</a>";
						break;

		case 'IMAGE':	return "<img src=\"images/".$var."\">".$info['name']."</a>";
						break;

		case 'MONEY':	//$gold = floor($var / 10000);
						//$silver = floor($var / 100);
						//$copper = $var - $gold*1000 - $silver*100;
						//return "<span class=\"money_gold\">$gold</span> <span class=\"money_silver\">$silver</span> <span class=\"money_copper\">$copper</span>";
						return "<span class=\"money\">$var</span>";

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

/**
  * Load template variable description and translate variables
  *
  * @param string $type		The GUID category from which to obdain the translation information (*_vardesc table)
  * @param array $vars		The variables to convert (usually obdained from the gl_get_guid_vars()
  * @return array 			A two-dimensional array that contains the parameter name and a visual value
  */
function gl_translate_vars($type, $vars, $guid = 0) {
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
				$info['value'] = gl_decode_variable($vars[$row['variable']],$row['mode'],$row['translation'], $row['default'], $guid);
				array_push($result, $info);
			}			
			
		} elseif ($row['showmissing'] == 1) {
			$info['name'] = $row['name'];
			$info['value'] = gl_decode_variable($row['default'],$row['mode'],$row['translation'], $row['default'], $guid);
			array_push($result, $info);
		}
		
	}
	
	// Return the translated array
	return $result;
}


/**
  * Get all the GUID's parameters
  *
  * @param int $vars			The GUID to read (instance only)
  * @return array|bool		An array that contains the parameter names and a values or false in case of error
  */
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
	//unset($row['guid']);
	unset($row['template']);
	unset($row['data']);
	$instance_vars = $row;
	
	// Include template variables
	$ans = $sql->query("SELECT * FROM `{$parts['group']}_template` WHERE `template` = {$template}");
	if (!$ans) return false;
	if ($sql->emptyResults) return false;
	$row = $sql->fetch_array(MYSQL_ASSOC);
	unset($row['template']);
	unset($row['schema']);
	$template_vars = $row;

	// Return result
	return array_merge($template_vars, $instance_vars, $data_vars);
}	

/**
  * Update any of the GUID's parameters
  *
  * @param int $vars		The GUID to update (instance only)
  * @param array $vars		An array that contains the variables to update and their new value
  * @return bool 			True if successfull, false otherways
  */
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

	// Import result
	$ans = $sql->editRow("{$parts['group']}_instance", "`guid` = {$guid}", $import);

	// Notify system messenger to update any binded information
	callEvent('system.guid.update_end', $guid, $parts['group'], $vars);
	return $ans;		
}	

/**
  * Delete an instance
  *
  * @param int $guid 		The GUID to delete (instance only)
  * @return bool 			Returns TRUE if deletion was successfull;
  */
function gl_delete_guid($guid) {
	global $sql, $TableInstanceFields;

	// Analyze guid
	$parts = gl_analyze_guid($guid);
	if (!$parts['group']) return false;		// Group not exists? Cannot continue...
	if (!$parts['instance']) return false;	// Is not an instance? Template variables 

	// Notify plugins that this GUID is deleted
	callEvent('system.guid.deleted', $guid);

	// Delete the table
	$ans = $sql->query("DELETE FROM `{$parts['group']}_instance` WHERE `guid` = {$guid}");
	if (!$ans) return false;
	if ($sql->affectedRows == 0)  return false;
		
	return true;

}


/** Some Bag Groups */
define('BAGS_INVENTORY', 1000);
define('BAGS_KEYRING',   1001);
define('BAGS_SAFE',      1002);
define('BAGS_SHOP',      1003);
define('BAGS_LOOT',      1004);

/**
  * Get User's bag(s) GUID
  *
  * @param int $bag			The bag index or the bag group index from wich to obdain the bags GUID
  * @return array			The bag(s) requested
  *
  */

function gl_get_user_bags($bag = 0) {

}

?>
