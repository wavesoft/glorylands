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
  * @return string		 	Returns the aliased file path
  */
function package_gather_files($pid, $dest_dir) {
	global $sql;

	// Detect file cache base
	$base = DIROF("SYSTEM.ADMIN")."cache/files";
	if (!is_dir($base)) mkdir($base);
	
	// Obdain package file names
	$ans = $sql->query("SELECT * FROM `system_files` WHERE `package` = $pid");
	if (!$ans) return false;
	if ($sql->emptyResults) return true;
		
	// Start copying and mapping files
	$i=0;
	while ($file = $sql->fetch_array_fromresults($ans)) {
		// Get some information
		$fname = $file['filename'];
		
		// Archive the file
		if (copy($fname, $dest_dir.'/'.$shortname)) {
				
			// Insert a mapping file entry
			if ($relative) $fname=package_path_alias($fname);
			fwrite($f, $shortname.'='.$file['version'].'='.$fname."\n");
						
		}
	}
	
	// Close mapped file
	fclose($f);
	
	// Everything is OK
	return true;
}

?>