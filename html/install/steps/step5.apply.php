<?php

if (isset($_REQUEST['package'])) {
	
	echo "<h2>Installing packages...</h2><div class=\"separator\">Installation Console</div><pre>";

	chdir("../admin/packetman");	
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

	// Make sure the filesystem is ready to receive the disabled data
	$cache = DIROF('SYSTEM.ADMIN').'cache';
	$package = DIROF('SYSTEM.ADMIN').'packages';

	// Package counter
	$installed_count = 0;
	
	// Change directory to emulate executionf rom the /adming/packageinstall.php
	//chdir("../admin/packetman");
	
	foreach ($_REQUEST['package'] as $package_name) {

		$fname = $_CONFIG[GAME][BASE]."/install/data/packages/".$package_name;

		// Clear cache
		echo "Preparing installation of <b>{$package_name}</b>...";
		package_clear_dir($cache);
		echo "<font color=\"green\">ok</font>\n";

		// Include administration operations
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
		
			
	}
	
	echo "\n<b>{$installed_count}</b> packages are installed";
	echo "</pre>";
}

?>