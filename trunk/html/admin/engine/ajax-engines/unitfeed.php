<?php
global $action,$sql, $res_num, $res_str, $_CONFIG;

function tripleInfo($what, $firstTime, $secondTime, $thirdTime) {
	return ($firstTime?$what:"")."*".($secondTime?$what:"")."*".($thirdTime?$what:"");
}

function dirAndReturn($search, $fileTypes) {
	global $_CONFIG;
	$ans = "";
	$d = dir($_CONFIG[GAME][BASE].'/'.$search);
	while (false !== ($entry = $d->read())) {
		$fileparts = pathinfo($entry);
		if (in_array(strtolower($fileparts['extension']), $fileTypes)) {
			if ($ans!="") $ans.=":";
			$ans.=$entry."=".($search."/".$entry);
		}
	}
	$d->close(); 
	return $ans;
}

if ($action == 'typeinfo') {

	// Require type name definition
	if (isset($_REQUEST['type'])) {
		 $uType = $_REQUEST['type'];
	} else {
		$res_num = -1;
		$res_str = "TypeName not specified";
		return;
	}
	
	// Get all the entries for this file type
	$ans = $sql->query("SELECT * FROM `units_dev_variables` WHERE `typeName` = '".$uType."'");
	
	// Cache some results to gain some speed
	$dircache = array();
	
	while ($row = $sql->fetch_array_fromresults($ans)) {
		
		if ($res_str != "") $res_str.="|";
		
		$res_str.=$row['name']."*";
		$resType='';
		
		if (($row['vartype'] == 'INTEGER') || ($row['vartype'] == 'STRING')) {
			$resType = 'TEXT';
		} else if ($row['vartype'] == 'CHECK') {
			$resType = 'CHECK';
		} else if ($row['vartype'] == 'IMAGE') {
			// Cache some dir results if they are not already cached
			if (!isset($dircache[$row['typeBaseDir']])) {
				$dircache[$row['typeBaseDir']] = dirAndReturn($row['typeBaseDir'], array("gif","png","jpg","bmp","tif","tiff"));
			} 
			$resType .= 'IMGCOMBO:'.$dircache[$row['typeBaseDir']];
		} else if ($row['vartype'] == 'SCRIPT') {
			// Cache some dir results if they are not already cached
			if (!isset($dircache[$row['typeBaseDir']])) {
				$dircache[$row['typeBaseDir']] = dirAndReturn($row['typeBaseDir'], array("php"));
			} 
			$resType .= 'COMBO:'.$dircache[$row['typeBaseDir']];
		} else if ($row['vartype'] == 'COMBO') {
			$resType = '';
			$names = explode(",",$row['typeListNames']);
			$values = explode(",",$row['typeListValues']);
			for ($i=0;$i<sizeof($names);$i++) {
				if ($resType!="") $resType.=":";
				$resType.=$names[$i]."=".$values[$i];
			}
			$resType = 'COMBO:'.$resType;
		} else if ($row['vartype'] == 'LIST') {
			$resType = '';
			$names = explode(",",$row['typeListNames']);
			$values = explode(",",$row['typeListValues']);
			for ($i=0;$i<sizeof($names);$i++) {
				if ($resType!="") $resType.=":";
				$resType.=$names[$i]."=".$values[$i];
			}
			$resType = 'LIST:'.$resType;
		} else if ($row['vartype'] == 'NOTES') {
			$resType = 'TEXTAREA';
		} else if ($row['vartype'] == 'MAP') {
			$resType = 'COMBO:(None)=0';
			$map_ans = $sql->query("SELECT `name`,`index` FROM `map_info`");
			while ($map_row = $sql->fetch_array()) {
				if ($map_row['name']=='') $map_row['name']='Map #'.$map_row['index'];
				$resType.=":".$map_row['name'].'='.$map_row['index'];
			}
		} else if ($row['vartype'] == 'UNIT') {
			$resType = 'COMBO:(None)=';
			$map_ans = $sql->query("SELECT `name`,`typeName` FROM `units_template`");
			while ($map_row = $sql->fetch_array()) {
				if ($map_row['name']=='') $map_row['name']='Unit #'.$map_row['index'];
				$resType.=":".$map_row['typeName'].'='.$map_row['name'];
			}
		}
		
		$res_str .= tripleInfo($resType, ($row['isVar_Current']=='yes'), ($row['isVar_Default']=='yes'), ($row['isVar_Max']=='yes'));
	}

}

?>