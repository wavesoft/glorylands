<?php
include "../config/config.php";
define("NOZIP",true);
include "../engine/includes/base.php";
$g_pcltar_lib_dir = $_CONFIG[GAME][BASE]."/admin/includes/lib";
include "includes/lib/pclzip.lib.php";
include "includes/lib/pcltar.lib.php";
include "includes/update_dictionaries.php";
ob_implicit_flush();

function cleanDir($base) {
	$d = dir($base);
	while (false !== ($entry = $d->read())) {
		if (($entry=='.') || ($entry=='..')) {
			// skip
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
<pre>
<?php

// Check, copy and log file actions
function logCopy($src, $dst, $default_replace, $type) {
	global $package_id;
	global $sql;
	
	// Calculate source MD5
	$src_md5 = md5_file($src);

	// Exists on SQL?
	$ans=$sql->query("SELECT `hash`,`index`,`package` FROM `system_files` WHERE `filename` = '$dst'");
	if (!$ans) die($sql->getError());
	if (!$sql->emptyResults) {
		$row = $sql->fetch_array(MYSQL_NUM);

		// Calculate MD5 hashes
		if (file_exists($dst)) {
			$dst_md5 = md5_file($dst);
		} else {
			$dst_ms5 = '';
		}
		
		if ($row[2]!=$package_id) {
			// File exists, skip it
			echo "<font color=\"#663399\">used by another package. Skipped</font>\n"; 
			return;
		} else {
			if ($row[0]!=$dst_md5) {
				// Destination file exists, but has invalid hash? Consider it as damaged & replace it..
				$ans=$sql->query("UPDATE `system_files` SET `hash` = '{$src_md5}' WHERE `index` = {$row[1]}");
				if (!$ans) die($sql->getError());
				echo "<font color=\"green\">damaged or missing. Replaced</font>\n"; 
				if (is_file($dst)) unlink($dst);
				copy($src,$dst);
				return;
			
			} else {
				// File exists, skip it
				echo "<font color=\"#663399\">exists. Skipped</font>\n"; 
				return;
			}
		}
	} 
	
	$ans=$sql->query("INSERT INTO `system_files` (`filename`,`package`,`type`, `hash`) VALUE ('$dst',$package_id,'$type','{$src_md5}')");
	if (!$ans) die($sql->getError());

	if (is_file($dst)) { // File on destination exists
		
		// Calculate MD5 hashes
		$dst_md5 = md5_file($dst);
		
		// Same file?
		if ($src_md5 == $dst_md5) {
			echo "<font color=\"#FF9900\">exists &amp; same</font>\n"; 
			return;
		} else {
			if ($default_replace) {
				echo "<font color=\"green\">exists. Replaced</font>\n"; 
				if (is_file($dst)) unlink($dst);
				unlink($dst);
				copy($src,$dst);
				return;
			} else {
				echo "<font color=\"#663399\">exists. Skipped</font>\n"; 
				return;
			}
		}

	} else { // Newly created
	
		// Make sure directory exists
		$dir = dirname($dst);
		if (!is_dir($dir)) {
			mkdir($dir,0700,true);
		}
		$ans=@copy($src,$dst);
		if (!$ans) {
			echo "<font color=\"red\">failed!</font> ($src => $dst)\n"; 
		} else {
			//$sql->query("INSERT INTO `system_files` (`filename`,`package`,`type`,`hash`) VALUE ('$dst',$package_id,'$type','{$src_md5}')");
			echo "<font color=\"green\">done</font>\n"; 
		}
		return;
	}
	
	echo "\n";
}
function logMkdir($dst) {
	if (is_dir($dst)) {
		echo "<font color=\"#663399\">exists. Skipped</font>\n";
	} else {
		echo "<font color=\"green\">done</font>\n";
		mkdir($dst,0700,true);
	}
}

// Copy directory to directory (No ending slashes!)
function dirCopy($src, $dst, $default_replace = false, $type) {
	$d = dir($src);
	
	// If destination does not exists, create it
	if (is_dir($dst)) {
		echo "   Making directory <i><b>".$dst."</b></i>...";
		logMkdir($dst);
	}
	
	// For each file of source dir..
	while (false !== ($entry = $d->read())) {
		if (($entry=='.') || ($entry=='..')) {
			// skip
		} elseif (is_dir($src."/".$entry)) {
			dirCopy($src."/".$entry, $dst."/".$entry, $default_replace, $type);
		} else {
			echo "   Copying <i><b>".$entry."</b></i> ==&gt; <i><b>".$dst."/".$entry."</b></i>...";
			logCopy($src."/".$entry, $dst."/".$entry, $default_replace, $type);
		}
	}
	$d->close();
}

// Return the basepath of the specified type
function getPathOf($type) {
	global $_CONFIG;
	$base = $_CONFIG[GAME][BASE];
	$desc = '(Unknown)';
	
	foreach ($_CONFIG[DIRS][ALIAS] as $key => $path) {
		if ($type == $key) {
			$base = $base.$path;
			$desc = $_CONFIG[DIRS][NAMES][$key];
		}
	}
	
	return array($base,$desc);
}

// Return true, if user has the right to write this type of file
function canWrite($type) {	
	// Not implemented
	return true;
}

// Parse XML
echo "Reading conficuration file..";
$p = xml_parser_create();
xml_parse_into_struct($p,file_get_contents($_CONFIG[GAME][BASE]."/admin/cache/package.xml"),$vals,$index);
xml_parser_free($p);
echo "<font color=\"green\">done</font>\n\n";
global $_CONFIG;
global $package_id;

// Load iformation
$package = array();
if (isset($vals[$index['GUID'][0]]['value'])) $package['guid'] = $vals[$index['GUID'][0]]['value'];
if (isset($vals[$index['NAME'][0]]['value'])) $package['name'] = $vals[$index['NAME'][0]]['value'];
if (isset($vals[$index['VERSION'][0]]['value'])) $package['version'] = $vals[$index['VERSION'][0]]['value'];
if (isset($vals[$index['DESCRIPTION'][0]]['value'])) $package['description'] = $vals[$index['DESCRIPTION'][0]]['value'];
if (isset($vals[$index['AUTHOR'][0]]['value'])) $package['author'] = $vals[$index['AUTHOR'][0]]['value'];
if (isset($vals[$index['COPYRIGHT'][0]]['value'])) $package['copyright'] = $vals[$index['COPYRIGHT'][0]]['value'];
$package['installdate'] = time();
$package['status'] = 'INCOMPLETED';
$package['type'] = 'FILES';
$package['require'] = '';

if (!isset($package['guid'])) die("<font color=\"red\"><br><b>ERROR!</b> This package has not any GUID assosiated with!");

// Calculate dependencies
if (isset($index['DEPENDENCY'])) {
	foreach ($index['DEPENDENCY'] as $id) {
		$guid = $vals[$id]['value'];	
		if ($guid!='') {
			if ($$package['require']!='') $package['require'].=",";
			$package['require'].=$guid;
		}
	}
}

// In case of recovery, delete the old entry, but keep the same ID
if (isset($_REQUEST['recover'])) {
	$package_id = $_REQUEST['recover'];

	// Verify that package exists on SQL
	$ans=$sql->query("SELECT * FROM `system_packages` WHERE `index` = {$package_id}");
	if (!$ans) die($sql->getError());
	if (!$sql->emptyResults) {
		$ans=$sql->query("DELETE FROM `system_packages` WHERE `index` = {$package_id}");
		if (!$ans) die($sql->getError());
		$package['index'] = $package_id;
	} else {
		echo "\nThe specified package id <i>(#{$package_id})</i> does not belong to an installed package!</pre><br>";
		die("<input type=\"button\" onclick=\"window.history.go(-2)\" value=\"Back\" />");
	}

} 

// Insert package to SQL
$ans=$sql->addRow('system_packages', $package);
if (!$ans) die($sql->getError());

// Get package ID
$ans=$sql->query('SELECT `index` FROM `system_packages` ORDER BY `index` DESC LIMIT 0,1');
if (!$ans) die($sql->getError());
$package_id = $sql->fetch_array();
$package_id = $package_id[0];

// Create uninstall directory, if not exists
$dstDir = $_CONFIG[GAME][BASE]."/admin/uninstall/".$package['guid'];
if (!is_dir($dstDir)) mkdir($dstDir);

// For each file...
if (isset($index['FILE'])) {
	echo "Copying files...\n";

	$srcBase = $_CONFIG[GAME][BASE]."/admin/cache/";
	foreach ($index['FILE'] as $id) {
	
		$mode = "SYSTEM";
		$subdir = "/";
		$recurse = false;
		$replace = false;
				
		if (isset($vals[$id]['attributes']['TYPE'])) $mode = $vals[$id]['attributes']['TYPE'];
		if (isset($vals[$id]['attributes']['SUBDIR'])) $subdir = $vals[$id]['attributes']['SUBDIR'];
		if (isset($vals[$id]['attributes']['RECURSE'])) $recurse = (strtolower($vals[$id]['attributes']['RECURSE']) == 'yes');
		if (isset($vals[$id]['attributes']['REPLACE'])) $replace = (strtolower($vals[$id]['attributes']['REPLACE']) == 'yes');
		if (isset($vals[$id]['value'])) {
			
			$file = $vals[$id]['value'];
			$info = getPathOf($mode);
			$targetPath = $info[0].$subdir;
			
			if (!$recurse) {
				$target_file = basename($file);
				echo "Copying <font color=\"blue\">{$info[1]}</font> <i><b>".$file."</b></i> ==&gt; <i><b>".$targetPath.$target_file."</b></i>...<div class=\"filefolder\">";
				logCopy($srcBase.$file, $targetPath.$target_file, $replace, $mode);
				echo "</div>";
			} else {
				echo "Copying <font color=\"blue\">{$info[1]}</font> <i><b>".$file."</b></i> directory ==&gt; <i><b>".substr($targetPath,0,-1)."</b></i>...<div class=\"filefolder\">";
				dirCopy($srcBase.$file, substr($targetPath,0,-1), $replace, $mode);
				echo "</div>";
			}
 		}	
	}
}

// For each SQL File...
if (isset($index['SQL'])) {
	echo "Importing database files...\n";

	$srcBase = $_CONFIG[GAME][BASE]."/admin/cache/";
	foreach ($index['SQL'] as $id) {
		$file = $vals[$id]['value'];
		$sql->run($srcBase.$file);
	}
	
}

// Register uninstalling scripts
if (isset($index['UNINSTALL'])) {
	echo "Saving uninstall info...\n";

	$srcBase = $_CONFIG[GAME][BASE]."/admin/cache/";
	$dstDir = $_CONFIG[GAME][BASE]."/admin/uninstall/".$package['guid'];
	foreach ($index['UNINSTALL'] as $id) {

		// Defaults
		$mode = "SCRIPT";
		
		// Load variables from XML
		if (isset($vals[$id]['attributes']['TYPE'])) $mode = strtoupper($vals[$id]['attributes']['TYPE']);
		$file = $vals[$id]['value'];
				
		// Copy file and store info into DB
		copy($srcBase.$file, $dstDir.'/'.$file);
		$sql->query("INSERT INTO `system_packages_uninstall` (`package`, `umode`, `data`) VALUES ({$package_id}, '".mysql_escape_string($mode)."', '".mysql_escape_string($file)."')");
	}

}

// Update dictionary entries (if set)
if (isset($index['DICTIONARY']) && isset($index['ENTRY'])) {
	echo "Updating dictionary...\n";

	foreach ($index['ENTRY'] as $id) {
		
		$type = "CATEGORY";
		if (isset($vals[$id]['attributes']['TYPE'])) $type = strtoupper($vals[$id]['attributes']['TYPE']);
		$name = $vals[$id]['value'];

		if ($type == 'CATEGORY') {
		
			// Get the ID of the first entry
			$sql->query("SELECT `index` FROM `system_group_dictionary` ORDER BY `index` DESC LIMIT 0,1");
			$id = 0;
			if (!$sql->emptyResults) {
				$row = $sql->fetch_array(MYSQL_NUM);
				$id = $row[0];
			}

			// Import entry		
			$sql->query("INSERT INTO `system_group_dictionary` (`index`, `name`, `package`) VALUES ({$id}, '".mysql_escape_string($name)."', {$package_id})");
			
			// Set uninstall entry
			$sql->query("INSERT INTO `system_packages_uninstall` (`umode`, `data`, `package`) VALUES ('DICT', '$name', {$package_id})");
			
		}
		
	}
	
}

// Update hook entries (if set)
if (isset($index['HOOKS']) && isset($index['HOOK'])) {
	echo "Registering hooks...\n";
	global $EventChain;

	foreach ($index['HOOK'] as $id) {
		if (isset($vals[$id]['attributes']['EVENT']) && isset($vals[$id]['attributes']['FUNCTION'])) {
			
			$event = $vals[$id]['attributes']['EVENT'];
			$function = $vals[$id]['attributes']['FUNCTION'];
			$file = $vals[$id]['value'];
			
			// Create event chain if not set 
			if (!isset($EventChain[$event])) $EventChain[$event] = array();
			
			// Append entry to event chain
			array_push($EventChain[$event], array($file, $function));

			// Set uninstall entry
			$sql->query("INSERT INTO `system_packages_uninstall` (`umode`, `data`, `package`) VALUES ('HOOK', '".mysql_escape_string(serialize(array(
				'event' => $event,
				'function' => $function,
				'file' => $file
			)))."', {$package_id})");
			
		}

	}

	// Update file
	updateEventChainCache();
	
}


// Update possible changed cache
updateDictionaryCache();
updateTableCache();

$ans=$sql->query("UPDATE `system_packages` SET `status` = 'ACTIVE' WHERE `index` = {$package_id}");
if (!$ans) die($sql->getError());

echo "Cleaning up...";
cleanDir($_CONFIG[GAME][BASE]."/admin/cache");
echo "<font color=\"green\">done</font>\n";

?>

<font color="green">Completed successfully!</font>
</pre>
<input type="button" onclick="window.history.go(-2)" value="Back" />