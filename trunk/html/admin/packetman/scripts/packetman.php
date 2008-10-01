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
 * The last error message that occured on package management system
 * @var package_error
 */
$package_error = '';


/**
  * Create the directory tree
  *
  * @param string $dir		The source package ID
  * @return bool		 	Returns TRUE if successfull or FALSE on error
  */
function package_create_dirtree($dir) {
	$dir = str_replace("\\","/", $dir);
	$parts = explode("/",$dir);
	$cdir = '';

	foreach ($parts as $part) {
		if ($cdir!='') {
			if (!is_dir($cdir.$part)) {
				mkdir($cdir.$part);
			}
			$cdir .= $part.'/';
		} else {
			$cdir = $part.'/';
		}
	}
}

/**
  * Copy file and import it into SQL
  *
  * @param int $pid			The package ID that will receive the files
  * @param string $type		The file types (DIROF enum) being copied
  * @param string $src		The source directory or file to be copied
  * @param string $dst_dir	The destination directory (without trailing slash)
  * @return bool		 	Returns TRUE if everything is ok, or FALSE on error
  */
function package_copy_file($pid, $type, $src, $dst_dir) {
	global $sql,$package_error;

	// Make sure destination directory exists
	if (!is_dir($dst_dir)) package_create_dirtree($dst_dir);
	
	// Copy the file
	$fname = $dst_dir.'/'.basename($src);
	$ans=copy($src, $fname);
	
	// If the copy failed, do not import the file into SQL
	// and raise a warning
	if (!$ans) {
		$package_error.=" &bull; Error copying file $src to $fname\n";
	} else {		
		// Import file entry into SQL
		$sql->addRow('system_files', array(
			'package' => $pid,
			'filename' => $fname,
			'type' => $type,
			'version' => 1,
			'hash' => md5_file($src)
		));
	}
}

/**
  * Recursive dir-to-dir copy
  *
  * @param int $pid			The package ID that will receive the files
  * @param string $type		The file types (DIROF enum) being copied
  * @param string $src		The source directory or file to be copied
  * @param string $dst_dir	The destination directory (without trailing slash)
  * @return bool		 	Returns TRUE if everything is ok, or FALSE on error
  */
function package_copy_recursive($pid, $type, $src, $dst_dir) {
	global $sql,$package_error;
	
	// If the source is directory, traverse and copy the contents
	$d = dir($src);	
	while (false !== ($entry = $d->read())) {
		if (substr($entry,0,1)=='.') {
			// skip '.', '..', and hidden files (linux)
		} elseif (is_dir($src.'/'.$entry)) {
			package_copy_recursive($pid, $type, $src.'/'.$entry, $dst_dir.'/'.$entry);
		} else {
			package_copy_file($pid, $type, $src.'/'.$entry, $dst_dir);
		}
	}
	$d->close();
	
	return true;
}

/**
  * Recursive directory cleanup
  *
  * @param string $dir		The base directory
  * @return string		 	Returns the aliased file path
  */
function package_clear_dir($dir) {
	$base = $dir;
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
	global $sql, $package_error;
	$package_error='';
	
	// Obdain package file names
	$ans = $sql->query("SELECT * FROM `system_files` WHERE `package` = $pid");
	if (!$ans) {
		$package_error.=" &bull; ".$sql->getError()."\n";
		return false;
	}
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
				if (unlink($file['filename'])) {
					$sql->query("DELETE FROM `system_files` WHERE `index` = ".$file['index']);
				} else {
					$package_error.=" &bull; Error deleting file $fname\n";
				}
			}
			
		} else {		
			$package_error.=" &bull; Error copying file {$fname} to {$dest_dir}/{$shortname}\n";
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
	global $sql, $package_error;
	$package_error='';
	
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
				$ans=$sql->addRow('system_files', array(
					'type' => $ftype,
					'package' => $pid,
					'filename' => $fname,
					'version' => $fversion,
					'hash' => md5_file($fname)
				));
				if (!$ans) {
					$package_error.=" &bull; Error copying file {$src_dir}/{$shortname} to {$fname}\n";
				}
			}
		} else {
			$package_error.=" &bull; Error copying file {$src_dir}/{$shortname} to {$fname}\n";
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
	global $sql, $package_error;
	$package_error='';
	
	// Obdain generic package info
	$ans=$sql->query("SELECT * FROM `system_packages` WHERE `index` = $pid");
	if (!$ans) {
		$package_error.=" &bull; ".$sql->getError()."\n";
		return false;
	};
	if ($sql->emptyResult) {
		$package_error.=" &bull; Cannot find plugin with index #{$pid}\n";
		return false;
	};
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
	global $sql, $package_error;
	$package_error='';

	// Try to load the data file
	if (!is_file($src_dir.'/data')) return false;
	$data = file_get_contents($src_dir.'/data');
	if (!$data) {
		$package_error.=" &bull; Manifest data are empty\n";
		return false;
	};
	$data = unserialize($data);
	if (!is_array($data)) {
		$package_error.=" &bull; Invalid manifest file\n";
		return false;
	};

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
	global $sql, $package_error;
	$package_error='';
	
	// Detect use
	$use = 'UNINSTALL';
	if ($disable) $use='DISABLE';
	
	// Obdain information
	$ans=$sql->query("SELECT * FROM `system_packages_uninstall` WHERE `package` = $pid AND `use` = '{$use}'");
	if (!$ans) {
		$package_error.=" &bull; ".$sql->getError()."\n";
		return false;
	};
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
	global $sql, $package_error;
	$package_error='';
	
	// Detect use
	$use = 'INSTALL';
	if ($enable) $use='ENABLE';
	
	// Obdain information
	$ans=$sql->query("SELECT * FROM `system_packages_install` WHERE `package` = $pid AND `use` = '{$use}'");
	if (!$ans) {
		$package_error.=" &bull; ".$sql->getError()."\n";
		return false;
	};
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
	global $sql, $package_error;
	$package_error='';

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
	global $sql, $package_error;
	$package_error='';
	
	// Get package information
	$ans=$sql->query("SELECT * FROM `system_packages` WHERE `package` = $pid");
	if (!$ans) {
		$package_error.=" &bull; ".$sql->getError()."\n";
		return false;
	};
	$info = $sql->fetch_array();

	// Find the files to erase
	$ans=$sql->query("SELECT * FROM `system_files` WHERE `package` = $pid");
	if (!$ans) {
		$package_error.=" &bull; ".$sql->getError()."\n";
		return false;
	};
	
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

/**
  *
  * Installation preparation function
  *
  * This function prepares for installation by importing the package
  * into the SQL, obdaining a package ID and setting its status to 'INSTALLING'
  *
  * @param string $src_dir	The directory that contains the extracted archive
  * @return bool	 	 	Returns true on success or false otherways
  */
function package_install_prepare($src_dir) {
	global $sql, $package_error;
	$package_error='';

	// Load manifest
	$p = xml_parser_create();
	if (!p) {
		$package_error.=" &bull; Cannot initialize XML parser\n";
		return false;
	}
	if (!is_file($src_dir.'/package.xml')) {
		$package_error.=" &bull; Package manifest not found\n";
		return false;
	
	}
	xml_parse_into_struct($p,file_get_contents($src_dir."/package.xml"),$vals,$index);
	xml_parser_free($p);

	// Prepare SQL record
	$package = array();
	if (isset($vals[$index['GUID'][0]]['value'])) $package['guid'] = $vals[$index['GUID'][0]]['value'];
	if (isset($vals[$index['NAME'][0]]['value'])) $package['name'] = $vals[$index['NAME'][0]]['value'];
	if (isset($vals[$index['VERSION'][0]]['value'])) $package['version'] = $vals[$index['VERSION'][0]]['value'];
	if (isset($vals[$index['DESCRIPTION'][0]]['value'])) $package['description'] = $vals[$index['DESCRIPTION'][0]]['value'];
	if (isset($vals[$index['AUTHOR'][0]]['value'])) $package['author'] = $vals[$index['AUTHOR'][0]]['value'];
	if (isset($vals[$index['COPYRIGHT'][0]]['value'])) $package['copyright'] = $vals[$index['COPYRIGHT'][0]]['value'];
	$package['installdate'] = time();
	$package['status'] = 'INCOMPLETED';
	$package['type'] = 'FILES';
	$package['require'] = '';

	// Check for errors
	if (!isset($package['guid'])) {
		$package_error.=" &bull; This package has no GUID associated with!\n";
		return false;
	}
	if (!isset($package['version'])) {
		$package_error.=" &bull; This package has no version identifier!\n";
		return false;
	}

	// Check for preexisting version
	$ans = $sql->query("SELECT * FROM `system_packages` WHERE `guid` = '".$package['guid']."'");
	if (!$ans) {
		$package_error.=" &bull; ".$sql->getError()."\n";
		return false;
	}
	if (!$sql->emptyResults) {
		$row = $sql->fetch_array();
		if ($row['version'] == $package['version']) {
			$package_error.=" &bull; You already have this version of this package installed!\n";
			return false;
		} elseif ($row['version'] < $package['version']) {
			$package_error.=" &bull; You have a previous version of this package already installed. Please remove it first!\n";
			return false;
		} 
	}

	// Calculate dependencies
	$depends = array();
	if (isset($index['DEPENDENCY'])) {
		foreach ($index['DEPENDENCY'] as $id) {
		
			// Get the dependency information
			$guid = $vals[$id]['value'];	
			$ver = $vals[$id]['version'];	
			$name = $vals[$id]['name'];	
			
			// Import dependency
			if ($guid!='') {
			
				// Check for existing depndencies
				if ($ver!='') {
					$sql->query("SELECT `version` FROM `system_packages` WHERE `guid` = '{$guid}' ");
					if (!$sql->emptyResults) {
						$package_error.=" &bull; The required dependency <b>$name</b> is missing!\n";
						return false;
					} else {
						$row = $sql->fetch_array(MYSQL_NUM);
						$my_ver = $row[0];
						if (!my_ver < $ver) {
							$package_error.=" &bull; This package requires <b>$name</b> version <b>$ver</b> or grater! You have version <b>$my_ver</b>\n";
							return false;
						}
					}
				} else {
					if (!$sql->poll("SELECT * FROM `system_packages` WHERE `guid` = '{$guid}'")) {
						$package_error.=" &bull; The required dependency <b>$name</b> is missing!\n";
						return false;
					}
				}
				
				// Import dependency			
				if ($ver=='') {
					array_push($depends, array(
						'guid' => $guid,
						'name' => $name
					));
				} else {
					array_push($depends, array(
						'guid' => $guid,
						'ver' => $ver,
						'name' => $name
					));
				}
			}
		}
	}
	$package['require'] = serialize($depends);

	// Insert package to SQL
	$ans=$sql->addRow('system_packages', $package);
	if (!$ans) {
		$package_error.=" &bull; ".$sql->getError()."\n";
		return false;
	}

	// Get package ID
	$ans=$sql->query('SELECT `index` FROM `system_packages` ORDER BY `index` DESC LIMIT 0,1');
	if (!$ans) {
		$package_error.=" &bull; ".$sql->getError()."\n";
		return false;
	}
	$pid = $sql->fetch_array();
	$package['index'] = $pid[0];
	
	// Return data
	return $package;
}

/**
  * Complete install a package from an extracted directory
  *
  * @param string $src_dir	The directory that contains the extracted archive
  * @param string $dst_dir	The directory that will hold the local cache
  * @param array $pinfo		The package info as obdained from package_install_prepare
  * @return bool	 	 	Returns true on success or false otherways
  */
function package_install($src_dir, $dst_dir, $pinfo) {
	global $sql, $package_error;
	$package_error='';
	
	// Get PID from pinfo
	$pid = $pinfo['index'];
	
	// Load manifest
	$p = xml_parser_create();
	if (!p) {
		$package_error.=" &bull; Cannot initialize XML parser\n";
		return false;
	}
	if (!is_file($src_dir.'/package.xml')) {
		$package_error.=" &bull; Package manifest not found\n";
		return false;
	
	}
	xml_parse_into_struct($p,file_get_contents($src_dir."/package.xml"),$vals,$index);
	xml_parser_free($p);


	
	// Make sure local cache exists
	if (!is_dir($dst_dir)) {
		$package_error.=" &bull; Local package cache directory does not exists\n";
		return false;
	}
	$dst_dir.="/".$pinfo['guid'];
	if (!is_dir($dst_dir)) mkdir($dst_dir);
	
	// Make local cache tree
	if (!is_dir($dst_dir.'/disabled')) mkdir($dst_dir.'/disabled');
	if (!is_dir($dst_dir.'/scripts')) mkdir($dst_dir.'/scripts');
	if (!is_dir($dst_dir.'/source')) mkdir($dst_dir.'/source');

	// Install files
	if (isset($index['FILE'])) {
	
		foreach ($index['FILE'] as $id) {
		
			$type = "SYSTEM";
			$subdir = "/";
			$recurse = false;
			$replace = false;
					
			if (isset($vals[$id]['attributes']['TYPE'])) $type = $vals[$id]['attributes']['TYPE'];
			if (isset($vals[$id]['attributes']['SUBDIR'])) $subdir = $vals[$id]['attributes']['SUBDIR'];
			if (isset($vals[$id]['attributes']['RECURSE'])) $recurse = (strtolower($vals[$id]['attributes']['RECURSE']) == 'yes');
			if (isset($vals[$id]['attributes']['REPLACE'])) $replace = (strtolower($vals[$id]['attributes']['REPLACE']) == 'yes');
			if (isset($vals[$id]['value'])) {
				
				$file = $vals[$id]['value'];
				if ($subdir=='/') $subdir='';
				$target_path = DIROF($type,true).$subdir;
				
				if (!$recurse) {
					package_copy_file($pid, $type, $src_dir."/".$file, $target_path);
				} else {
					package_copy_recursive($pid, $type, $src_dir."/".$file, $target_path);
				}
			}	
		}
	}

	// Update dictionary entries (if set)
	if (isset($index['DICTIONARY']) && isset($index['ENTRY'])) {
		foreach ($index['ENTRY'] as $id) {
			
			$type = "GUID";
			$value = false;
			if (isset($vals[$id]['attributes']['TYPE'])) $type = strtoupper($vals[$id]['attributes']['TYPE']);
			if (isset($vals[$id]['attributes']['VALUE'])) {	
				// If value exists use FIXED mode
				$mode = 'FIXED';
				$value = $vals[$id]['attributes']['VALUE'];
			} else {
				// If it does not exists, find the last used
				$mode = "DYNAMIC";
				$last = $sql->query_and_get_value("SELECT MAX(`value`) FROM `system_dictionaries` WHERE `group` = '{$type}'");
				if ($last=='') $last=0;
				$last++;
				$value = $last;				
			}
			$name = $vals[$id]['value'];
	
			// Import entry
			$sql->addRow('system_dictionaries', array(
				'group' => $type,
				'name' => $name,
				'value' => $value,
				'mode' => $mode,
				'package' => $pid
			));		
		}
	}

	// Update hook entries (if set)
	if (isset($index['HOOKS']) && isset($index['HOOK'])) {	
		foreach ($index['HOOK'] as $id) {
			if (isset($vals[$id]['attributes']['EVENT']) && isset($vals[$id]['attributes']['FUNCTION'])) {
				
				$event = $vals[$id]['attributes']['EVENT'];
				$function = $vals[$id]['attributes']['FUNCTION'];
				$file = $vals[$id]['value'];

				// Import entry
				$sql->addRow('system_hooks', array(
					'hook' => $event,
					'filename' => $file,
					'function' => $function,
					'active' => 'YES',
					'package' => $pid
				));		
				
			}	
		}
	}

	// Import and run Install/Uninstall/Disable/Enable SQL Entries
	if (isset($index['SQL'])) {
		foreach ($index['SQL'] as $id) {
		
			$file = $vals[$id]['value'];
			$use = '';
			if (isset($vals[$id]['attributes']['USE'])) $use = $vals[$id]['attributes']['USE'];
			
			// Enable/Install goes to system_packages_install
			$valid = false;
			if (($use == 'INSTALL') || ($use == 'ENABLE')) {
				$valid = true;
				$sql->addRow('system_packages_install', array(
					'package' => $pid,
					'imode' => 'SQL',
					'use' => $use,
					'data' => $file
				));
			} elseif (($use == 'UNINSTALL') || ($use == 'DISABLE')) {
				$sql->addRow('system_packages_uninstall', array(
					'package' => $pid,
					'umode' => 'SQL',
					'use' => $use,
					'data' => $file
				));
			}
			
			// Was the import valid?
			if (!valid) {			
				// Copy the file if yes
				if (!copy($src_dir.'/scripts/'.$file, $dst_dir.'/scripts/'.$file)) {
					$package_error.=" &bull; Cannot copy file {$src_dir}/scripts/{$file} to {$dst_dir}/scripts/{$file}!\n";
				};
			}
		}		
	}

	// Import and run Install/Uninstall/Disable/Enable Script Entries
	if (isset($index['SCRIPT'])) {
		foreach ($index['SCRIPT'] as $id) {
		
			$file = $vals[$id]['value'];
			$use = '';
			if (isset($vals[$id]['attributes']['USE'])) $use = $vals[$id]['attributes']['USE'];
			
			// Enable/Install goes to system_packages_install
			$valid = false;
			if (($use == 'INSTALL') || ($use == 'ENABLE')) {
				$valid = true;
				$sql->addRow('system_packages_install', array(
					'package' => $pid,
					'imode' => 'SCRIPT',
					'use' => $use,
					'data' => $file
				));
			} elseif (($use == 'UNINSTALL') || ($use == 'DISABLE')) {
				$sql->addRow('system_packages_uninstall', array(
					'package' => $pid,
					'umode' => 'SCRIPT',
					'use' => $use,
					'data' => $file
				));
			}
			
			// Was the import valid?
			if (!valid) {
				// Copy the file if yes
				if (!copy($src_dir.'/scripts/'.$file, $dst_dir.'/scripts/'.$file)) {
					$package_error.=" &bull; Cannot copy file {$src_dir}/scripts/{$file} to {$dst_dir}/scripts/{$file}!\n";
				};				
			}
		}		
	}

	// Installation completed. Update package status
	$ans=$sql->query("UPDATE `system_packages` SET `status` = 'ACTIVE' WHERE `index` = {$pid}");
	if (!$ans) {
		$package_error.=" &bull; ".$sql->getError()."\n";
		return false;
	};
	
	// Everything was OK
	return true;
}

?>