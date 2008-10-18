<?php
/**
  * <h3>Object Spawning System</h3>
  *
  * This file contains all the functions used by the spawning system.
  * The spawning system periodically instance an object on a container specified
  *
  * This file uses the following tables:
  * <ul>
  *   <li>data_spawn 		: Contains the spawn information such as the object to instance, the container etc..</li>
  *   <li>data_spawn_times 	: Contains the information of the already spawned items
  * </ul>
  *
  *
  * @package GloryLands
  * @subpackage Engine
  * @author John Haralampidis <johnys2@gmail.com>
  * @copyright Copyright (C) 2007-2008, John Haralampidis
  * @version 1.2
  */


/**
  * Check and spawn all the items that has to be spawn
  *  
  * This function traverses through the item's parents till the root item is found
  *
  */
function gl_spawn_check() {
	global $sql;
	
	// Select all the expired timeouts
	$ans=$sql->query("SELECT
				`data_spawn`.*
				FROM
				`data_spawn`
				Inner Join `data_spawn_times` ON `data_spawn_times`.`spawn_id` = `data_spawn`.`index`
				WHERE
				`data_spawn_times`.`last_spawn` <= ".time());
	
	if (!$ans) {debug_error($sql->getError()); return false; }
	
	while ($row = $sql->fetch_array_fromresults($ans,MYSQL_ASSOC)) {
	
		// Check if the hit is successfull
		if (gl_chance($row['successrate'])) {
			
			// Get information about the container
			$template = gl_analyze_guid($row['guid']);
			
			// Count the spawned items for this container
			$sql->query("SELECT COUNT(*) FROM `{$template['group']}_instance` WHERE `parent` = ".$row['container']." AND `template` = ".$template['index']);
			$items = $sql->fetch_array(MYSQL_NUM);
			$items = $items[0];
			
			// Check if there are items available
			if ($items < $row['maxitems']) {
			
				// If yes, create new item instance(s) and place them on the container
				$vars = array();
				if ($row['variables']!='') $vars=unserialize($row['variables']);
				$vars['parent'] = $row['container'];
				
				// Loop import sequence till we reach the maximum import entries
				$spawncount = $row['maxitems']-$items;

				if ($spawncount>$row['spawncount']) $spawncount=$row['spawncount'];
				for ($i=1; $i<=$spawncount; $i++) {
					
					// Instance the object
					$obj = gl_instance_object($row['guid'], $vars);

					// Update it's container
					gl_dynupdate_update($row['container']);
					
				}
			}
		}
		
		// Update the row for the next hit
		$sql->editRow('data_spawn_times', '`spawn_id` = '.$row['index'], array(
			'last_spawn' => (time()+(60*$row['delay']))
		));
	
	}
}

?>