<?php 
// (Disable GZip output)
define('NOZIP',true);
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
include "scripts/packetman.php"; 
include "scripts/packetgen.php"; 
$g_pcltar_lib_dir = $_CONFIG[GAME][BASE]."/admin/includes/lib";
include "../includes/lib/pclzip.lib.php";
include "../includes/lib/pcltar.lib.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Install new package</title>
<link rel="stylesheet" type="text/css" href="res/stylemain.css">
</head>

<body>
<?php
if ($_REQUEST['a'] == 'upload') {
	echo "<pre>\n";
	
	$forced = false;
	if (isset($_REQUEST['force'])) $forced=true;

	// Make sure the filesystem is ready to receive the disabled data
	$cache = DIROF('SYSTEM.ADMIN').'cache';
	$package = DIROF('SYSTEM.ADMIN').'packages';
	
	// Clear cache
	echo "Clearing cache...";
	package_clear_dir($cache);
	echo "<font color=\"green\">ok</font>\n";
	
	// Extract archive
	$file = $_FILES['fileupload'];
	if (!isset($file)) {
		die("<font color=\"red\">Warning! No file uploaded!</font></pre>\n<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />");
	}
	if (strtolower(substr($file['name'],-4)) == '.zip') {	
		// ZIP Archive
	
		echo "<b>ZIP</b> Detected. Deflating...";
		$zip = new PclZip($file['tmp_name']);
		$files=$zip->extract($cache);	
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
		$files=PclTarExtract($file['tmp_name'], $cache);
		if (!$files) {
			$extractok = false;
			echo "<font color=\"red\">failed</font>\n";
		} else {
			$extractok = true;
			echo "<font color=\"green\">done</font>\n";
		}
	
	} elseif ((strtolower(substr($file['name'],-7)) == '.tar.gz') || (strtolower(substr($file['name'],-4)) == '.tgz')) {	
		// GZip TAR Archive
	
		echo "<b>GZip</b> Detected. Deflating...";	
		if (!function_exists('gzopen')) {
			echo "<font color=\"red\">failed</font>\n";
			die("\n<b>Your webserver does not support Zlib Compression Functions! Please install the <a href=\"http://www.gzip.org/zlib/\">zlib</a> extension for php, or try another package compression</b></pre>\n<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" /><input type=\"button\" onclick=\"window.location='http://www.gzip.org/zlib/'\" value=\"Download ZLib\" />");
		}
		
		$fin = gzopen($file['tmp_name'],"r");
		$fout = fopen($cache."/package.tar", "w");
		while ($buf = gzread($fin,10240)) {
			fwrite($fout,$buf);
		}
		fclose($fin);
		fclose($fout);
		echo "<font color=\"green\">done</font>\n";
	
		echo "<b>TAR</b> Detected. Extracting...";
		$files=PclTarExtract($cache."/package.tar", $cache);
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
		$fout = fopen($cache."/package.tar", "w");
		while ($buf = bzread($fin,10240)) {
			fwrite($fout,$buf);
		}
		fclose($fin);
		fclose($fout);
		echo "<font color=\"green\">done</font>\n";
	
		echo "<b>TAR</b> Detected. Extracting...";
		$files=PclTarExtract($cache."/package.tar", $cache);
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

	echo "Preparing installation...";
	$pinfo = package_install_prepare($cache);
	if (!$pinfo) {
		echo "<font color=\"red\">failed</font>\n";
		if ($force) {
			echo "Operation interrupted! Errors:\n".$package_error."\nProcess is forced to continue...\n\n";	
		} else {
			die("Operation interrupted! Errors:\n".$package_error);
		}
	} else {
		echo "<font color=\"green\">ok</font>\n";
	}
	
	echo "Installing package...";
	$filelist = package_install($cache, $package, $pinfo);
	if (!$filelist) {
		echo "<font color=\"red\">failed</font>\n";
		if ($force) {
			echo "Operation interrupted! Errors:\n".$package_error."\nProcess is forced to continue...\n\n";	
		} else {
			die("Operation interrupted! Errors:\n".$package_error);
		}
	} else {
		echo "<font color=\"green\">ok</font>\n";
	}
	
	echo "Running install scripts...";
	if (!package_run_install($pinfo['index'], $package.'/'.$pinfo['guid'].'/scripts')) {
		echo "<font color=\"red\">failed</font>\n";
		if ($force) {
			echo "Operation interrupted! Errors:\n".$package_error."\nProcess is forced to continue...\n\n";	
		} else {
			die("Operation interrupted! Errors:\n".$package_error);
		}
	} else {
		echo "<font color=\"green\">ok</font>\n";
	}
	
	// Display warnings
	if ($package_error!='') {
		echo "\nWarnings:\n";
		echo $package_error;
	}
	
	echo "</pre>\n";
	?>
	<form action="navbar.php" target="left">
	<input type="hidden" name="rand" value="<?php echo md5(time().rand(0,100)); ?>" />
	<input type="hidden" name="guid" value="" />
	</form>
	<script language="javascript">
	document.forms[0].submit();
	</script>
	<input type="button" value="&lt;&lt; Back" onclick="window.location='package.php?guid=<?php echo $pinfo['guid']; ?>'" />
	<?php

} else {
?>
<table width="100%">
<tr>
	<td width="35" rowspan="2" valign="top"><img src="../images/install.gif" align="absmiddle" /></td>
	<td align="left"><font size="+2"><b> Install/Upgrade package</b></font></td>
</tr>
<tr>
	<td align="left">Upload and install or upgrade a package or package set</td>
</tr>
<tr>
	<td colspan="2">
	
	<p>
	<fieldset><legend>Create a custom hook</legend>
	<form action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="a" value="upload" />
	<table>
		<tr>
			<td><b>Upload package:</b></td>
			<td>
			<input type="file" name="fileupload" />
			</td>
		</tr>
		<tr>
			<td><b>Options:</b></td>
			<td>
			<label for="force"><input type="checkbox" name="force" id="force" /> Force install (do not stop on errors)</label>
			</td>
		</tr>
	</table>
	<input type="submit" value="Install package" />
	</form>
	</fieldset>
	</p>
		
	</td>
</tr>
</table>
<?php
}
?>
</body>
</html>
