<?php

class GLExtensionConfig {		
	
	private $vars;
	private $schema;
	private $changed;
	
	public function __construct($schema) {
		
	}
	
	public function __destruct() {
		if (!$this->changed) return;
	}
	
	public function __get() {
	
	}
	
	public function __set() {	
		$this->changed = true;
	}
	
}

/**
  * The extension instance
  * 
  * This class contains all the configuration, and run-time information
  * that can be used by the extensions and the system.
  * It also provide access to the structures the extension provides.
  */
class GLExtension {

	/**
	  * Extension status. This could be:
	  *
	  *		"blank"		: If the class is instanced but no data are available
	  *		"error"		: If the loading process failed somewhere
	  *		"loaded"	: If the extension config is loaded but not yet parsed
	  *		"ready"		: If the config is parsed and the extension environment is built
	  *
	  * @var string
	  */
	public $status;
	
	/**
	  * General information extracted from the <info> tag on the XML
	  * config file
	  * @var array
	  */
	public $info;

	/**
	  * The configuration definition variables, extracted from the <config> tag on
	  * the XML config file
	  * @var array
	  */
	public $config_def;

	/**
	  * The configuration variables, wrapped around the configuration object.
	  * The GLExtensionConfig object will automatically save/load all the structures that
	  * were requested.
	  *
	  * @var GLExtensionConfig
	  */
	public $config;

	/**
	  * The extension's base directory
	  * @var string
	  */
	public $basedir;
	
	public $units;
	
	function get

}

class GLExtensions {

	static public $extensions;
	
	public function initialize() {
		
	}

	public function load($name) {				
		
	}	
	
	public function load_all() {
		
	}
	
	public function hybernate() {
		
	}	
	
}

?>