<?php 
// (Disable GZip output)
define('NOZIP',true);
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
include "scripts/packetman.php"; 
include "scripts/packetgen.php"; 
include "scripts/installfunc.php"; 
$g_pcltar_lib_dir = $_CONFIG[GAME][BASE]."/admin/includes/lib";
include "../includes/lib/pclzip.lib.php";
include "../includes/lib/pcltar.lib.php";
set_time_limit(0);
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
	
	// Confirm upload and get file
	$file = $_FILES['fileupload'];
	if (!isset($file)) {
		die("<font color=\"red\">Warning! No file uploaded!</font></pre>\n<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />");
	}
	$fname = $cache.'/'.$file['name'];
	if (!move_uploaded_file($file['tmp_name'], $fname)) {
		die("<font color=\"red\">Error uploading this file!</font></pre>\n<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />");
	}
	
	// Extract archive
	echo "Extracting archive...";
	if (!package_extract_file($fname, $cache)) {
		echo "<font color=\"red\">failed</font>\n";
		die("Operation interrupted! Errors:\n".$package_error);
	} else {
		echo "<font color=\"green\">ok</font>\n";
	}
	
	// Check multiple archive case
	echo "Checking for multiple archive...";
	$d = dir($cache);
	$only_zip = true;
	$zip_list = array();
	while (false !== ($entry = $d->read())) {
		// Exclude hidden files
		if (substr($entry,0,1)!='.') {
			if ( (strtolower(substr($entry,-4)) == '.zip') ||
				 (strtolower(substr($entry,-4)) == '.tar') ||
				 (strtolower(substr($entry,-4)) == '.tgz') ||
				 (strtolower(substr($entry,-7)) == '.tar.gz') ||
				 (strtolower(substr($entry,-4)) == '.tbz') ||
				 (strtolower(substr($entry,-8)) == '.tar.bz2')) {
				 
				// Exclude the uploaded zip file
				if ($entry!=$file['name']) array_push($zip_list, $entry);				
			} else {
				$only_zip = false;
			}
		}
	}
	$d->close();
	if ($only_zip) {
		echo "<font color=\"#FF9900\">found</font>\nInstalling <b>".sizeof($zip_list)."</b> packages:\n\n";
	
		// Sort list
		sort($zip_list);
	
		// Make the folder that will hold the further extracted files
		$old_cache = $cache;
		$cache.='/extract';
		mkdir($cache);
		
	} else {
		echo "<font color=\"green\">not found</font>\n";
		$zip_list = array('');
	}

	// Package counter
	$installed_count = 0;

	// Start installing archives
	foreach ($zip_list as $zip_file) {
		
		// Error flag
		$has_errors = false;
		
		if ($zip_file=='') {
			// If we do not have multiple archives, use the default archive and the current directory
			$zipname = 'package';
			
		} else {
		
			// If we haev multiple archives, extract the archive to the "extracted" directory
			echo "\nExtracting $zip_file...";
			package_clear_dir($cache);
			
			// Extract archive
			if (!package_extract_file($old_cache.'/'.$zip_file, $cache)) {
				echo "<font color=\"red\">failed</font>\n";
				echo "Operation interrupted! Errors:\n".$package_error;
				$has_errors = true;
			} else {
				echo "<font color=\"green\">ok</font>\n";
			}
			
			// Get zip name
			$zipname = pathinfo($zip_file);
			$zipname = $zipname['filename'];
		}
	
		if (!$has_errors) {
			echo "Starting installation of the <b>{$zipname}</b>...";
			$pinfo = package_install_prepare($cache);
			if (!$pinfo) {
				echo "<font color=\"red\">failed</font>\n";
				if ($force) {
					echo "Operation interrupted! Errors:\n".$package_error."\nProcess is forced to continue...\n\n";	
				} else {
					echo "Operation interrupted! Errors:\n".$package_error;
					$has_errors = true;
				}
			} else {
				echo "<font color=\"green\">ok</font>\n";
			}
		}
		
		if (!$has_errors) {
			echo "Installing package...";
			$filelist = package_install($cache, $package, $pinfo);
			if (!$filelist) {
				echo "<font color=\"red\">failed</font>\n";
				if ($force) {
					echo "Operation interrupted! Errors:\n".$package_error."\nProcess is forced to continue...\n\n";	
				} else {
					echo "Operation interrupted! Errors:\n".$package_error;
					$has_errors = true;
				}
			} else {
				echo "<font color=\"green\">ok</font>\n";
			}
		}
		
		if (!$has_errors) {
			echo "Running install scripts...";
			if (!package_run_install($pinfo['index'], $package.'/'.$pinfo['guid'].'/scripts')) {
				echo "<font color=\"red\">failed</font>\n";
				if ($force) {
					echo "Operation interrupted! Errors:\n".$package_error."\nProcess is forced to continue...\n\n";	
				} else {
					echo "Operation interrupted! Errors:\n".$package_error;
					$has_errors = true;
				}
			} else {
				echo "<font color=\"green\">ok</font>\n";
			}
		}

		if (!$has_errors) {
			// Display warnings
			if ($package_error!='') {
				echo "\nWarnings:\n";
				echo $package_error;
			}
			
			$installed_count++;
		}
		
	}
	
	echo "\n<b>{$installed_count}</b> packages are installed";
	
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
