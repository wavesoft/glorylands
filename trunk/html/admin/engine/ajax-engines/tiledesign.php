<?php

global $action,$sql, $res_num, $res_str;

$x = $_REQUEST['x'];
$y = $_REQUEST['y'];
$m = $_REQUEST['map'];
$layer = $_REQUEST['level'];
$img = $_REQUEST['img'];
$obj = $_REQUEST['obj'];

if ($action == 'put') {

	$ans = $sql->query("SELECT `index` FROM `map_tiles` WHERE `mapID` = '$m' AND `x` = '$x' AND `y` = '$y'");
	
	// SQL error?
	if (!$ans) {
		$res_num = 1001;
		$res_str = $sql->getError();
		return;
	}
	
	// Does not exist? Create new...
	if ($sql->emptyResults) {
		// Inserting object?
		if (isset($obj)) {
			$sql->query("INSERT INTO `map_tiles` (`mapID`, `x`, `y`, `objectID`) VALUES ('$m','$x','$y','$obj')");
			$res_num=0;
			return;
		// Inserting tile...
		} else {
			$sql->query("INSERT INTO `map_tiles` (`mapID`, `x`, `y`, `layer{$layer}`) VALUES ('$m','$x','$y','$img')");
			$res_num=0;
			return;
		}
	
	// Else.. edit existing...
	} else {
		// Get index
		$index = $sql->fetch_array();
		$index = $index['index'];
		
		// Inserting object?
		if (isset($obj)) {
			$sql->query("UPDATE `map_tiles` SET `objectID` = '$obj' WHERE `index` = '$index'");
			$res_num=0;
			return;
		// Inserting tile...
		} else {
			$sql->query("UPDATE `map_tiles` SET `layer{$layer}` = '$img' WHERE `index` = '$index'");
			$res_num=0;
			return;
		}
	
	}

} elseif ($action == 'put_unit') {

	$ans = $sql->query("SELECT `UID` FROM `units_instances` WHERE `pos_map` = '$m' AND `pos_x` = '$x' AND `pos_y` = '$y'");
	
	// SQL error?
	if (!$ans) {
		$res_num = 1001;
		$res_str = $sql->getError();
		return;
	}

	// Prepare data
	$rowData = array();
	$rowData['pos_map'] = $m;
	$rowData['pos_x'] = $x;
	$rowData['pos_y'] = $y;
	$rowData['map_level'] = $layer;
	$maxVars = "";
	$defVars = "";
	$curVars = "";
	
	foreach ($_REQUEST as $key => $value) {
		// Row Parameter
		if (substr($key,0,2) == 'p_') {
			$rowData[substr($key,2)]=$value;
		// Maximum Value Parameter
		} elseif (substr($key,0,4) == 'max_') {
			if ($maxVars!='') $maxVars.='&';
			$maxVars.=substr($key,4).'='.urlencode($value);
		// Default Value Parameter
		} elseif (substr($key,0,4) == 'def_') {
			if ($defVars!='') $defVars.='&';
			$defVars.=substr($key,4).'='.urlencode($value);
		// Current Value Parameter
		} elseif (substr($key,0,4) == 'cur_') {
			if ($curVars!='') $curVars.='&';
			$curVars.=substr($key,4).'='.urlencode($value);
		}
	}
	
	$rowData['vars_max']=$maxVars;
	$rowData['vars_current']=$curVars;
	$rowData['vars_default']=$defVars;
	
	// Does not exist? Create new...
	if ($sql->emptyResults) {

		$sql->addRow('units_instances', $rowData);
		$res_num=0;
		return;
	
	// Else.. edit existing...
	} else {
		// Get index
		$index = $sql->fetch_array();
		$index = $index['UID'];
		
		// Inserting object?
		$sql->editRow('units_instances', "`UID` = '$index'", $rowData);
		$res_num=0;
		return;
	
	}


} elseif ($action == 'clr') {
	
	// Get entry info
	$ans = $sql->query("SELECT * FROM `map_tiles` WHERE `mapID` = '$m' AND `x` = '$x' AND `y` = '$y'");

	// SQL error?
	if (!$ans) {
		$res_num = 1001;
		$res_str = $sql->getError();
		return;
	}

	// If empty results, quit
	if ($sql->emptyResults) {
		return;
	}	
	$res = $sql->fetch_array(MYSQL_ASSOC);

	// If all entries are empty, remove entry
	// Deleting object?
	if (isset($obj)) {
		$res['objectID']='';
		if (($res['layer0'] == '') && ($res['layer1'] == '') && ($res['layer2'] == '')) {
			$ans = $sql->query("DELETE FROM `map_tiles` WHERE `index` = '".$res['index']."'");
			$res_num=0;
			return;
		}
	// Deleting tile...
	} else {
		$res['layer'.$layer]='';
		if (($res['layer0'] == '') && ($res['layer1'] == '') && ($res['layer2'] == '') && (($res['objectID'] == '') || ($res['objectID'] == 0))) {
			$ans = $sql->query("DELETE FROM `map_tiles` WHERE `index` = '".$res['index']."'");
			$res_num=0;
			return;
		}
	}
	
	// Update the modified entry
	$ans = $sql->editRow('map_tiles', "`index` = '".$res['index']."'", $res);
	//$res_str="<pre>".print_r($res,true)."\n".print_r($_REQUEST,true)."\n".$sql->getError()."</pre>";
	
	// SQL error?
	if (!$ans) {
		$res_num = 1001;
		$res_str = $sql->getError();
		return;
	}
} else {
	$res_num = -1;
	$res_str = "No action specified";
}

?>