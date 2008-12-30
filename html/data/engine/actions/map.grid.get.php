<?php

define("GRID_W", 24);
define("GRID_H", 16);

$Gx=5; $Gy=5; $map=1; $quick=false;
if (isset($_SESSION[PLAYER][DATA]['x'])) $Gx = $_SESSION[PLAYER][DATA]['x'];
if (isset($_SESSION[PLAYER][DATA]['y'])) $Gy = $_SESSION[PLAYER][DATA]['y'];
if (isset($_SESSION[PLAYER][DATA]['map'])) $map = $_SESSION[PLAYER][DATA]['map'];
if (isset($_REQUEST['x'])) $Gx = $_REQUEST['x'];
if (isset($_REQUEST['y'])) $Gy = $_REQUEST['y'];
if (isset($_REQUEST['map'])) $map = $_REQUEST['map'];
if (isset($_REQUEST['quick'])) $quick=($_REQUEST['quick']=='1');

// Raise Move Event if not in quick mode
// NOTE: Quick mode is used only when we need update without calling all the move triggers
if (!$quick) {

	if (($_SESSION[PLAYER][DATA]['x']!=$Gx) || ($_SESSION[PLAYER][DATA]['y']!=$Gy)) {
		// Notify Grid alteration
		callEvent('grid.alter', $_SESSION[PLAYER][GUID], $_SESSION[PLAYER][DATA]['x'], $_SESSION[PLAYER][DATA]['y'], $_SESSION[PLAYER][DATA]['map']); // Missing object here
		callEvent('grid.alter', $_SESSION[PLAYER][GUID], $Gx, $Gy, $map); // New object appeared here
	}

	if (!callEvent('map.move', $_SESSION[PLAYER][GUID], 
			$_SESSION[PLAYER][DATA]['x'], 
			$_SESSION[PLAYER][DATA]['y'], 
			$_SESSION[PLAYER][DATA]['map'], 
			$Gx, $Gy, $map)) {
	
		// Our move is cancelled? Display nothing, but send messages		
		die(json_encode(array(
			'mode'=>'NONE', 
			'messages'=>jsonPopMessages(MSG_INTERFACE)
		)));		
	}
	
	// Update player information	
	gl_update_guid_vars($_SESSION[PLAYER][GUID], array('x'=>$Gx,'y'=>$Gy,'map'=>$map));
}

// Prepare Object cache
$objects = array();
$sql->query("SELECT * FROM `data_maps` WHERE `index` = $map");
$map_info = $sql->fetch_array();

// Load objects from data grid
$basex = $Gx-GRID_W/2;
$basey = $Gy-GRID_H/2;
$xw = $basex+GRID_W;
$yh = $basey+GRID_H;

// Get Game Objects
$ans=$sql->query("SELECT
	`gameobject_instance`.`guid`,
	`gameobject_instance`.`x`,
	`gameobject_instance`.`y`,
	`gameobject_instance`.`z`,
	`gameobject_instance`.`map`,
	`gameobject_instance`.`model`,
	`gameobject_instance`.`name`,
	`gameobject_template`.`subname`,
	`gameobject_template`.`icon`,
	`gameobject_template`.`flags`
	FROM
	`gameobject_instance`
	Inner Join `gameobject_template` ON `gameobject_instance`.`template` = `gameobject_template`.`template`
	WHERE
	`gameobject_instance`.`visible` =  1 AND
	(`gameobject_instance`.`x` > $basex) AND (`gameobject_instance`.`x` <  $xw) AND 
	(`gameobject_instance`.`y` > $basey) AND (`gameobject_instance`.`y` <  $yh) AND 
	`gameobject_instance`.`map` = $map
");
while ($row = $sql->fetch_array_fromresults($ans,MYSQL_ASSOC)) {
	$objects[] = array(
		// Storage/Information variables
		'guid' => $row['guid'],
		'name' => $row['name'],
		'subname' => $row['subname'],
		'icon' => $row['icon'],
		'flags' => $row['flags'],
		
		// GL Map render variables
		'x' => $row['x'],
		'y' => $row['y']+1,
		'image' => 'elements/'.$row['model'],
		'id' => $row['guid'],
		'dynamic' => true,
		'fx_move' => 'fade',
		'fx_show' => 'fade',
		'fx_hide' => 'fade'
	);
}

// Get NPCs
$ans=$sql->query("SELECT
	`npc_instance`.`guid`,
	`npc_instance`.`x`,
	`npc_instance`.`y`,
	`npc_instance`.`map`,
	`npc_instance`.`model`,
	`npc_template`.`name`,
	`npc_template`.`icon`,
	`npc_template`.`flags`
	FROM
	`npc_instance`
	Inner Join `npc_template` ON `npc_instance`.`template` = `npc_template`.`template`
	WHERE
	`npc_instance`.`visible` =  1 AND
	(`npc_instance`.`x` > $basex) AND (`npc_instance`.`x` <  $xw) AND 
	(`npc_instance`.`y` > $basey) AND (`npc_instance`.`y` <  $yh) AND 
	`npc_instance`.`map` = $map
");
while ($row = $sql->fetch_array_fromresults($ans,MYSQL_ASSOC)) {
	$myobj = array(
		'guid' => $row['guid'],
		'x' => $row['x'],
		'y' => $row['y']+1,
		'image' =>'chars/'. $row['model'],
		'name' => $row['name'],
		'icon' => $row['icon'],
		'flags' => $row['flags'],
		'id' => $row['guid'],
		'dynamic' => true,
		'fx_move' => 'slide',
		'fx_show' => 'fade',
		'fx_hide' => 'fade'
	);
	$details = gl_get_guid_vars($row['guid']);
	if (strstr($details['flags'],'VENDOR')) {
		$myobj['name'] = '<font color="gold">'.$row['name'].'</font>';
		$myobj['subname'] = '(Click to trade)';
		$myobj['click'] = '?a=merchant.buy&guid='.$row['guid'];
	}
	
	$objects[] = $myobj;
}

// Get Players
$ans=$sql->query("SELECT
	`char_instance`.`guid`,
	`char_instance`.`x`,
	`char_instance`.`y`,
	`char_instance`.`map`,
	`char_instance`.`model`,
	`char_instance`.`name`,
	`char_template`.`icon`,
	`char_template`.`flags`
	FROM
	`char_instance`
	Inner Join `users_accounts` ON `char_instance`.`account` = `users_accounts`.`index`
	Inner Join `char_template` ON `char_instance`.`template` = `char_template`.`template`
	WHERE
	`users_accounts`.`online` =  1 AND
	`char_instance`.`online` =  1 AND
	`char_instance`.`visible` =  1 AND
	(`char_instance`.`x` > $basex) AND (`char_instance`.`x` <  $xw) AND 
	(`char_instance`.`y` > $basey) AND (`char_instance`.`y` <  $yh) AND 
	`char_instance`.`map` = $map
");
while ($row = $sql->fetch_array_fromresults($ans,MYSQL_ASSOC)) {
	$myobj = array(
		'guid' => $row['guid'],
		'x' => $row['x'],
		'y' => $row['y']+1,
		'image' => 'elements/'.$row['model'],
		'name' => $row['name'],
		'icon' => $row['icon'],
		'flags' => $row['flags'],
		'subname' => '<img src="images/'.$row['icon'].'" />',
		'id' => $row['guid'],
		'dynamic' => true,
		'fx_show' => 'fade',
		'fx_move' => 'slide',
		'fx_hide' => 'fade'
	);
	
	// Focus on the player's object
	if ($row['guid']==$_SESSION[PLAYER][GUID]) {
		$myobj['focus'] = true;
		$myobj['player'] = true;
	}
	$objects[] = $myobj;
}

// If cached collision grid is not the one current grid has, reload it (if not in quick mode)
if (!$quick) {
	if ($_SESSION['GRID']['ID'] != $map) {
		if (file_exists(DIROF('DATA.MAP').$map_info['filename'].'.zmap')) {
			// Update collision grid
			$data = unserialize(file_get_contents(DIROF('DATA.MAP').$map_info['filename'].'.zmap'));
			if (!is_array($data)) {
				echo "<h1>Data:<pre>".print_r($data,true)."</pre></h1>";
			} else {
				$_SESSION['GRID']['ZID'] = $data;
				callEvent('map.updategrid', $_SESSION['GRID']['ZID'], $map_info['filename']);
			}
		}
		$_SESSION['GRID']['ID'] = $map;
	}
}

// Notify infogrid on linked modules
callEvent('map.infogrid', $nav_grid, $map_info['filename']);

// Prepare final data and perform the appropriate notifications
$data = array(
	'objects' => $objects,
	'map' => $map_info['filename'],
	'title'=>$map_info['name'],
	'x' => $Gx,
	'y' => $Gy
);
callEvent('map.render', $data);

// Store the results
$data['mode']='GRID';
$act_result = array_merge($act_result, $data);

?>