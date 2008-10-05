<?php 
// (Disable GZip output)
define('NOZIP',true);
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
include "scripts/packetman.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Disable Package</title>
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

echo "Disabling package <b>".$row['name']."</b>...\n\n";

// Make sure the filesystem is ready to receive the disabled data
$root = DIROF('SYSTEM.ADMIN').'packages/'.$guid;
if (!is_dir($root)) mkdir($root);
$dest = $root.'/disabled';
if (!is_dir($dest)) mkdir($dest);
$scripts = $root.'/scripts';
if (!is_dir($scripts)) mkdir($scripts);

// Running disable scripts
echo "Preparing for hybernate...";
if (package_run_uninstall($pid, $scripts, true)) {
	echo "<font color=\"green\">ok</font>\n";
} else {
	echo "<font color=\"red\">failed</font>\n";
	die("Operation interrupted! Errors:\n".$package_error);
}

// Archie the data
echo "Archiving data...";
if (package_archive_manifest($pid, $dest, true)) {
	echo "<font color=\"green\">ok</font>\n";
} else {
	echo "<font color=\"red\">failed</font>\n";
	die("Operation interrupted! Errors:\n".$package_error);
}

// Archive the files
echo "Archiving files...";
if (package_archive_files($pid, $dest, true, true)) {
	echo "<font color=\"green\">ok</font>\n";
} else {
	echo "<font color=\"red\">failed</font>\n";
	die("Operation interrupted! Errors:\n".$package_error);
}

// Display warnings
if ($package_error!='') {
	echo "\nWarnings:\n";
	echo $package_error;
}

// Update packet status
$sql->query("UPDATE `system_packages` SET `status` = 'INACTIVE' WHERE `index` = $pid");

?>
</pre>
<input type="button" value="&lt;&lt; Back" onclick="window.location='package.php?guid=<?php echo $guid; ?>'" />
</body>
</html>
