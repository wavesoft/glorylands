<?php 
// (Disable GZip output)
define('NOZIP',true);
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
include "scripts/packetman.php"; 
include "scripts/packetgen.php"; 
include "../includes/lib/pclzip.lib.php";
include "../includes/lib/pcltar.lib.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Pack and Download Package</title>
<link rel="stylesheet" type="text/css" href="res/stylemain.css">
</head>
<body>
<pre>
<?php

$guid = $_REQUEST['guid'];
$ans = $sql->query("SELECT * FROM `system_packages` WHERE `guid` = '{$guid}'");
if (!$ans) die($sql->getError());
if ($sql->emptyResults) die("Cannot find package with guid {$guid}");

$row = $sql->fetch_array();
$pid = $row['index'];

echo "Packing package <b>".$row['name']."</b>...\n\n";

// Make sure the filesystem is ready to receive the disabled data
$package = DIROF('SYSTEM.ADMIN').'/packages/'.$guid;
$cache = DIROF('SYSTEM.ADMIN').'/cache';

// Clear cache
echo "Clearing cache...";
package_clear_dir($cache);
echo "<font color=\"green\">ok</font>\n";

// Gather files
echo "Gathering package files...";
$filelist = package_gather_files($pid, $cache);
if (!$filelist) {
	echo "<font color=\"red\">failed</font>\n";
	die("Operation interrupted! Errors:\n".$package_error);
} else {
	echo "<font color=\"green\">ok</font>\n";
}

// Archive the files
echo "Building manifest...";
$manifestfiles = package_build_manifest($pid, $cache, $package);
$filelist = array_merge($filelist, $manifestfiles);
if ($manifestfiles) {
	//array_push($filelist, $cache.'/package.xml');
	echo "<font color=\"green\">ok</font>\n";
} else {
	echo "<font color=\"red\">failed</font>\n";
	die("Operation interrupted! Errors:\n".$package_error);
}

// Archive the files
echo "Compressing archive...";

$pname = strtolower($row['name']);
$pname = str_replace(" ","_",$pname);
$pname = str_replace("\\",".",$pname);
$pname = str_replace("/",".",$pname);
$pname = str_replace("|",".",$pname);
$pname = str_replace("?",".",$pname);
$pname = str_replace("*",".",$pname);
$pname = str_replace(">",")",$pname);
$pname = str_replace("<","(",$pname);

$pname .= "-v".$row['version'];
$pname .= ".zip";

$z_file = $cache.'/'.$pname;
$zip = new PclZip($z_file);
$zip->create($filelist, '', $cache);
echo "<font color=\"green\">ok</font>\n";

// Ready to download
echo "\n<a href=\"../cache/$pname\"> Download the package</a>\n";

// Display warnings
if ($package_error!='') {
	echo "\nWarnings:\n";
	echo $package_error;
}

?>
</pre>
<input type="button" value="&lt;&lt; Back" onclick="window.location='package.php?guid=<?php echo $guid; ?>'" />
</body>
</html>
