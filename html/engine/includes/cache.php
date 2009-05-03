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

global $cache_mode, $cache_mem, $cache_data;

define('CACHE_PERMANENT', 1);	// Permanent cache - Remains in storage even if the server is restarted
define('CACHE_GLOBAL', 1);		// Alias for permanent
define('CACHE_SESSION', 2);		// Session cache - Remains in memory only while the user stays logged
define('CACHE_LOCAL', 3);		// Local cache - Released when execution is completed

define('CACHE_FAST', 4);		// Fast flag - Defines that this operation must be done quickly
define('CACHE_BIG', 8);			// Large flag - Defines that the cache data are considerably big

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
  * @param	$cache_group string	The storage group name
  * @param	$cache_id	 mixed	The cache reference ID
  * @param	$data		 mixed	The structure to save on cache
  * @param	$pref_mode	 int	The prefered caching method to use
  *	@return	bool				TRUE if successfull, FALSE otherways
  *
  */
function gl_cache_set($cache_group, $cache_id, $data, $pref_mode=CACHE_LOCAL) {
	global $cache_mode, $cache_mem, $cache_data;

	// Check the target mode
	$pref_mode_hi = $pref_mode & 0x03;
	if ($pref_mode_hi == CACHE_LOCAL) {
			
		// Local cache is stored on local variables
		if (!isset($cache_data[$cache_group])) $cache_data[$cache_group]=array();
		$cache_data[$cache_group][$cache_id] = $data;
		return true;
		
		// NOTE: FAST and BIG flaags are not used here
		
	} elseif ($pref_mode_hi == CACHE_SESSION) {

		// Session cache is stored on the session variables
		if (!isset($_SESSION['cache'])) $_SESSION['cache'] = array();
		if (!isset($_SESSION['cache'][$cache_group])) $_SESSION['cache'][$cache_group]=array();
		$_SESSION['cache'][$cache_group][$cache_id] = $data;
		return true;

		// NOTE: FAST and BIG flaags are not used here
	
	} elseif ($pref_mode_hi == CACHE_PERMANENT) {
	
		// Check chat cache method to use
		if ($cache_mode == 'ram') {
			
			// Check what communication mode to use, based on the flags we used
			$mode = MEMCACHE_COMPRESSED;
			if ($pref_mode & CACHE_FAST) $mode=0;
			return $cache_mem->set($cache_group.'::'.$cache_id, $data, $mode);
			
		} else {
			$chunk = serialize($data);
			$md5 = md5($chunk);
			$cache = DIROF('SYSTEM.ENGINE').'data/cache/'.$cache_group.'-'.$cache_id;
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
}

/**
  * Obdain a cache structure
  *  
  * This function retrives a cached structure, previously saved with gl_cache_save
  *
  * @param	$cache_group string	The storage group name
  * @param	$cache_id	 mixed	The cache reference ID
  * @param	$pref_mode	 int	The prefered caching method to use
  *	@return	mixed				The cached structure or NULL if missing
  *
  */
function gl_cache_get($cache_group, $cache_id, $pref_mode=CACHE_LOCAL) {
	global $cache_mode, $cache_mem, $cache_data;

	// Check the target mode
	$pref_mode_hi = $pref_mode & 0x03;
	if ($pref_mode_hi == CACHE_LOCAL) {

		// Local cache is stored on local variables
		if (!isset($cache_data[$cache_group])) return NULL;
		return $cache_data[$cache_group][$cache_id];
		
		// NOTE: FAST and BIG flaags are not used here
		
	} elseif ($pref_mode_hi == CACHE_SESSION) {

		// Session cache is stored on the session variables
		if (!isset($_SESSION['cache'])) return NULL;
		if (!isset($_SESSION['cache'][$cache_group])) return NULL;
		return $_SESSION['cache'][$cache_group][$cache_id];

		// NOTE: FAST and BIG flaags are not used here
	
	} elseif ($pref_mode_hi == CACHE_PERMANENT) {
		
		// Check chat cache method to use
		if ($cache_mode == 'ram') {		
			return $cache_mem->get($cache_group.'::'.$cache_id);
			
		} else {
			$cache = DIROF('SYSTEM.ENGINE').'data/cache/'.$cache_group.'-'.$cache_id;
			$cache_data = $cache.'.dat';
			
			// If the file exists, return it
			if (file_exists($cache_data)) {
				$data = unserialize(file_get_contents($cache_data));
				return $data;
			} else {
				return NULL;
			}		
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