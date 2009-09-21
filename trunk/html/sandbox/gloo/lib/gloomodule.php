<?php

class GLOOModule {
	
	static public $cache;
	
	static public function mine($class) {
		// Get the class name
		$name = get_class($class);
		
		// Detect the GLModule from stack
		foreach (self::$cache as $module) {
			foreach ($module['classes'] as $class) {
				if ($class == $name) return $module['object'];
			}
		}
		
		// Return false if not returned by now
		return false;
	}

	static public function install($module) {
		if (!self::$cache) self::$cache=array();
		if (isset(self::$cache[$module])) {
			return self::$cache[$module];
		} else {
			return new self($module);
		}
	}

	// ======================================================================================================

	public $name;
	
	public $root;
	public $url;
	
	public $config;
	public $unitconfig;

	private function serialize_xml($simplexml) {
		$value = trim((string)$simplexml);
		$name = $simplexml->getName();
		$attrib = array();
		foreach ($simplexml->attributes() as $n => $v) {
			$attrib[$n] = (string)$v;
		}
		
		// Render each one of our children
		$vars = array();
		foreach ($simplexml->children() as $name => $c) {
			if (isset($vars[$name])) {
				if (isset($vars[$name][0])) {
					$vars[$name][] = $this->serialize_xml($c);
				} else {
					$vars[$name] = array(
						$vars[$name],
						$this->serialize_xml($c)
					);
				}
			} else {
				$vars[$name] = $this->serialize_xml($c);
			}			
		}
		
		// If vars are still empty, check for value-only node
		if (sizeof($vars) == 0) {
			if ($value != '') {
				return $value;
			}
		}
		
		// Append my atrib
		$vars = array_merge($attrib, $vars);
		
		// Return this stack
		return $vars;
	}

	public function __construct($name) {
		
		// Initialize variables
		$this->name = $name;
		$this->unitconfig = array();
		$this->root = str_replace('\\','/',dirname(dirname(__FILE__))).'/modules/'.$this->name;
		$this->url = 'modules/'.$name;
		
		// Initialize config
		if (!is_dir($this->root)) return false;		
		$this->config = $this->serialize_xml(simplexml_load_string(file_get_contents($this->root.'/config.xml')));
		
		// Prepare the array that will hold the names of 
		// all of our PHP classes (used by GLModule::mine() 
		// in order to find out the parent GLOOModule)
		$classes = array();
		
		// Include all the external files
		if (isset($this->config['provision'])) {
		
			// Make sure provision units is an array, even if it's only one
			if (!isset($this->config['provision']['unit'][0])) $this->config['provision']['unit']=array($this->config['provision']['unit']);			
			foreach ($this->config['provision']['unit'] as $unit) {				
				$id = $unit['name'];
				
				// Check for server file to include
				if (isset($unit['server'])) {
					include_once($this->root.'/'.$unit['server']['file']);
				
					// Store the class name
					$classes[] = $unit['server']['class'];
				}
				
				// Check for client file to include
				if (isset($unit['client'])) {
					GL::$stream->headers['js'][] = $this->url.'/'.$unit['client']['file'];
				}
				
				// Store the configuration of this unit (For quick fetching from $this->get_config_for()
				$this->unitconfig[$id] = $unit;
				
			}
		}
		
		// Register this instance on the static store
		if (!self::$cache) self::$cache=array();
		self::$cache[$this->name] = array(
			'object' => &$this,
			'classes' => $classes
		);	
	}
	
	public function get_config_for($object) {
		$name = get_class($object);
		if (!isset($this->unitconfig[$name])) return false;
		return $this->unitconfig[$name];
	}
	
	public function send($call) {
		
	}

}

?>