<?php

/**
 * Multilanguage 
 * Generecit class for all multilanguage output strings
 * @author 3DEN
 */
class Language{
	
	var $_interface = array();
	/**@var Array of Translactions */
	var $_lang = array();
	
	/** @varTranslation Type [local, remote] */
	var $_type = null;
	
	/** @var webserver used for remote tranlation */
	var $_dict_url = null;
	
	/**
	 * 
	 * @return 
	 * @param $lang Object
	 * @param $type Object[optional]
	 */
	function __construct($lang, $type='local'){
		$this->_type = $type;
		$this->_dict_url = $lang;
			
	}
	
	
	/**
	 * Function usec for tranlation
	 * @return word translated
	 * @param $w Word to Translate
	 * @param $group lang file
	 */
	function translate($w, $group='game'){
		//$group = 
		if( !array_search($group, $this->_interface) ){
			include_once 'languages/'.$lang.'/'.$group.'.lang';
			$this->_interface[] = $group;
			$this->_lang = array_merge($this->_lang, $dictionary); 
		}
		
		return (isset($this->_lang[$w]))? // if find tranlation 
			$this->_lang[$w]: // return tranlation 
			$w; // return gived word
	}
	
}
