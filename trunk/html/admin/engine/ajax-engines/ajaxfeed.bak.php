<?php
global $action,$sql, $res_num, $res_str;

// Parse Variables
$basex = 0;
$basey = 0;
$map = 0;
if (isset($_REQUEST['map'])) {
	$map = $_REQUEST['map'];
}
if (isset($_REQUEST['x'])) {
	$basex = $_REQUEST['x'];
}
if (isset($_REQUEST['y'])) {
	$basey = $_REQUEST['y'];
}
$maxx = $basex+20;
$maxy = $basey+15;
if (isset($_REQUEST['mx'])) {
	$maxx = $_REQUEST['mx'];
}
if (isset($_REQUEST['my'])) {
	$maxy = $_REQUEST['my'];
}

// Get default background
$ans = $sql->query("SELECT * FROM `map_info` WHERE `mapID` = '$map'");
if (!$sql->emptyResults) {
	$row = $sql->fetch_array();
	$defbk = $row['default-background'];
}

// Echo background
$res_str = $defbk;

// Prepare data grid
$gridData = array();

// Load SQL unit info
$ans = $sql->query("SELECT * FROM `units_instances` WHERE `pos_map` = '".$map."' AND `pos_x` >= '$basex' AND `pos_x` <= '$maxx' AND `pos_y` >= '$basey' AND `pos_y` <= '$maxy'");
if (!$ans) {
	$res_num = -1;
	$res_str = "MySQL Error: ".$sql->getError();
}
while ($row = $sql->fetch_array()) {
	if (isset($gridData[$row['pos_x']][$row['pos_y']]['units'])) {
		array_push($gridData[$row['pos_x']][$row['pos_y']]['units'], $row);	
	} else {
		$gridData[$row['pos_x']][$row['pos_y']]['units'] = array($row);
	}
}

// Load SQL grid info
$ans = $sql->query("SELECT * FROM `map_tiles` WHERE `mapID` = '".$map."' AND `x` >= '$basex' AND `x` <= '$maxx' AND `y` >= '$basey' AND `y` <= '$maxy'");
if (!$ans) {
	$res_num = -1;
	$res_str = "MySQL Error: ".$sql->getError();
}
while ($row = $sql->fetch_array()) {
	// Echo cell data
	$res_str .= "|".($row['x']-$basex).",".($row['y']-$basey).",".$row['layer0'].",".$row['layer1'].",".$row['layer2'].",".$unitInfo;
}

?>