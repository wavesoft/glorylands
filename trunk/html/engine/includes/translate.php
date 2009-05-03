<?php
/**
  * Multilanguage translation support
  *
  * This file contains all the functions needed to convert multilanguage tags {#TAG#} into
  * texts.
  *
  * @package GloryLands
  * @subpackage Engine
  * @author John Haralampidis <johnys2@gmail.com>
  * @copyright Copyright (C) 2007-2008, John Haralampidis
  * @version 1.0
  */
global $translate_time;
$translate_time = 0;


/**
  * Load a translation dictionary
  *  
  * This function loads a translation file into the dictionary cache
  *
  * @param	$language	string	The language file name to use
  *	@return	array				The dictionary in array structure
  *
  */
function gl_load_dictionary($language) {
	// Prepare dictionary
	$dictionary = array();
	$file = DIROF("DATA.LANG").$language.'.dat';
	
	// Load dictionary
	$lines = explode("\n",file_get_contents($file));
	foreach ($lines as $line) {
		
		// Cleanup messy lines
		$line = trim(str_replace("\r","",$line));
		if ($line!='') {
			// If the line is not blank, try to load it
			$parts = explode("=",$line);
			$parts[0] = strtoupper(trim($parts[0]));
			$parts[1] = str_replace('"','',trim($parts[1]));
			$dictionary[$parts[0]] = $parts[1];
		}
		
	}
	
	return $dictionary;
}

/**
  * Translate a string
  *  
  * This function replaces all the tags found in the string with the language equilavent
  *
  * @param	$buffer	string	The source string
  * @param	$lang	string	(Optional) The language to use (if ommited, the config language will be used)
  *	@return	string			The result string
  *
  */
function gl_translate_string($buffer, $lang=false) {
	global $_CONFIG, $dictionary;
	
	// Load the dictionary for the translation
	if (!$lang) $lang = $_CONFIG[GAME][LANG];
	if (!isset($dictionary)) {
		$dictionary = gl_cache_get('translations', $lang, CACHE_GLOBAL);
		if (!$dictionary) {
			$dictionary = gl_load_dictionary($lang);
			gl_cache_set('translations', $lang, $dictionary, CACHE_GLOBAL);
		}
	}

	// Perform the translation
	return preg_replace_callback('/{#([^#]*)#}/i', 
		create_function(
			'$matches',
			'global $dictionary; if (isset($dictionary[strtoupper($matches[1])])) { return $dictionary[$matches[1]]; } else { return gl_ucfirst(mb_strtolower(str_replace("_"," ",$matches[1]))); }'
		), 
		$buffer);	
}

/**
  * Translate a structure
  *  
  * This function replaces all the tags found inside the strings inside the structure,
  * with the international language equilavent
  *
  * @param	$struct	any		The source string, object or array
  * @param	$lang	string	(Optional) The language to use (if ommited, the config language will be used)
  *	@return	string			The translated structure
  *
  */
function gl_translate(&$struct, $lang=false) {
	global $translate_time;
	$start = microtime(true);
	
	if (is_object($struct)) {
		// Object structures are looped for their variables
		foreach ($struct as $var => $value) {
			$struct->$var = gl_translate($value, $lang);
		}
	} elseif (is_array($struct)) {
		// Array structures are looped
		foreach ($struct as $var => $value) {
			$struct[$var] = gl_translate($value, $lang);
		}	
	} elseif (is_string($struct) && !is_numeric($value)) {
		// Strings are directly translated
		$struct = gl_translate_string($struct, $lang);
	} else {
		// Any other data structures are ignored
	}
	
	$translate_time += microtime(true)-$start;
	return $struct;
}

?>