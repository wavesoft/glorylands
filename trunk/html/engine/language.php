<?php

/**
 * Multilanguage 
 * Generecit class for all multilanguage output strings
 * @author 3DEN
 */
class Language{
	
	var $_interface = array();
	
	/** @var Array of Translactions */
	var $_langarray = array();
	var $_lang = null;
	
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
		$this->_lang = $lang;
	}
	
	/**
	 * 
	 * @return 
	 * @param $lang Object[optional]
	 */
	function &getInstance($lang='en-US'){
		static $langs;
		if(!empty($langs)){
			return $langs;
		}
		$langs = new Language($lang);
		return $langs;		
	}
	
	/**
	 * Function used for tranlation
	 * @return word translated
	 * @param $w Word to Translate
	 * @param $group lang file
	 */
	function translate($w, $group='game'){
		//$group = 
		if( !array_search($group, $this->_interface) ){
			include_once 'languages/'.$this->_lang .'/'.$group.'.lang';
			$this->_interface[] = $group;
			$this->_langarray = array_merge($this->_langarray, $dictionary); 
		}
		
		return (isset($this->_langarray[$w]))? // if find tranlation 
			$this->_langarray[$w]: // return tranlation 
			$w; // return gived word
	}
	
}
?>