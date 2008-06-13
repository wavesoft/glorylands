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

// Open the file
$map = new RWmapDB('C:/map'.$map.'.map');
$defbk = $map->mapBack;

// Echo background
$res_str = $defbk;

// Prepare data grid
$gridData = array();

// Read the cells
for ($y = $basey; $y <= $maxy; $y++) {
	for ($x = $basex; $x <= $maxx; $x++) {
		$row = $map->getCell($x,$y);
		if ($row['layer0']!='' || $row['layer1']!='' || $row['layer2']!='') {
			$gridData["$x"]["$y"]['layers'] = $row;
		}
	}
}

/*
echo "<pre>";
print_r($gridData);
echo "</pre>";
*/

// Create result from the grid array
foreach ($gridData as $x => $gridData_x) {
	foreach ($gridData_x as $y => $info) {
		$unitInfo='';
		if (isset($info['units'])) {
			foreach ($info['units'] as $unit) {
				if ($unitInfo!="") $unitInfo.=",";
				$unitInfo.=$unit['UID'];
			}
		}
		$res_str .= "|".($x-$basex).",".($y-$basey).",".$info['layers']['layer0'].",".$info['layers']['layer1'].",".$info['layers']['layer2'].",".$unitInfo;
	}
}

?>