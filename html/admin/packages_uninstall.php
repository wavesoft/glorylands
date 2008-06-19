<?php
ob_implicit_flush();
set_time_limit(0);
define("NOZIP",1);
include "../config/config.php";
include "../engine/includes/base.php";
$g_pcltar_lib_dir = $_CONFIG[GAME][BASE]."/admin/includes/lib";
include "includes/lib/pclzip.lib.php";
include "includes/lib/pcltar.lib.php";
include "includes/update_dictionaries.php";
global $EventChain;

error_reporting(E_ALL);

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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Game Packages</title>
<link href="style.css" rel="stylesheet" type="text/css" />
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
.filefolder {
	width: 100%;
	overflow: auto;
	height: 200px;
}
-->
</style>
</head>
<body>
<pre>
<?php

function qdie($text) {
	echo '<font color="red">'.$text."\n".'Uninstallation failed!</font></pre><input type="button" onclick="window.history.go(-1)" value="Back" />';	
	die();
}
function gecho($text) {
	echo "<font color=\"green\">$text</font>";
}
function recho($text) {
	echo "<font color=\"red\">$text</font>";
}
function yecho($text) {
	echo "<font color=\"gold\">$text</font>";
}
function pecho($text) {
	echo "<font color=\"purple\">$text</font>";
}

echo "Reading package info...";

$package_id=(int)$_REQUEST['id'];
$ans=$sql->query("SELECT * FROM `system_packages` WHERE `index` = {$package_id}");
if (!$ans) die($sql->getError());
if ($sql->emptyResults) qdie("Cannot load info!");
$package=$sql->fetch_array(MYSQL_ASSOC);
gecho("ok\n");

echo "Uninstallation of <b>".$package['name']."</b> initiated\n\n";

if (!isset($_REQUEST['confirm'])) {
	// Create a confirmation hash to validate the 'yes'
	$confirm = md5(time());
	$_SESSION['confirmhash'] = $confirm;	
?>

<center>
Do you really want to uninstall this package? This action is not undoable!<br />
<input type="button" onclick="window.history.go(-1)" value="No" /> <input type="button" onclick="window.location='packages_uninstall.php?id=<?php echo $_REQUEST['id'] ?>&confirm=<?php echo $confirm; ?>'" value="Yes" />
</center>
<?php
	die("</pre></body></html>");
} elseif ($_REQUEST['confirm']!=$_SESSION['confirmhash']) {
	qdie("Confirmation key is not valid!");
}
unset($_SESSION['confirmhash']);

echo "Finding files...";

$sql->query("UPDATE `system_packages` SET `status` = 'UNINSTALLING' WHERE `index` = {$package_id}");
$ans=$sql->query("SELECT * FROM `system_files` WHERE `package` = {$package_id}");
echo "<b>".$sql->numRows . "</b> found\n<div class=\"filefolder\">";

while ($row=$sql->fetch_array_fromresults($ans, MYSQL_ASSOC)) {
	echo "Erasing <b>".$row['filename']."</b>...";
	if (file_exists($row['filename'])) {
	
		// Check if other packages uses the same file
		$sql->query("SELECT `package` FROM `system_files` WHERE `filename` = '".$row['filename']."'");
		$unique = true;
		while ($pckg = $sql->fetch_array(MYSQL_NUM)) {
			if ($pckg[0]!=$package_id) $unique = false;
		}
		
		if (!$unique) {
			$sql->query("DELETE FROM `system_files` WHERE `index` = ".$row['index']);
			pecho("used by another package\n");
		} elseif (unlink($row['filename'])) {
			$sql->query("DELETE FROM `system_files` WHERE `index` = ".$row['index']);
			gecho("deleted\n");
		} else {
			recho("cannot be deleted!\n");
		}
	} else {
		$sql->query("DELETE FROM `system_files` WHERE `index` = ".$row['index']);
		yecho("not found!\n");
	}
}

echo "</div>\nFiles uninstalled. Searching for further uninstallation rules...";

$uninstdir=$_CONFIG[GAME][BASE]."/admin/uninstall/".$package['guid'];

$ans=$sql->query("SELECT * FROM `system_packages_uninstall` WHERE `package` = {$package_id}");
if (!$ans) die($sql->getError());
if ($sql->emptyResults) {
	yecho("none found\n");
} else {
	gecho("ok\n");
	while ($row = $sql->fetch_array_fromresults($ans,MYSQL_ASSOC)) {
		if ($row['umode'] == 'SCRIPT') {
			echo "Executing uninstallation script ".$row['data']."...";
			@include_once($uninstdir.'/'.$row['data']);
			yecho("ok\n");
		} elseif ($row['umode'] == 'SQL') {
			echo "Executing uninstallation SQL script ".$row['data']."...";
			if (!$sql->run($uninstdir.'/'.$row['data'])) {
				gecho("ok\n");
			} else {
				recho("failed\n".$sql->getError()."\n");
			}
		} elseif ($row['umode'] == 'INLINE') {
			echo "Executing in-line SQL script <pre>".$row['data']."</pre>...";
			$ans = eval($row['data']);
			yecho("ok\n");
		} elseif ($row['umode'] == 'DICT') {
			echo "Removing dictionary entry ".$row['data']."...";
			$ans=$sql->query("DELETE FROM `system_group_dictionary` WHERE `name` = '".$row['data']."'");
			if ($ans) {
				gecho("ok\n");
			} else {
				recho("failed\n".$sql->getError()."\n");
			}
		} elseif ($row['umode'] == 'HOOK') {
			$sqldata = unserialize($row['data']);
			echo "Removing hook chain entry ".$sqldata['event']."...";
			
			// Remove specific entry
			foreach ($EventChain as $chain => $data) {
				foreach ($data as $index => $rows) {
					if (($rows[0]==$sqldata['file']) && ($rows[1]==$sqldata['function'])) {
						// Remove entry
						unset($EventChain[$chain][$index]);
						if (sizeof($EventChain[$chain])==0) unset($EventChain[$chain]);
					}
				}
			}
			gecho("ok\n");
		}
		$sql->query("DELETE FROM `system_packages_uninstall` WHERE `index` = ".$row['index']);
	}
}

echo "\nUpdating dictionaries...";

// Update possible changed cache
updateEventChainCache();
updateDictionaryCache();
updateTableCache();


echo "\nCleaning-up...";
if (is_dir($_CONFIG[GAME][BASE]."/admin/uninstall/".$package['guid'])) {
	cleanDir($_CONFIG[GAME][BASE]."/admin/uninstall/".$package['guid']);
	rmdir($_CONFIG[GAME][BASE]."/admin/uninstall/".$package['guid']);
}
$sql->query("DELETE FROM `system_packages` WHERE `index` = {$package_id}");
echo "\n";

?>

<font color="green">Completed successfully!</font>
</pre>
<input type="button" onclick="window.history.go(-2)" value="Back" />
</body>
</html>