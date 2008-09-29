<?php
/**
  * Package Management Module
  *
  * @package GloryLands
  * @subpackage Administration
  * @author John Haralampidis <johnys2@gmail.com>
  * @copyright Copyright (C) 2007-2008, John Haralampidis
  * @version 1.0
  */


/**
  * Recursive directory cleanup
  *
  * @param string $dir		The base directory
  * @return string		 	Returns the aliased file path
  */
function package_clear_dir($dir) {
	$base = DIROF("SYSTEM.ADMIN")."cache";
	$d = dir($base);
	while (false !== ($entry = $d->read())) {
		if (substr($entry,0,1)=='.') {
			// skip '.', '..', and hidden files (linux)
		} elseif (is_dir($base."/".$entry)) {
			package_clear_dir($base."/".$entry);
			rmdir($base."/".$entry);
		} else {
			unlink($base."/".$entry);
		}
	}
	$d->close();
	
}

/**
  * Private function that replaces the base file path with it's DIROF alias
  *
  * @param string $filepath	The source file path
  * @return string		 	Returns the aliased file path
  */
function package_path_alias($filepath) {
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
function package_path_expand($filepath, &$usedalias) {
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
  * @param int $pid 		The source package index
  * @param string $dest_dir	The destination directory (without trailling slash)
  * @param bool $relative	If TRUE the mapping file will contain relative paths rather than absolute
  * @param bool $move		If TRUE the source files and SQL entries will be removed
  * @return bool		 	Returns true on success or false otherways
  */
function package_archive_files($pid, $dest_dir, $relative=true, $move=false) {
	global $sql;
	
	// Obdain package file names
	$ans = $sql->query("SELECT * FROM `system_files` WHERE `package` = $pid");
	if (!$ans) return false;
	if ($sql->emptyResults) return true;
	
	// Initialize maping file
	// The mapping file provides the information required to rebuild the package structure
	$f = fopen($dest_dir.'/files.map','w');
	
	// Start copying and mapping files
	$i=0;
	while ($file = $sql->fetch_array_fromresults($ans)) {
		// Get some information
		$fname = $file['filename'];
		$shortname = 'file'.$i++;
		
		// Archive the file
		if (copy($fname, $dest_dir.'/'.$shortname)) {
				
			// Insert a mapping file entry
			if ($relative) $fname=package_path_alias($fname);
			fwrite($f, $shortname.'='.$file['version'].'='.$fname."\n");
			
			// Remove source data if told so
			if ($move) {
				if (unlink($file['filename'])) $sql->query("DELETE FROM `system_files` WHERE `index` = ".$file['index']);
				//echo "Removing {$file['filename']}\n";
			}
			
		} else {
			
			// Unable to proceed normally, notify a warning
			//fclose($f);
			//return false;

		}
	}
	
	// Close mapped file
	fclose($f);
	
	// Everything is OK
	return true;
}

/**
  * Imports all the files previously archived with package_archive_files, back into the
  * filesystem and the SQL database
  *
  * @param int $pid 		The destination package index
  * @param string $src_dir	The destination directory (without trailling slash)
  * @param bool $import		If TRUE the source files will be imported in the SQL
  * @return bool		 	Returns true on success or false otherways
  */
function package_restore_files($pid, $src_dir, $import=false) {
	global $sql;
	
	// Open maping file
	// The mapping file provides the information required to rebuild the package structure
	$f = @fopen($src_dir.'/files.map','r');
	if (!$f) return true;
	
	// Reset package files if we also use import
	if ($import) $sql->query("DELETE FROM `system_files` WHERE `package` = $pid");
	
	// Start copying and unmapping files
	while ($row = fgets($f)) {
	
		// Get some information
		$row = explode("=",$row);
		$fname = trim(package_path_expand($row[2], $ftype));
		$fversion = $row[1];
		$shortname = $row[0];		
		
		// Restore the file
		if (copy($src_dir.'/'.$shortname, $fname)) {
			// Import data if told so
			if ($import) {
				//echo "Importing file $fname, with type $ftype into package $pid\n";
				$sql->addRow('system_files', array(
					'type' => $ftype,
					'package' => $pid,
					'filename' => $fname,
					'version' => $fversion,
					'hash' => md5_file($fname)
				));			
			}
		}
	}
	
	// Close mapped file
	fclose($f);
	
	// Everything is OK
	return true;
}

/**
  * Extracts and saves a package's manifest parameters
  * Including:
  *  - General information
  *  - Dependencies
  *  - Dictionary Entries
  *  - Script hooks
  *  - SQL Files
  *  - Uninstall information
  *
  * @param int $pid 		The source package index
  * @param string $dest_dir	The destination directory (without trailling slash)
  * @param bool $move		If TRUE the source SQL entries will be removed
  * @return bool		 	Returns true on success or false otherways
  */
function package_archive_manifest($pid, $dest_dir, $move=false) {
	global $sql;
	
	// Obdain generic package info
	$ans=$sql->query("SELECT * FROM `system_packages` WHERE `index` = $pid");
	if (!$ans) return false;
	if ($sql->emptyResult) return false;
	$row = $sql->fetch_array(MYSQL_ASSOC);
	unset($row['index']);
	
	// Save general info into a structureal array
	$data = array('general' => $row);
	
	/* ------------ DICTIONARY -------------- */
	
	// Obdain dictionary entries
	$ans=$sql->query("SELECT * FROM `system_dictionaries` WHERE `package` = $pid");
	if (!$ans) return false;
	if ($sql->emptyResult) return false;
	$rows = $sql->fetch_array_all(MYSQL_ASSOC);
	foreach ($rows as $index => $row) {
		unset($row['index']);
		$rows[$index] = $row;
	}

	// Delete data if told so
	if ($move) $sql->query("DELETE FROM `system_dictionaries` WHERE `package` = $pid");

	// Save general info into a structureal array
	$data['dic'] = $rows;

	/* -------------- HOOKS -------------- */

	// Obdain hook entries
	$ans=$sql->query("SELECT * FROM `system_hooks` WHERE `package` = $pid");
	if (!$ans) return false;
	if ($sql->emptyResult) return false;
	$rows = $sql->fetch_array_all(MYSQL_ASSOC);
	foreach ($rows as $index => $row) {
		unset($row['index']);
		$rows[$index] = $row;
	}

	// Delete data if told so
	if ($move) $sql->query("DELETE FROM `system_hooks` WHERE `package` = $pid");

	// Save general info into a structureal array
	$data['hooks'] = $rows;

	/* ------------ SQL Files ------------ */

	// Obdain sql file entries
	$ans=$sql->query("SELECT * FROM `system_packages_install` WHERE `package` = $pid");
	if (!$ans) return false;
	if ($sql->emptyResult) return false;
	$rows = $sql->fetch_array_all(MYSQL_ASSOC);
	foreach ($rows as $index => $row) {
		unset($row['index']);
		$rows[$index] = $row;
	}

	// Delete data if told so
	if ($move) $sql->query("DELETE FROM `system_packages_install` WHERE `package` = $pid");

	// Save general info into a structureal array
	$data['sql'] = $rows;

	/* ------------ UNINSTALL ------------ */

	// Obdain uninstall entries
	$ans=$sql->query("SELECT * FROM `system_packages_uninstall` WHERE `package` = $pid");
	if (!$ans) return false;
	if ($sql->emptyResult) return false;
	$rows = $sql->fetch_array_all(MYSQL_ASSOC);
	foreach ($rows as $index => $row) {
		unset($row['index']);
		$rows[$index] = $row;
	}

	// Delete data if told so
	if ($move) $sql->query("DELETE FROM `system_packages_uninstall` WHERE `package` = $pid");

	// Save general info into a structureal array
	$data['uninstall'] = $rows;
	
	// Save this file
	file_put_contents($dest_dir.'/data', serialize($data));
	return true;
	
}

/**
  * Imports a package's manifest parameters
  * Including:
  *  - General information
  *  - Dependencies
  *  - Dictionary Entries
  *  - Script hooks
  *  - SQL Files
  *  - Uninstall information
  *
  * @param int $pid 		The destination package index
  * @param string $src_dir	The source directory (without trailling slash)
  * @return bool		 	Returns true on success or false otherways
  */
function package_restore_manifest($pid, $src_dir) {
	global $sql;

	// Try to load the data file
	if (!is_file($src_dir.'/data')) return false;
	$data = file_get_contents($src_dir.'/data');
	if (!$data) return false;
	$data = unserialize($data);
	if (!is_array($data)) return false;

	// Update package information
	$entry = $data['general'];
	$entry['index'] = $pid;
	$sql->replaceRow('system_packages', $entry);
	
	
	// Cleanup and import DICTIONARY
	$sql->query("DELETE FROM `system_dictionaries` WHERE `package` = $pid");
	foreach ($data['dic'] as $entry) {
		$entry['package'] = $pid;
		$sql->addRow('system_dictionaries', $entry);
	}

	// Cleanup and import HOOKS
	$sql->query("DELETE FROM `system_hooks` WHERE `package` = $pid");
	foreach ($data['hooks'] as $entry) {
		$entry['package'] = $pid;
		$sql->addRow('system_hooks', $entry);
	}

	// Cleanup and import SQL Files
	$sql->query("DELETE FROM `system_packages_install` WHERE `package` = $pid");
	foreach ($data['sql'] as $entry) {
		$entry['package'] = $pid;
		$sql->addRow('system_packages_install', $entry);
	}

	// Cleanup and import UNINSTALL information
	$sql->query("DELETE FROM `system_packages_uninstall` WHERE `package` = $pid");
	foreach ($data['uninstall'] as $entry) {
		$entry['package'] = $pid;
		$sql->addRow('system_packages_uninstall', $entry);
	}
	
	return true;
}

/**
  * Executes a package's uninstall scripts (obdained from SQL table 'system_packages_uninstall')
  *
  * @param int $pid 		  The package to uninstall
  * @param string $script_dir The directory where the uninstall script files are located (without trailing slash)
  * @param bool $disable 	  If TRUE the DISABLE scripts will be executed instead of the UNINSTALL ones
  * @return bool	 	 	  Returns true on success or false otherways
  */
function package_run_uninstall($pid, $script_dir, $disable=false) {
	global $sql;
	
	// Detect use
	$use = 'UNINSTALL';
	if ($disable) $use='DISABLE';
	
	// Obdain information
	$ans=$sql->query("SELECT * FROM `system_packages_uninstall` WHERE `package` = $pid AND `use` = '{$use}'");
	if (!$ans) return false;
	if ($sql->emptyResults) return true;
	
	// Start uninstalling
	while ($row = $sql->fetch_array_fromresults($ans, MYSQL_ASSOC)) {
		if ($row['umode'] == 'SCRIPT') {
			@include_once($script_dir.'/'.$row['data']);
		} elseif ($row['umode'] == 'SQL') {
			$sql->run($script_dir.'/'.$row['data']);
		} elseif ($row['umode'] == 'INLINE') {
			$ans = eval($row['data']);
		}
	}

	// Everything went ok
	return true;
}

/**
  * Executes a package's install scripts (obdained from SQL)
  *
  * @param int $pid 		  The package to install
  * @param string $script_dir The directory where the uninstall script files are located (without trailing slash)
  * @param bool $enable 	  If TRUE the ENABLE scripts will be executed instead of the INSTALL ones
  * @return bool	 	 	  Returns true on success or false otherways
  */
function package_run_install($pid, $script_dir, $enable=false) {
	global $sql;
	
	// Detect use
	$use = 'INSTALL';
	if ($enable) $use='ENABLE';
	
	// Obdain information
	$ans=$sql->query("SELECT * FROM `system_packages_install` WHERE `package` = $pid AND `use` = '{$use}'");
	if (!$ans) return false;
	if ($sql->emptyResults) return true;
	
	// Start uninstalling
	while ($row = $sql->fetch_array_fromresults($ans, MYSQL_ASSOC)) {
		if ($row['imode'] == 'SCRIPT') {
			@include_once($script_dir.'/'.$row['data']);
		} elseif ($row['imode'] == 'SQL') {
			$sql->run($script_dir.'/'.$row['data']);
		}
	}
	
	// Everything went ok
	return true;
}

/**
  * Removes every entry from SQL for this package
  *
  * @param int $pid 		  The package to install
  * @return bool	 	 	  Returns true on success or false otherways
  */
function package_uninstall_db($pid) {
	global $sql;

	$sql->query("DELETE FROM `system_files` WHERE `package` = $pid");
	$sql->query("DELETE FROM `system_dictionaries` WHERE `package` = $pid");
	$sql->query("DELETE FROM `system_hooks` WHERE `package` = $pid");
	$sql->query("DELETE FROM `system_packages_install` WHERE `package` = $pid");
	$sql->query("DELETE FROM `system_packages_uninstall` WHERE `package` = $pid");
	$sql->query("DELETE FROM `system_packages` WHERE `index` = $pid");

	return true;
}

/**
  * Removes every files used from this file
  *
  * @param int $pid 		  The package to install
  * @return bool	 	 	  Returns true on success or false otherways
  */
function package_uninstall_files($pid) {
	global $sql;
	
	// Get package information
	$ans=$sql->query("SELECT * FROM `system_packages` WHERE `package` = $pid");
	if (!$ans) return false;
	$info = $sql->fetch_array();

	// Find the files to erase
	$ans=$sql->query("SELECT * FROM `system_files` WHERE `package` = $pid");
	if (!$ans) return false;
	
	// Remove files
	while ($row = $sql->fetch_array(MYSQL_ASSOC)) {
		unlink($row['filename']);
	}

	// Remove package cache directories
	$guid = $info['guid'];
	$package_dir = DIROF('SYSTEM.ADMIN').'packages/'.$guid;
	package_clear_dir($package_dir);
	rmdir($package_dir);
	
	// Everything is ok
	return true;
}

?>