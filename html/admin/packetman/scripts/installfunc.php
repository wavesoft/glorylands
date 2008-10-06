<?php
/**
  * Installation helping function
  *
  * This file contains some functions that felps the package extraction
  * and installation procedure
  *
  * @package GloryLands
  * @subpackage Administration
  * @author John Haralampidis <johnys2@gmail.com>
  * @copyright Copyright (C) 2007-2008, John Haralampidis
  * @version 1.0
  */
  
/**
  * Extract an archive to directory
  *
  * @param string $file		The archive to extract
  * @param string $cache	The destination directory
  * @return bool		 	Returns true if everything was successfull or false otherways
  */
function package_extract_file($file, $cache) {
	global $sql, $package_error;

	if (strtolower(substr($file,-4)) == '.zip') {	
		// ZIP Archive
	
		$zip = new PclZip($file);
		$files=$zip->extract($cache);	
		if (!$files) {
			$extractok = false;
			$package_error=' &bull; Cannot extract this ZIP archive';
			return false;
		} else {
			return true;
		}
	
	} elseif (strtolower(substr($file,-4)) == '.tar') {	
		// TAR Archive
	
		$files=PclTarExtract($file, $cache);
		if (!$files) {
			$extractok = false;
			$package_error=' &bull; Cannot extract this TAR archive';
			return false;
		} else {
			$extractok = true;
			return true;
		}
	
	} elseif ((strtolower(substr($file,-7)) == '.tar.gz') || (strtolower(substr($file,-4)) == '.tgz')) {	
		// GZip TAR Archive
	
		if (!function_exists('gzopen')) {
			$package_error=' &bull; Your server do not support GZip compression!';
			return false;
		}
		
		$fin = gzopen($file,"r");
		$fout = fopen($cache."/package.tar", "w");
		while ($buf = gzread($fin,10240)) {
			fwrite($fout,$buf);
		}
		fclose($fin);
		fclose($fout);
	
		$files=PclTarExtract($cache."/package.tar", $cache);
		if (!$files) {
			$extractok = false;
			$package_error=' &bull; Cannot extract this TAR archive';
			return false;
		} else {
			$extractok = true;
			return true;
		}
	
	} elseif (strtolower(substr($file,-8)) == '.tar.bz2') {	
		// BZ2 TAR Archive
		if (!function_exists('bzopen')) {
			$package_error=' &bull; Your server do not support Bzip2 compression!';
			return false;
		}
	
		$fin = bzopen($file,"r");
		$fout = fopen($cache."/package.tar", "w");
		while ($buf = bzread($fin,10240)) {
			fwrite($fout,$buf);
		}
		fclose($fin);
		fclose($fout);

		$files=PclTarExtract($cache."/package.tar", $cache);
		if (!$files) {
			$extractok = false;
			$package_error=' &bull; Cannot extract this TAR archive';
			return false;
		} else {
			$extractok = true;
			return false;
		}
	} else {
		$package_error=' &bull; Cannot identify the archive type';
		return false;
	}

}
  
?>