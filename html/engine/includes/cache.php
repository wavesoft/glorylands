<?php
/**
  * <h3>GloryLands Caching system</h3>
  *
  * This file contains all the caching functions that are used to speed-up the program
  * execution
  *
  * @package GloryLands
  * @subpackage Engine
  * @author John Haralampidis <johnys2@gmail.com>
  * @copyright Copyright (C) 2007-2008, John Haralampidis
  * @version 1.0
  */

global $cache_mode, $cache_mem;

define('CACHE_AUTO', 0);	// Automatically detect the cache target
define('CACHE_FAST', 1);	// Cache operations are time-depending. Store it on RAM or session
define('CACHE_LARGE', 2);	// A large ammout of data must be cached. Store it on Disk
define('CACHE_STATIC', 3);	// The data must be preserved. Store it on Disk

/**
  * Detect the cache mode to use and connect on MemCache daemon
  * if needed
  */
if ($_CONFIG[MCACHE][ENABLE]) {
	if (extension_loaded('memcache')) {
		$cache_mem = new Memcache;		
		if (!$cache_mem->connect($_CONFIG[MCACHE][HOST], $_CONFIG[MCACHE][PORT])) {
			$cache_mode='file';
		} else {		
			$cache_mode='ram';
		}
	} else {
		$cache_mode='file';
	}
} else {
	$cache_mode='file';
}

/**
  * Store a cache structure
  *  
  * This function stores a structure into the game cache
  *
  * @param	$cache_id	string	The cache name
  * @param	$data		mixed	The structure to save on cache
  * @param	$pref_mode	int		The prefered caching method to use
  *	@return	bool				TRUE if successfull, FALSE otherways
  *
  */
function gl_cache_save($cache_id, $data, $pref_mode=0) {
	global $cache_mode, $cache_mem;

	// Check chat cache method to use
	if ($cache_mode == 'ram') {
		return $cache_mem->set($cache_id, $data, MEMCACHE_COMPRESSED);
	} else {
		$chunk = serialize($data);
		$md5 = md5($chunk);
		$cache = DIROF('SYSTEM.ENGINE').'data/cache/'.$cache_id;
		$cache_data = $cache.'.dat';
		$cache_md5 = $cache.'.md5';
		
		// If the file exists, check if we don't have changes
		if (file_exists($cache_md5)) {
			$c_md5 = file_get_contents($cache_md5);
			if ($c_md5 == $md5) return true;
		}
		
		// Save the cache data and the MD5 signature
		file_put_contents($cache_data, $chunk);
		file_put_contents($cache_md5, $md5);
		return true;
	}
	
}

/**
  * Obdain a cache structure
  *  
  * This function retrives a cached structure, previously saved with gl_cache_save
  *
  * @param	$cache_id	string	The cache name
  *	@return	mixed				The cached structure
  *
  */
function gl_cache_get($cache_id) {

	// Check chat cache method to use
	if ($cache_mode == 'ram') {
	
		return $cache_mem->get($cache_id);
		
	} else {
		$cache = DIROF('SYSTEM.ENGINE').'data/cache/'.$cache_id;
		$cache_data = $cache.'.dat';
		
		// If the file exists, check if we don't have changes
		if (file_exists($cache_md5)) {
			$data = unserialize(file_get_contents($cache_data));
			return $data;
		} else {
			return false;
		}		
	}

}

/**  
  * Synchronize Cache with game-wide variables
  *
  */
function gl_sync_cache() {

}

?>