<?php
/**
  * Package Generator Module
  *
  * This module provides the interface to create complete packet archives using the
  * data from the database
  *
  * @package GloryLands
  * @subpackage Administration
  * @author John Haralampidis <johnys2@gmail.com>
  * @copyright Copyright (C) 2007-2008, John Haralampidis
  * @version 1.0
  */

/**
  * Gather package files
  *
  * @param int $pid			The source package ID
  * @param string $dest_dir	The directory to store the files to
  * @return bool|array	 	Returns the file list array if successfull or FALSE on error
  */
function package_gather_files($pid, $dest_dir) {
	global $sql, $package_error;
	$package_error='';

	// Initialize partial manifest XML file
	$f = fopen($dest_dir.'/package.xml.part','w');
	fwrite($f,"	<files>\n");

	// Make dir storage
	package_create_dirtree($dest_dir);
	
	// Initialize file list
	$files = array();
		
	// Obdain package file info
	$ans = $sql->query("SELECT * FROM `system_files` WHERE `package` = $pid");
	if (!$ans) return false;
	if ($sql->emptyResults) return true;
	
	// Store the directories created
	$dirsmade = array();	
	
	// Start copying and mapping files
	$i=0;
	while ($file = $sql->fetch_array_fromresults($ans)) {
		// Get file information
		$fname = DIROF($file['type']).$file['filename'];
		$ftype = strtolower($file['type']);
		$relativename = $file['filename'];
		$path_parts = pathinfo($relativename);
		$shortname = $path_parts['basename'];
		
		// Detect the relative structure
		$struct = $path_parts['dirname'];
		if ($struct=='.') {
			$fdest = $dest_dir.'/'.$ftype;
		} else {
			$fdest = $dest_dir.'/'.$ftype.'/'.$struct;		
		}
		
		// Mark directory as made
		if (!isset($dirsmade[$ftype])) {
			$dirsmade[$ftype] = true;
			fwrite($f,"		<file type=\"".$file['type']."\" subdir=\"/\" recurse=\"yes\">".$ftype."</file>\n");
		}
		
		// Make missing dirs
		if (!is_dir($fdest)) package_create_dirtree($fdest);
		
		// Archive the file
		if (copy($fname, $fdest.'/'.$shortname)) {
			array_push($files, $fdest.'/'.$shortname);
		} else {
			$package_error.=" &bull; Cannot archive file $files to {$fdest}/{$shortname}\n";
		}

	}
	
	// Close file
	fwrite($f,"	</files>\n");
	fclose($f);
	
	// Everything is OK
	return $files;
}

/**
  * Build manifest file using SQL parameters and the partial file generated with package_gather_files
  *
  * @param int $pid				The source package ID
  * @param string $dest_dir		The directory to store the files to
  * @param string $package_root	The package's local storage directory
  * @return bool|array	 		Returns the files created in an array or FALSE on error
  */
function package_build_manifest($pid, $dest_dir, $package_root) {
	global $sql;
		
	// Obdain package file info
	$ans = $sql->query("SELECT * FROM `system_packages` WHERE `index` = $pid");
	if (!$ans) return false;
	if ($sql->emptyResults) return false;
	$info = $sql->fetch_array();

	// Prepare file cache
	$files = array();

	// Find package's local scripts directory
	$package_scripts = $package_root."/scripts";

	// Build head
	$f = fopen($dest_dir.'/package.xml','w');
	fwrite($f,"<?xml version=\"1.0\" encoding=\"iso-8859-7\"?>\n");
	fwrite($f,"<package>\n");
	
	// Write general files
	fwrite($f,"
	<guid>{$info['guid']}</guid>
	<name>{$info['name']}</name>
	<version>{$info['version']}</version>
	<description>{$info['description']}</description>

	<author>{$info['author']}</author>
	<copyright>{$info['copyright']}</copyright>
	<website>{$info['website']}</website>\n\n");	
	
	// Write dependencies
	if ($info['require']!='a:0:{}') {
		fwrite($f,"	<dependencies>\n");
		foreach (unserialize($info['require']) as $dep) {
			if (!(!$dep['ver'])) {
				fwrite($f,'		<dependency name="'.$dep['name'].'" version="'.$dep['ver'].'">'.$dep['guid'].'</dependency>'."\n");
			} else {
				fwrite($f,'		<dependency name="'.$dep['name'].'">'.$dep['guid'].'</dependency>'."\n");
			}
		}
		fwrite($f,"	</dependencies>\n\n");
	}
	
	// Include part
	fwrite($f, file_get_contents($dest_dir.'/package.xml.part')."\n");
	
	// Import install files 
	$sql->query("SELECT * FROM `system_packages_install` WHERE `package` = $pid");
	if (!$sql->emptyResults) {
	
		// Make scripts directory
		$scripts = $dest_dir.'/scripts';
		package_create_dirtree($scripts);

		// Import files and XML entries
		fwrite($f, "	<install>\n");
		while ($row = $sql->fetch_array(MYSQL_ASSOC)) {
			
			// Import file
			copy($package_scripts.'/'.$row['data'], $scripts.'/'.$row['data']);
			array_push($files, $scripts.'/'.$row['data']);
		
			// Import XML
			if ($row['imode'] == 'SCRIPT') {
				fwrite($f, "		<script use=\"".$row['use']."\">".$row['data']."</script>\n");
			} elseif ($row['imode'] == 'SQL') {
				fwrite($f, "		<sql use=\"".$row['use']."\">".$row['data']."</sql>\n");
			}
		}
		fwrite($f, "	</install>\n\n");
	}

	// Import uninstall files 
	$sql->query("SELECT * FROM `system_packages_uninstall` WHERE `package` = $pid");
	if (!$sql->emptyResults) {
	
		// Make scripts directory
		$scripts = $dest_dir.'/scripts';
		package_create_dirtree($scripts);

		// Import files and XML entries
		fwrite($f, "	<uninstall>\n");
		while ($row = $sql->fetch_array(MYSQL_ASSOC)) {
			
			// Import file
			copy($package_scripts.'/'.$row['data'], $scripts.'/'.$row['data']);
			array_push($files, $scripts.'/'.$row['data']);
		
			// Import XML
			if ($row['umode'] == 'SCRIPT') {
				fwrite($f, "		<script use=\"".$row['use']."\">".$row['data']."</script>\n");
			} elseif ($row['umode'] == 'SQL') {
				fwrite($f, "		<sql use=\"".$row['use']."\">".$row['data']."</sql>\n");
			}
		}
		fwrite($f, "	</uninstall>\n\n");
	}
	
	// Import system hooks
	$sql->query("SELECT * FROM `system_hooks` WHERE `package` = $pid");
	if (!$sql->emptyResults) {
	
		// Import files and XML entries
		fwrite($f, "	<hooks>\n");
		while ($row = $sql->fetch_array(MYSQL_ASSOC)) {
			// Import XML
			fwrite($f, "		<hook event=\"".$row['hook']."\" function=\"".$row['function']."\">".$row['filename']."</hook>\n");
		}
		fwrite($f, "	</hooks>\n\n");
	}

	// Import system dictionaries
	$sql->query("SELECT * FROM `system_dictionaries` WHERE `package` = $pid");
	if (!$sql->emptyResults) {
	
		// Import files and XML entries
		fwrite($f, "	<dictionary>\n");
		while ($row = $sql->fetch_array(MYSQL_ASSOC)) {
			// Import XML
			if ($row['mode'] == 'DYNAMIC') {
				fwrite($f, "		<entry type=\"".$row['group']."\">".$row['name']."</entry>\n");
			} else {
				fwrite($f, "		<entry type=\"".$row['group']."\" value=\"".$row['value']."\">".$row['name']."</entry>\n");
			}
		}
		fwrite($f, "	</dictionary>\n\n");
	}

	// Finalize
	fwrite($f,"<package>\n");
	fclose($f);

	// Remove partial XML
	unlink($dest_dir.'/package.xml.part');

	// Everything was OK
	array_push($files, $dest_dir.'/package.xml');
	return $files;
}

?>