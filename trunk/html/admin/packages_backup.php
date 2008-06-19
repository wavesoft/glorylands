<?php
ob_implicit_flush();
set_time_limit(0);
define("NOZIP",1);
include "../config/config.php";
include "../engine/includes/base.php";
$g_pcltar_lib_dir = $_CONFIG[GAME][BASE]."/admin/includes/lib";
include "includes/lib/pclzip.lib.php";
include "includes/lib/pcltar.lib.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Game Packages</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<style type="text/css">
<!--
pre {
	font-size: 12px;
	color: #333333;
	background-color: #E9E9E9;
	border: 1px dashed #CCCCCC;
	margin: 3px;
	padding: 3px;
}
table {
	border: 1px solid #999999;
}

-->
</style>
<script language="javascript">
<!-- 
function setPos(p) {
	var elm = document.getElementById('progBar');
	elm.style.width = p + "px";
}
function hideBar() {
	var elm = document.getElementById('progWin');
	elm.style.display = 'none';
}
//-->
</script>
<body>
<?php
function cleanDir($base) {
	$d = dir($base);
	while (false !== ($entry = $d->read())) {
		if (substr($entry,0,1)=='.') {
			// skip '.', '..', and hidden files (linux)
		} elseif (is_dir($base."/".$entry)) {
			cleanDir($base."/".$entry);
			rmdir($base."/".$entry);
		} else {
			unlink($base."/".$entry);
		}
	}
	$d->close();
}

echo "<pre>";

// Get package info
$sql->query("SELECT * FROM `system_packages` WHERE `index` = ".$_REQUEST['id']);
if ($sql->emptyResults) die("This plugin ID is invalid!</pre>\n");
$p_info = $sql->fetch_array();

echo "Initializing backup engine...";
cleanDir($_CONFIG[GAME][BASE]."/admin/cache");
echo "<font color=\"green\">done</font>\n";
echo "Initializing file map...\n";
$f_list = fopen($_CONFIG[GAME][BASE]."/admin/cache/filemap.inf","w");

// Count the files needed
$sql->query("SELECT COUNT(*) FROM `system_files` WHERE `package` = ".$_REQUEST['id']);
if ($sql->emptyResults) die("This plugin ID is invalid!</pre>\n");
$num_rows = $sql->get_value();

echo "Gathering <b>$num_rows</b> files...\n\n";

// Keep file names in an array
$zip_files = array($_CONFIG[GAME][BASE]."/admin/cache/filemap.inf");

// Display progress bar and calculate progress step
?>
<div style="border: solid 1px #333333; height: 24px; width: 204px; position: relative;">
<div style="background-color: #333366; height: 20px; position: absolute; left: 2px; top: 2px;" id="progBar"></div>
</div>
<?php
$p_step = 200/$num_rows;
$p_value = 0;
$p_last = 0;

// Traverse on the array
$files = $sql->query("SELECT * FROM `system_files` WHERE `package` = ".$_REQUEST['id']);
while ($file = $sql->fetch_array_fromresults($files)) { 

	// Display message
	//echo "Archiving ".$file['filename']."...\n";

	// Create filename's hash (used to store all the files and directories into a single level dir)
	$cache_file = md5($file['filename']).'.bak';
	
	// Copy the file
	copy($file['filename'], $_CONFIG[GAME][BASE]."/admin/cache/".$cache_file);
	array_push($zip_files, $_CONFIG[GAME][BASE]."/admin/cache/".$cache_file);

	// Change the file name into a relative location (where available)
	$f_name = $file['filename'];
	$f_name = str_ireplace($_CONFIG[GAME][BASE], '{$BASE}', $f_name);
	foreach ($_CONFIG[DIRS][ALIAS] as $key => $path) {
		if ($path!='') $f_name = str_ireplace($path, '{$'.$key.'}',$f_name);
	}

	// Map the file Hash with the real file name and location
	fwrite($f_list, $cache_file.'='.$f_name."\r\n");

	// Move progressbar forward
	$p_value += $p_step;
	$i = ceil($p_value);
	if ($i != $p_last) echo "<script language=\"javascript\">setPos({$i});</script>";
	$p_last = $i;
}

// Hide the progress
if ($i != $p_last) echo "<script language=\"javascript\">hideBar();</script>";

// Close map file handler
fclose($f_list);
echo "\nCompleted.\nCompressing archive...";

// Compress archive
$z_file = $_CONFIG[GAME][BASE].'/admin/archive/'.$p_info['guid'].'_'.date('y-m-d-H-i-s').'.zip';
$zip = new PclZip($z_file);
$zip->create($zip_files, PCLZIP_OPT_REMOVE_ALL_PATH);

echo "<font color=\"green\">done</font>\n";
echo "\nCompleted!\n</pre>";
echo "<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />";
?>
</body>
</html>