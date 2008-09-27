<?php
/**
  * Packet Management Module
  *
  * @package GloryLands
  * @subpackage Administration
  * @author John Haralampidis <johnys2@gmail.com>
  * @copyright Copyright (C) 2007-2008, John Haralampidis
  * @version 1.0
  */

/**
  * Private function that replaces the base file path with it's DIROF alias
  *
  * @param string $filepath	The source file path
  * @return string		 	Returns the aliased file path
  */
function packet_path_alias($filepath) {
	global $_CONFIG;
	foreach ($_CONFIG[DIRS][ALIAS] as $alias => $apath) {
		$apath = $_CONFIG[GAME][BASE].$apath;
		if (strstr($filepath, $apath)) {
			$filepath = str_replace($apath, '{'.$alias.'}', $filepath);
			return $filepath;
		}
	}
	return $filepath;
}

/**
  * Private function that detects and updates the DIROF aliases back into 
  * full paths
  *
  * @param string $filepath	 The source file path
  * @param string $usedalias This parameter returns the aliased value found and replaced in the string
  * @return string		 	 Returns the aliased file path
  */
function packet_path_expand($filepath, &$usedalias) {
	global $_CONFIG;
	foreach ($_CONFIG[DIRS][ALIAS] as $alias => $apath) {
		$apath = $_CONFIG[GAME][BASE].$apath;
		if (substr($filepath,0,strlen($alias)+2) == '{'.$alias.'}') {
			$filepath = str_replace('{'.$alias.'}', $apath, $filepath);
			$usedalias = $alias;
			return $filepath;
		}
	}
	$usedalias = '';
	return $filepath;
}

/**
  * Finds and copies all the files of the provided package
  *
  * @param int $pid 		The source packet index
  * @param string $dest_dir	The destination directory (without trailling slash)
  * @param bool $relative	If TRUE the mapping file will contain relative paths rather than absolute
  * @param bool $move		If TRUE the source files and SQL entries will be removed
  * @return bool		 	Returns true on success or false otherways
  */
function packet_archive_files($pid, $dest_dir, $relative=true, $move=false) {
	global $sql;
	
	// Obdain package file names
	$ans = $sql->query("SELECT * FROM `system_files` WHERE `package` = $pid");
	if (!$ans) return false;
	if ($sql->emptyResults) return true;
	
	// Initialize maping file
	// The mapping file provides the information required to rebuild the packet structure
	$f = fopen($dest_dir.'/files.map','w');
	
	// Start copying and mapping files
	$i=0;
	while ($file = $sql->fetch_array_fromresults($ans)) {
		// Get some information
		$fname = $file['filename'];
		$shortname = 'file'.$i++;
		
		// Archive the file
		copy($fname, $dest_dir.'/'.$shortname);
		
		// Insert a mapping file entry
		if ($relative) $fname=packet_path_alias($fname);
		fwrite($f, $shortname.'='.$fname."\n");
		
		// Remove source data if told so
		if ($move) {
			//if (unlink($file['filename'])) $sql->query("DELETE FROM `system_files` WHERE `index` = ".$file['index']);
			echo "Removing {$file['filename']}\n";
		}
	}
	
	// Close mapped file
	fclose($f);
	
	// Everything is OK
	return true;
}

/**
  * Imports all the files previously archived with packet_archive_files, back into the
  * filesystem and the SQL database
  *
  * @param int $pid 		The destination packet index
  * @param string $src_dir	The destination directory (without trailling slash)
  * @param bool $import		If TRUE the source files will be imported in the SQL
  * @return bool		 	Returns true on success or false otherways
  */
function packet_restore_files($pid, $src_dir, $import=false) {
	global $sql;
	
	// Open maping file
	// The mapping file provides the information required to rebuild the packet structure
	$f = @fopen($src_dir.'/files.map','r');
	if (!$f) return false;
	
	// Start copying and unmapping files
	while ($row = fgets($f)) {
	
		// Get some information
		$row = explode("=",$row);
		$fname = packet_path_expand($row[1], $ftype);
		$shortname = $row[0];		
		
		// Restore the file
		//copy($src_dir.'/'.$shortname, $fname);
		echo "Copying ".$src_dir.'/'.$shortname.' to '.$fname."\n";
		
		// Import data if told so
		if ($import) {
			echo "Importing file $fname, with type $ftype into package $pid\n";
		}
	}
	
	// Close mapped file
	fclose($f);
	
	// Everything is OK
	return true;
}

?>