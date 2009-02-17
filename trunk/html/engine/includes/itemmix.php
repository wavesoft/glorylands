<?php

/**
  * <h3>Object parameter manager</h3>
  *
  * This file contains all the functions used by the object handling/mixing system.
  * The object mixing system consists of a smart engine that combines the parameters 
  * between objects creating a new, unique object
  *
  * @package GloryLands
  * @subpackage Engine
  * @author John Haralampidis <johnys2@gmail.com>
  * @copyright Copyright (C) 2007-2008, John Haralampidis
  * @version 0.1
  */

/**
  * Extract the mix data chunk from the object variables given
  *
  * @param array $object_vars 	The object's variables (obdained by gl_get_guid_vars())
  * @return array|bool 			Returns the data chunk in an array format or false if an error occured
  */
function gl_extract_mixdata($object_vars) {
	global $sql;
	
	// Error? Quit..
	if (!$object_vars) return false;
	
	// Check if it has a mixing chunk
	if (isset($object_vars['mixinfo'])) {
		
		// Return the mixing info
		return $object_vars['mixinfo'];
	
	// Try to get the default mixing information for this object	
	} else {
	
		// Find default parameters that are dedicated on this instance
		$template_guid = gl_get_guid_template($object_vars['guid']);
		$ans=$sql->query("SELECT * FROM `data_mix_defaults` WHERE `linkguid` = $template_guid");
		if (!$ans) { debug_error($sql->getError()); return false; }
		
		// If there are not default parameters for this object, return
		// the global default parameters
		if ($sql->emptyResults) {
			$ans = array(
				'group' => 0,
				'data' => array(
				// If you want something, put it here
				// It is blank by default
				)
			);
			return $ans;
		}

		// Fetch the parameters		
		$mixdata = array('group' => 0, 'data' => array());
		while ($row = $sql->fetch_array()) {
			if ($row['type'] == 'GROUP'){
				$mixdata['group'] = $row['typeparm'];			
			} else {
				array_push($mixdata['data'], array(
					'typ' => $row['type'],
					'mod' => $row['typeparm'],
					'ofs' => $row['offset'],
					'grv' => $row['gravity'],
					'drp' => $row['dropchance'],
					'att' => $row['attennuation'],
				));
			}
		}
		
		// Update item parameters
		gl_set_item_mixdata($object_vars['guid'], $mixdata);
		
		// Return tue result
		return $mixdata;
	}
}

/**
  * Obdain the mix data chunk from the GUID given
  *
  * @param int 	$guid 	The object's GUID
  * @return array|bool 	Returns the data chunk in an array format or false if an error occured
  */
function gl_get_item_mixdata($guid) {
	global $sql;

	// Obdain all the object variables
	$object_vars = gl_get_guid_vars($guid);
	
	// And extract only the mix data
	return gl_extract_mixdata($object_vars);
}

/**
  * Save the mix data chunk to the GUID given
  *
  * @param int 	$guid 	The object's GUId
  * @parm array	$data	The data to save into the object
  * @return bool		Returns TRUE if successfull
  */
function gl_set_item_mixdata($guid, $data) {
	// Update data
	$object_vars = array('mixinfo' => $data);
	gl_update_guid_vars($guid, $object_vars);
	return true;
}

/**
  * Create random chances 
  *
  * @param int 	$successrate	The chance value (in percent value)
  * @return bool				Returns TRUE if the chance is taken
  */
function gl_chance($successrate) {
	$id = mt_rand(1, 100000);
	return ($id<=(1000*$successrate));
}

/**
  * Remove the mix data chunk from the GUID given
  *
  * @param int 	$guid 	The object's GUId
  * @return bool		Returns TRUE if successfull
  */
function gl_remove_item_mixdata($guid) {
	// Obdain all the object variables
	$object_vars = gl_get_guid_vars($guid);
	if ($object_vars === false) return false;

	// Update data
	$object_vars['mixinfo'] = false;
	print_r($object_vars['mixinfo']);
	
	gl_update_guid_vars($guid, $object_vars);
	return true;
}

/**
  * Gravity sorting helping function
  *
  * Sorts the array based on each element's 'grv' index 
  *
  * @private
  */
function itemmix_gravity_sort($a, $b) {
    if ($a['grv'] == $b['grv']) {
        return 0;
    }
    return ($a['grv'] < $b['grv']) ? 1 : -1;
}

/**
  * Skill obdaining function
  *
  * @param	int	$guid	The skill GUID
  * @return int			The skill value given by guid
  */
function gl_get_skill_value($guid) {
    return $guid;
}

/**
  * Mix two data chunks and create a new one
  *
  * @param array $obj1 	The first data array
  * @param array $obj2 	The second data array
  * @return array|bool	Returns the new data array or FALSE if the objcet is not mergeable
  */
function gl_mix_data($obj1, $obj2, $skill = 0) {
	global $sql;
	
	// Load the environment parameters for this group mix
	$ans=$sql->query("SELECT `skillguid`,`deftype`,`defgroup`,`droprate`,`skill_min`,`skill_max`,`drop_min`,`drop_max`,`attennuate_min`,`attennuate_max`
					  FROM `data_mix_mixgroups` WHERE `group` = ".$obj1['group']." AND `mixgroup` = ".$obj2['group']);
	if (!$ans) {debug_error($sql->getError()); return false; }
	
	// If there are no data, load defautls
	if ($sql->emptyResults) {
		$environ = array(
			'skillguid' => 0,
			'deftype' => 'JUNK',
			'defgroup' => '0',
			'droprate' => 90,
			'skill_min' => 0,
			'skill_max' => 100,
			'drop_min' => 100,
			'drop_max' => 0,
			'attennuate_min' => 100,
			'attennuate_max' => 90
		);	
	} else {
		$environ = $sql->fetch_array();
	}
	
	// Group Drop Chance Check
	if (gl_chance($environ['droprate'])) {
		return false;
	}
	
	// If we have no skill specified, emulate full-skilled merging
	if ($environ['skillguid'] == 0) {
		$skill = $environ['skill_max'];

	} else {
		// Elseways, get the skill value
		$skill = gl_get_skill_value($skill);
		
		// Make sure the value is in range
		if ($skill>$environ['skill_max']) $skill = $environ['skill_max'];
		if ($skill<$environ['skill_min']) $skill = $environ['skill_min'];
	}
	
	// Calculate some helping variables
	$skrange = $environ['skill_max'] - $environ['skill_min'];
	$skillfactor = ($skill - $environ['skill_min']) / $skrange;
	$attrange = $environ['attennuate_max'] - $environ['attennuate_min'];
	$attbase = $attrange * $skillfactor + $environ['attennuate_min']; ## Default attennuation value ##
	$droprange = $environ['drop_max'] - $environ['drop_min'];
	$skilldrop = $droprange * $skillfactor + $environ['drop_min']; ## Drop rate based on skill ##
	

//	echo "Skillrange = $skrange, Skill Factor = $skillfactor, Attennuation Range = $attrange, AttBase = $attbase, SkillDrop = $skilldrop\n";

	// Skill Drop Chance Check
	if (gl_chance($skilldrop)) {
		return false;
	}
	
	// Prepare result
	$result = array();
	
	// Drop, attennuate and merge parameters of object 1
	foreach ($obj1['data'] as $index => $parm) {
		$import = true;
	
		// Check for variable dropping
		if (gl_chance($parm['drp'])) {
			// Remove variable
			$import = false;
		} elseif (($parm['ofs'] != '') && (is_numeric($parm['ofs']))) {
			// Check and perform offset attennuation
			$value = $parm['ofs'];
			
			// Calculate the attenuation to apply
			$attennuation = $attbase * $parm['att'];
			$attennuation = (float)($attennuation/100);
						
			// Apply attennuation on the offset
			$value += round($value * $attennuation,0);
			
			// If value is too small, drop the variable
			if ($value < 0.5) {
				$import = false;
			} else {
				// Save the value elseways
				$parm['ofs'] = $value;
			}
		}
		
		// Import only the accepted variables
		if ($import) {
//			echo "Accepted item $index ({$parm['typ']},{$parm['mod']}) of obj1\n";
			array_push($result, $parm);
		} else {
//			echo "Item $index ({$parm['typ']},{$parm['mod']}) of obj1 dropped\n";
		}
	}

	// Drop, attennuate and merge parameters of object 2
	foreach ($obj2['data'] as $index => $parm) {
		$import = true;
	
		// Check for variable dropping
		if (gl_chance($parm['drp'])) {
			// Remove variable
			$import = false;
		} elseif (($parm['ofs'] != '') && (is_numeric($parm['ofs']))) {
			// Check and perform offset attennuation
			$value = $parm['ofs'];
			
			// Calculate the attenuation to apply
			$attennuation = $attbase * $parm['att'];
			$attennuation = (float)($attennuation/100);
			
			// Apply attennuation on the offset
			$value += round($value * $attennuation,0);
			
			// If value is too small, drop the variable
			if ($value < 0.5) {
				$import = false;
			} else {
				// Save the value elseways
				$parm['ofs'] = $value;
			}
		}
		
		// Import only the accepted variables
		if ($import) {
//			echo "Accepted item $index ({$parm['typ']},{$parm['mod']}) of obj2\n";
			array_push($result, $parm);
		} else {
//			echo "Item $index ({$parm['typ']},{$parm['mod']}) of obj2 dropped\n";
		}
	}
	
	// Sort variables by gravity
	usort($result, 'itemmix_gravity_sort');
	
	// Remove duplicate entries
	$found = array();
	$wildentries = array(); ## AKA Entries with wildcards :-P
	foreach ($result as $key => $item) {
	
		// Create index based on type
		switch ($item['typ']) {
			
			case "MODIFIER":
			case "TIMEOUT":
			case "DAMAGE":
			case "TRIGGER":
			case "SCRIPT":
				$index = $item['typ']."|".$item['mod']; 
				break;

			default:
				$index = $item['typ'];
		}

		// Check for wildcards (used below)
		if ($item['mod'] == '*') array_push($wildentries, $item['typ']);
		
		// Perform the check (Hold only the first found)
		if (in_array($index, $found)) {
//			echo "Item $key ({$item['typ']},{$item['mod']}) of result dropped as duplicate\n";
			unset($result[$key]);
		} else {
			array_push($found, $index);
		}
	}
	
	// Replace wildcarded entries
	if (sizeof($wildentries)>0) {
		// Make sure only one entry of each kind exists
		array_unique($wildentries);
		
		$removewilds = array();
		$first = array();
		foreach ($result as $key => $item) {
			if (in_array($item['typ'], $wildentries)) {
			
				// First flag
				if (!isset($first[$item['typ']])) $first[$item['typ']]=true;
			
				// This item is not a wildcard?
				if ($item['mod']!='*') {
					// If the first item found is not a wildcard, we
					// abort the wildcard checking and remove the wildcarded entries
					if ($first[$item['typ']]) {
//						unset($wildentries[array_search($item['typ'], $wildentries)]);
						$removewilds[$item['typ']] = true;
					} else {
						// If this is not the first entry then erase what is requested
//						echo "Item $key ({$item['typ']},{$item['mod']}) of result replaced by wildcard\n";
						if (!$removewilds[$item['typ']]) {
//							echo "Item $key ({$item['typ']},{$item['mod']}) of result removed\n";
							unset($result[$key]);
						}
					}	
				} else {
					// If this is wildcard and we are asked to remove all wildcarded
					// entries, remove this entry
					if ($removewilds[$item['typ']]) {
//						echo "Item $key ({$item['typ']},{$item['mod']}) of result removed\n";
						unset($result[$key]);
					}
				}
				
				// Not first any more
				$first[$item['typ']] = false;
			}			
		}		
	}
	
	// Drop unbound timeout entries and 
	// also search if all required elements exist
	$has_group = false;
	$has_info = false;
	foreach ($result as $key => $item) {
	
		// For each TIMEOUT, non-wildcarded entries
		if (($item['typ'] == 'TIMEOUT') && ($item['mod'] != '*')) {
			
			// Check if the appropriate modifier exists
			$found = false;
			foreach ($result as $key2 => $item2) {
				if (($item2['typ'] == 'MODIFIER') && ($item2['mod'] == $item['mod'])) {
					$found = true;
					break;
				}
			}
			
			// And remove the timeout entry if it not exists
			if (!$found) {
//				echo "Item $key ({$item['typ']},{$item['mod']}) of result removed as unbound\n";
				unset($result[$key]);
			}
			
		}
		
		// Check for existing items
		if ($item['typ'] == 'GROUP') $has_group=true;
		if ($item['typ'] == 'CLASS') $has_info=true;
	}
	
	// Append any missing items
	if (!$has_info) {
		array_push($result, array(
			'typ' => 'CLASS',
			'mod' => $environ['deftype'],
			'ofs' => '',
			'grv' => 50,
			'drp' => 50,
			'att' => 0,
		));
	}

	// Return result
	return array('group' => 0, 'data' => $result);
}

/**
  * Convert data chunk into a hash (used for comparison)
  *
  * @param array $data 		The data chunk
  * @parm int $tollerance	The maximum tollerance of offset modifier
  * @return str|bool		Returns the hash or FALSE if there was an error
  */
function gl_mix_hash($data, $tollerance = 2, $unhashed = false) {
	// Data validation
	if (!isset($data['data'])) return false;
	if (!is_array($data['data'])) return false;

	// Calculate the string pattern that will be hashed	
	$hash = '';
	foreach ($data['data'] as $parm) {
		if ($hash!='') $hash.=',';
	
		// Store type and modifier
		$hash .= $parm['typ'].'|'.$parm['mod'].'|';
		
		// Calculate tollerance
		$ofs = $parm['ofs'];
		$ofs = round($ofs / $tollerance, 0) * $tollerance;
		
		// Include into hash
		$hash .= $ofs;
	}
	
	// Summarize into md5
	return $unhashed ? $hash : sha1($hash);
}


/**
  * Visualize a data chunk
  *
  * @param array $data 	The data chunk
  * @return html		Returns the visual representation of the data chunk
  */
function gl_mix_visualize($data, $short = false, $list = false) {
	
	$info = 'Unknown';
	$modifiers = array();
	$glob_timeout = false;
	
	foreach ($data['data'] as $parm) {
		if ($parm['typ'] == 'CLASS') {
			$info = gl_ucfirst(mb_strtolower($parm['mod']));
		} elseif ($parm['typ'] == 'MODIFIER') {
			$modifiers[$parm['mod']]['mod'] = $parm['mod'];
			$modifiers[$parm['mod']]['ofs'] = $parm['ofs'];
		} elseif ($parm['typ'] == 'TIMEOUT') {
			if ($parm['mod'] == '*') {
				$glob_timeout = $parm['ofs'];
			} else {
				$modifiers[$parm['mod']]['mod'] = $parm['mod'];
				$modifiers[$parm['mod']]['tim'] = $parm['ofs'];
			}
		}
	}
		
	
	$ans = "";
	foreach ($modifiers as $mod) {
		if ($mod['ofs'] > 0) {
			if (!$list) {
				if ($ans!='') $ans.=",<br />\n";
			} else {
				$ans.='<li>';
			}
			$ans .= 'Increases <b>'.$mod['mod'].'</b> by '.$mod['ofs'];
			
			if ($glob_timeout) $mod['tim'] = $glob_timeout;
			if (isset($mod['tim'])) {
				$ans .= ' for '.$mod['tim'].' seconds';
			}

			if ($list) $ans.='</li>';

		} elseif ($mod['ofs'] < 0) {
			if (!$list) {
				if ($ans!='') $ans.=",<br />\n";
			} else {
				$ans.='<li>';
			}

			$ans .= 'Decreases <b>'.$mod['mod'].'</b> by '.abs($mod['ofs']);

			if ($glob_timeout) $mod['tim'] = $glob_timeout;
			if (isset($mod['tim'])) {
				$ans .= ' for '.$mod['tim'].' seconds';
			}

			if ($list) $ans.='</li>';
		}
	}
	
	if ($list) $ans="<ul>\n$ans\n</ul>";
	
	if (!$short) {
	
		$ico['Ragent'] = array(
			"Spell_Arcane_Blink.jpg",
			"Spell_Frost_FrostShock.jpg"
		);
	
		$ico['Consumable'] = array(
			"INV_Potion_20.jpg", "INV_Potion_14.jpg",
			"INV_Potion_17.jpg", "INV_Potion_97.jpg",
			"INV_Potion_70.jpg"
		);
		
		$icon = $ico[$info][rand(0, (sizeof($ico[$info])-1))];
		
		$ans="
		<table>
		<tr>
		  <td><img src=\"../images/inventory/$icon\"></td>
		  <td align=\"left\"><h3>$info</h3></td>
		</tr>
		<tr>
		  <td colspan=\"2\">$ans</td>
		</tr>
		</table>
		";

	} else {
		$ans = "$info item that:<br />\n".$ans;
	}
	
	return $ans;
}

/**
  * Check if an item is usable
  *
  * This function attempts to load all the parameters an object contains returns false
  * if they are blank
  *
  * @param int 	$guid 	The object's GUID
  * @return bool 		Returns TRUE if the object contains usable parameters
  */
function gl_item_is_usable($guid) {

	// And the object's mix variables
	$mix_vars = gl_get_item_mixdata($guid);
	
	// Check for errors on data
	if (!$mix_vars) return false;
	if (sizeof($mix_vars['data'])==0) return false;
	
	// No errors found
	return true;
}

/**
  * Use an object
  *
  * This function loads all the parameters an object contains and tries to execute them.
  * It also schedules any timeout effects that have to be done
  *
  * @param int 	$guid 	The object's GUID
  * @param int 	$target	The targeted player's GUID (If omitted, the current user is selected)
  * @return array|bool 	Returns the data chunk in an array format or false if an error occured
  */
function gl_use_item($guid, $target = false) {

	// Get current user ID, if user id is not set
	if ($target === false) {
		if (isset($_SESSION[PLAYER][GUID])) {
			$target = $_SESSION[PLAYER][GUID];
		} else {
			$target = 0;
		}
	}

	// Obdain all the user's physical variables
	$user_vars = gl_get_guid_vars($target);

	// And the object's mix variables
	$mix_vars = gl_get_item_mixdata($guid);
	
	// Calculate what to do
	$modifier = array();
	$damage = array();
	$timeouts = array();
	foreach ($mix_vars['data'] as $parm) {
		switch ($parm['typ']) {
		
			case "MODIFIER":
				$modifier[$parm['mod']] = $parm['ofs'];
				break;

			case "DAMAGE":
				$damage[$parm['mod']] = $parm['ofs'];
				break;

			case "TIMEOUT":
				$timeouts[$parm['mod']] = $parm['ofs'];
				break;
		
		}
	}
	
	// Create scheduled rollback
	$rollback = array();
	$rollback_text = array();
	
	// Apply changes on user variables
	foreach ($modifier as $mod => $offset) {
	
//		echo "Modifying $mod by $offset ";
	
		// Apply offset on modifier
		$user_vars[$mod] += $offset;
		
		// Check if the modifier must be rolled-back
		if (isset($timeouts[$mod]) || isset($timeouts['*'])) {
		
			$tmod = $mod;
			if (isset($timeouts['*'])) $tmod = '*';
		
//			echo "Timeouting it with $tmod at ".$timeouts[$tmod];

			if (isset($rollback[$tmod])) {
				array_push($rollback[$tmod],
					array('mod' => $mod, 'ofs' => -$offset)	
				);
			} else {
				$rollback[$tmod] = array(
					array('mod' => $mod, 'ofs' => -$offset)	
				);
			}			 
			
			// Calculate text to display
			$text = "";
			if (isset($rollback_text[$tmod])) {
				$text = $rollback_text[$tmod].', ';
			}			
			$text.=$mod;
			if ($offset>0) {
				$text.='+ ';
				$text.=$offset;
			} else {
				$text.='- ';
				$text.=-$offset;
			}
			$rollback_text[$tmod] = $text;
		}

//		echo "\n";
	}
	
	// Apply damage calculations
	### IMPLEMENT ###

	// Apply scripts
	### IMPLEMENT ###
	
	// Store scheduled rollbacks
	foreach ($rollback as $id => $data) {
		gl_schedule_event('item.revert', $rollback_text[$id], $timeouts[$id], array('icon' => 'UI/minibox_magic.gif', 'mod'=>$data), $target);
	}
	
	// Store player info back on session and GUId
	gl_update_guid_vars($target, $user_vars);
}

?>