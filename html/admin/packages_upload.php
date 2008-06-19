<?php
include "../config/config.php";
define("NOZIP",true);
include "../engine/includes/base.php";
$g_pcltar_lib_dir = $_CONFIG[GAME][BASE]."/admin/includes/lib";
include "includes/lib/pclzip.lib.php";
include "includes/lib/pcltar.lib.php";
//ob_end_flush();
ob_implicit_flush();
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
-->
</style>
<pre>
<?php
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

// Clean cache
echo "Initializing uploader engine...";
cleanDir($_CONFIG[GAME][BASE]."/admin/cache");
echo "<font color=\"green\">done</font>\n";
echo "Checking uploaded file...\n\n";

$file = $_FILES['file'];
if (!isset($file)) {
	die("<font color=\"red\">Warning! No file uploaded!</font></pre>\n<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />");
}

// Detect package mode and extract it
$extractok = false;
if (strtolower(substr($file['name'],-4)) == '.zip') {	
	// ZIP Archive

	echo "<b>ZIP</b> Detected. Deflating...";
	$zip = new PclZip($file['tmp_name']);
	$files=$zip->extract($_CONFIG[GAME][BASE]."/admin/cache");	
	if (!$files) {
		$extractok = false;
		echo "<font color=\"red\">failed</font>\n";
	} else {
		$extractok = true;
		echo "<font color=\"green\">done</font>\n";
	}

} elseif (strtolower(substr($file['name'],-4)) == '.tar') {	
	// TAR Archive

	echo "<b>TAR</b> Detected. Extracting...";
	$files=PclTarExtract($file['tmp_name'], $_CONFIG[GAME][BASE]."/admin/cache");
	if (!$files) {
		$extractok = false;
		echo "<font color=\"red\">failed</font>\n";
	} else {
		$extractok = true;
		echo "<font color=\"green\">done</font>\n";
	}

} elseif (strtolower(substr($file['name'],-7)) == '.tar.gz') {	
	// GZip TAR Archive

	echo "<b>GZip</b> Detected. Deflating...";	
	if (!function_exists('gzopen')) {
		echo "<font color=\"red\">failed</font>\n";
		die("\n<b>Your webserver does not support Zlib Compression Functions! Please install the <a href=\"http://www.gzip.org/zlib/\">zlib</a> extension for php, or try another package compression</b></pre>\n<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" /><input type=\"button\" onclick=\"window.location='http://www.gzip.org/zlib/'\" value=\"Download ZLib\" />");
	}
	
	$fin = gzopen($file['tmp_name'],"r");
	$fout = fopen($_CONFIG[GAME][BASE]."/admin/cache/package.tar", "w");
	while ($buf = gzread($fin,10240)) {
		fwrite($fout,$buf);
	}
	fclose($fin);
	fclose($fout);
	echo "<font color=\"green\">done</font>\n";

	echo "<b>TAR</b> Detected. Extracting...";
	$files=PclTarExtract($_CONFIG[GAME][BASE]."/admin/cache/package.tar", $_CONFIG[GAME][BASE]."/admin/cache");
	if (!$files) {
		$extractok = false;
		echo "<font color=\"red\">failed</font>\n";
	} else {
		$extractok = true;
		echo "<font color=\"green\">done</font>\n";
	}

} elseif (strtolower(substr($file['name'],-8)) == '.tar.bz2') {	
	// BZ2 TAR Archive
	echo "<b>BZip2</b> Detected. Deflating...";
	if (!function_exists('bzopen')) {
		echo "<font color=\"red\">failed</font>\n";
		die("\n<b>Your webserver does not support Bzip2 Compression Functions! Please install the <a href=\"http://sources.redhat.com/bzip2/\">bzip2</a> extension for php, or try another package compression</b></pre>\n<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" /><input type=\"button\" onclick=\"window.location='http://sources.redhat.com/bzip2/'\" value=\"Download BZip2\" />");
	}

	$fin = bzopen($file['tmp_name'],"r");
	$fout = fopen($_CONFIG[GAME][BASE]."/admin/cache/package.tar", "w");
	while ($buf = bzread($fin,10240)) {
		fwrite($fout,$buf);
	}
	fclose($fin);
	fclose($fout);
	echo "<font color=\"green\">done</font>\n";

	echo "<b>TAR</b> Detected. Extracting...";
	$files=PclTarExtract($_CONFIG[GAME][BASE]."/admin/cache/package.tar", $_CONFIG[GAME][BASE]."/admin/cache");
	if (!$files) {
		$extractok = false;
		echo "<font color=\"red\">failed</font>\n";
	} else {
		$extractok = true;
		echo "<font color=\"green\">done</font>\n";
	}
} else {
	die("<font color=\"red\">Cannot identify package type! Please use one of the follow formats: ZIP, TAR, GZIP, BZIP2</font></pre>\n<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />");
}

if (!$extractok) {
	die("<font color=\"red\">Extraction failed! (Is the file corrupted?)</font></pre>\n<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />");
} 
echo sizeof($files) ." file(s) extracted\n\n";

echo "Reading conficuration file..";
if (!file_exists($_CONFIG[GAME][BASE]."/admin/cache/package.xml")) {
	echo "<font color=\"red\">failed</font>";
	die("\n<b>Package file does not contain any configuration files! Are you sure this is a GloryLands package?</b></pre>\n<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />");
}

// Parse XML
$p = xml_parser_create();
xml_parse_into_struct($p,file_get_contents($_CONFIG[GAME][BASE]."/admin/cache/package.xml"),$vals,$index);
xml_parser_free($p);
echo "<font color=\"green\">done</font>\n";

// Check for full-variable existance
if (!isset($index['GUID']) || !isset($index['NAME']) || !isset($index['VERSION'])) {
	die("<font color=\"red\">Warning! This file contains an invalid XML config!</font></pre>\n<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />");
}


// Load iformation
$package = array();
$package['guid'] = $vals[$index['GUID'][0]]['value'];
$package['name'] = $vals[$index['NAME'][0]]['value'];
$package['version'] = $vals[$index['VERSION'][0]]['value'];
$package['desc'] = $vals[$index['DESCRIPTION'][0]]['value'];
$package['author'] = $vals[$index['AUTHOR'][0]]['value'];
$package['copyright'] = $vals[$index['COPYRIGHT'][0]]['value'];

?>
<table>
<tr>
	<td><b>Package name:</b></td>
	<td><?php echo $package['name']; ?> <i>(v<?php echo $package['version']; ?>)</i></td>
</tr>
<tr>
	<td><b>Package guid:</b></td>
	<td><?php echo $package['guid']; ?></td>
</tr>
<tr>
	<td><b>Description:</b></td>
	<td><?php echo $package['desc']; ?></td>
</tr>
<tr>
	<td colspan="2"><?php echo $package['copyright']." - ".$package['author']; ?></td>
</tr>
</table>
<?
global $sql;

if (isset($index['DEPENDENCY'])) {
	echo "Checking dependencies...<ul>\n";
	foreach ($index['DEPENDENCY'] as $id) {
		$guid = $vals[$id]['value'];	
		$name = $vals[$id]['attributes']['NAME'];
		if (!isset($vals[$id]['attributes']['VERSION'])) {
			$ver = false;
		} else {
			$ver = $vals[$id]['attributes']['VERSION'];
		}
		if ($guid!='' && $name!='') {
			echo "<li>Checking <b>{$name}</b>...";
			$sql->query("SELECT * FROM `system_packages` WHERE `guid` = '".$guid."'");
			if ($sql->emptyResults) {
				echo "<font color=\"red\">not found</font>\n</ul>\n";
				echo "<font color=\"blue\">You must install package <b>[{$name} v{$ver}]</b> or later in order to continue!</font>";
				echo "</pre>\n";
				echo "<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />";
				return;
			} else {
				$row = $sql->fetch_array();
				if (!$ver) {
					echo "<font color=\"green\">found</font> <i>(v".$row['version'].")</i>\n";
				} else {
					if ($row['version']>=$ver) {
						echo "<font color=\"#FF9900\">found</font> <i>(v".$row['version']." - Wanted v{$ver})</i>\n";
					} else {
						echo "<font color=\"red\">out of date</font>\n</ul>\n";
						echo "<font color=\"blue\">You must upgrade the package <b>{$name}</b> to version <b>{$ver}</b> or later in order to continue!</font>";
						echo "</pre>\n";
						echo "<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />";
						return;
					}
				}
			}	
		}
	}
	echo "</ul>\n";
}

// Check for previous version
$sql->query("SELECT * FROM `system_packages` WHERE `guid` = '".$package['guid']."'");
if (!$sql->emptyResults) {
	$row = $sql->fetch_array();
	

	if ($row['version'] == $package['version']) {
		echo "\n<font color=\"green\">Previous version <i>(v".$row['version'].")</i> detected!</font>\n";

		echo "You already have this version of the package. There is no need to process any installation.\nIf you want to repair the package, click the reinstall button.";
		echo "</pre>\n";
		echo "<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />";
		echo "<input type=\"button\" onclick=\"window.location='packages_install.php?mode=force&snapshot=no&recover=".$row['index']."'\" value=\"Reinstall package\" />";
	} elseif ($row['version'] < $package['version']) {
		echo "\n<font color=\"blue\">Previous version <i>(v".$row['version'].")</i> detected!</font>\n";

		echo "Please uninstall the previous version, or select upgrade to take a snapshot and upgrade the package\n";
		echo "</pre>\n";
		echo "<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />";
		echo "<input type=\"button\" onclick=\"window.location='packages_install.php?mode=force&guid=".$row['guid']."'\" value=\"Upgrade package\" />";
		echo "<input type=\"button\" onclick=\"window.location='packages_uninstall.php?guid=".$row['guid']."'\" value=\"Uninstall previous package\" />";
	} else {
		echo "\n<font color=\"blue\">Previous version <i>(v".$row['version'].")</i> detected!</font>\n";

		echo "Please uninstall the previous version, or select downgrade to take a snapshot and downgrade the package\n";
		echo "</pre>\n";
		echo "<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />";
		echo "<input type=\"button\" onclick=\"window.location='packages_install.php?mode=force&guid=".$row['guid']."'\" value=\"Downgrade package\" />";
		echo "<input type=\"button\" onclick=\"window.location='packages_uninstall.php?guid=".$row['guid']."'\" value=\"Uninstall previous package\" />";
	}
	return;

} else {

	echo "\n<font color=\"green\">No previous version detected</font>\n";
	echo "Ready to install\n";
	echo "</pre>\n";
	echo "<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />";
	echo "<input type=\"button\" onclick=\"window.location='packages_install.php'\" value=\"Start installation\" />";
	return;

}

?>
