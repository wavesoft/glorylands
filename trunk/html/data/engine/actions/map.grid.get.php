<?php

define("GRID_W", 24);
define("GRID_H", 16);

function push_flatten_layer($x,$y,$z,$image) {
	global $grid;
	$depth = $z * 10 + 5;
	while (isset($grid[$y][$x][$depth])) {
		$depth++;
	}
	$grid[$y][$x][$depth] = $image;
	ksort($grid[$y][$x]);
}

function push_object($px,$py,$pz,&$o) {
	$bx = $o->bindX;
	$by = $o->bindY;
	for ($y=0;$y<$o->height;$y++) {	
		for ($x=0;$x<$o->width;$x++) {
			if ($o->grid[$x][$y]!='') {
				
				push_flatten_layer($px+$x-$bx, 
						   $py+$y-$by+1, 
						   $pz+($by-$y)-1,
						   $o->grid[$x][$y]
						   );

			}
		}
	}
}

function push_info($x, $y, $type, $guid, $details, $w=1, $h=1, $bx=0, $by=0) {
	global $nav_grid, $act_result;
	
	// Prepare data
	$data = array('t'=>$type,'g'=>$guid,'d'=>$details);

	// If dictionary is not built already, build it now
	if (!isset($nav_grid['dic'])) $nav_grid['dic']=array();
	
	// Put data on dictionary and return reference ID
	$id = sizeof($nav_grid['dic']);
	$nav_grid['dic'][$id] = $data;
	
	// Fill information with data reference ID
	$act_result['debug'] = "$x,$y | $bx,$by | $w,$h";
	for ($ix = $x-$bx; $ix<$x-$bx+$w; $ix++) {
		if (!isset($nav_grid[$ix])) $nav_grid[$ix]=array();
		for ($iy = $y-$by; $iy<$y-$by+$h; $iy++) {
			$nav_grid[$ix][$iy] = $id;
		}
	}
}

// =========================== END OF DEFINITIONS ==============================

$Gx=5; $Gy=5; $map=1; $quick=false;
if (isset($_SESSION[PLAYER][DATA]['x'])) $Gx = $_SESSION[PLAYER][DATA]['x'];
if (isset($_SESSION[PLAYER][DATA]['y'])) $Gy = $_SESSION[PLAYER][DATA]['y'];
if (isset($_SESSION[PLAYER][DATA]['map'])) $map = $_SESSION[PLAYER][DATA]['map'];
if (isset($_REQUEST['x'])) $Gx = $_REQUEST['x'];
if (isset($_REQUEST['y'])) $Gy = $_REQUEST['y'];
if (isset($_REQUEST['map'])) $map = $_REQUEST['map'];
if (isset($_REQUEST['quick'])) $quick=($_REQUEST['quick']=='1');

// Raise Move Event if not in quick mode
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
	//$_SESSION[PLAYER][DATA]['x'] = $Gx;
	//$_SESSION[PLAYER][DATA]['y'] = $Gy;
	//$_SESSION[PLAYER][DATA]['map'] = $map;
	gl_update_guid_vars($_SESSION[PLAYER][GUID], array('x'=>$Gx,'y'=>$Gy,'map'=>$map));
}

// Prepare Grid
$grid = array();
$nav_grid = array();
$sql->query("SELECT * FROM `data_maps` WHERE `index` = $map");
$map_info = $sql->fetch_array();

// Load objects from data grid
$basex = $Gx-GRID_W/2;
$basey = $Gy-GRID_H/2;
$xw = $basex+GRID_W;
$yh = $basey+GRID_H;

// Model Cache
$models = array();

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
	if (!isset($models[$row['model']])) $models[$row['model']] = new mapobj('data/models/'.$row['model']);
	push_object($row['x'], $row['y'], $row['z'],$models[$row['model']]);
	push_info($row['x'], $row['y'],'GOB',$row['guid'],array('name'=>$row['name'],'subname'=>$row['subname'], 'icon'=>$row['icon'], 'flags'=>$row['flags']), 
			  $models[$row['model']]->width, $models[$row['model']]->height, $models[$row['model']]->bindX, $models[$row['model']]->bindY);
}

// Get NPCs
$ans=$sql->query("SELECT
	`npc_instance`.`guid`,
	`npc_instance`.`x`,
	`npc_instance`.`y`,
	`npc_instance`.`map`,
	`npc_instance`.`model`,
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
	if (!isset($models[$row['model']])) $models[$row['model']] = new mapobj('data/models/'.$row['model']);
	push_object($row['x'], $row['y'],0,$models[$row['model']]);
	push_info($row['x'], $row['y'],'NPC',$row['guid'],array('name'=>$row['name'], 'icon'=>$row['icon'], 'flags'=>$row['flags']),
			  $models[$row['model']]->width, $models[$row['model']]->height, $models[$row['model']]->bindX, $models[$row['model']]->bindY);

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
	if (!isset($models[$row['model']])) $models[$row['model']] = new mapobj('data/models/'.$row['model']);
	push_object($row['x'],$row['y'],0,$models[$row['model']]);
	push_info($row['x'], $row['y'],'CHR',$row['guid'],array('name'=>$row['name'], 'icon'=>$row['icon'], 'flags'=>$row['flags']),
			  $models[$row['model']]->width, $models[$row['model']]->height, $models[$row['model']]->bindX, $models[$row['model']]->bindY);
	//relayMessage(MSG_INTERFACE,'MSGBOX',print_r($models[$row['model']],true));		
}

// ############ OBSOLETED ##################
//$char=$_REQUEST['char'];
//if (!$char && isset($_SESSION[DATA]['model'])) $char=$_SESSION[DATA]['model'];
//if (!$char) $char='faux-choque.o';
//$o_char = new mapobj('data/models/'.$char);
//push_object($Gx,$Gy+1,0,$o_char);
// ###########################################

// If cached ZID is not the one current grid has, reload it (if not in quick mode)
if (!$quick) {
	if ($_SESSION['GRID']['ID'] != $map) {
		if (file_exists(DIROF('DATA.MAP').$map_info['filename'].'.zmap')) {
			// Raise Update grid Event
			//$grid = unserialize(file_get_contents($_CONFIG[GAME][BASE].'/data/maps/'.$map_info['filename'].'.zmap'));		
			$_SESSION['GRID']['ZID'] = unserialize(file_get_contents(DIROF('DATA.MAP').$map_info['filename'].'.zmap'));
			callEvent('map.updategrid', $_SESSION['GRID']['ZID'], $map_info['filename']);
		}
		$_SESSION['GRID']['ID'] = $map;
	}
}

// Notify infogrid on linked modules
callEvent('map.infogrid', $nav_grid, $map_info['filename']);

// Stack some tests
/*
	chunk.color = (str) [x,y]	: Display icons with the given color on given locations
	chunk.x.m	= (int)			: Minimum X Value
	chunk.x.M	= (int)			: Maximum X Value
	chunk.y.m	= (int)			: Minimum Y Value
	chunk.y.M	= (int)			: Maximum Y Value
	chunk.center.x = (int)		: Center X offset
	chunk.center.y = (int)		: Center Y offset	
*/

/*
$obj = array(
	'grid' => array(
		0 => array(
			array('c'=>'#33FF00', 'i'=>1),
			array('c'=>'#00FF00', 'i'=>2, 't'=>'&uarr;'),
			array('c'=>'#33FF00', 'i'=>3)
			),
		1 => array(
			array('c'=>'#00FF00', 'i'=>4, 't'=>'&larr;'),
			array(),
			array('c'=>'#00FF00', 'i'=>6, 't'=>'&rarr;')
			),
		2 => array(
			array('c'=>'#33FF00', 'i'=>7),
			array('c'=>'#00FF00', 'i'=>8, 't'=>'&darr;'),
			array('c'=>'#33FF00', 'i'=>9)
			)
	),
	'x' => array('m'=>0, 'M'=>2),
	'y' => array('m'=>0, 'M'=>2),
	'center' => array('x' => $_SESSION[PLAYER][DATA]['x']-1 , 'y' => $_SESSION[PLAYER][DATA]['y']-1),
	'show' => array('x' => $_SESSION[PLAYER][DATA]['x'] , 'y' => $_SESSION[PLAYER][DATA]['y']),
	'action' => 'map.grid.get'
);
relayMessage(MSG_INTERFACE, 'RANGE', $obj);
*/

callEvent('map.render');

if (!$quick) {
	// Return result
	$act_result = array_merge($act_result, array(
			'mode' => 'GRID',
			'data' => $grid,
			'nav' => $nav_grid,
			'map' => $map_info['filename'],
			'head_image'=>'UI/navbtn_help.gif', 
			'head_link'=>'?a=interface.book&book='.$map_info['index'],
			'title'=>$map_info['name'],
			'background'=>$map_info['background'],
			'x' => $Gx,
			'y' => $Gy
	));
} else {
	// Return result
	$act_result = array_merge($act_result, array(
			'mode' => 'GRID',
			'data' => $grid,
			'nav' => $nav_grid,
			'map' => $map_info['filename'],
			'head_image'=>'UI/navbtn_help.gif', 
			'head_link'=>'?a=interface.book&book='.$map_info['index'],
			'title'=>$map_info['name'],
			'background'=>$map_info['background'],
			'x' => $Gx,
			'y' => $Gy
	));
}

?>