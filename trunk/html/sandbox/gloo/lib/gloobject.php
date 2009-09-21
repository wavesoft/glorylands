<?php

class GLOOObject extends GLDOMElement {

	/**
	  * A reference to the root module object
	  * @var GLOOModule
	  */
	protected $module;
	
	/**
	  * The Link synchronization number between PHP and Javscript object
	  * @var int
	  */
	public $linkID;
	
	/**
	  * The Unit configuration, as obtained from the config.xml
	  * @var array
	  */
	protected $config;
	
	public function __sleep() {
		// Remove the reference to the $module. We don't need it
		$vars = get_object_vars($this);
		unset($vars['module']);
		$vars = array_keys($vars);
		return $vars;		
	}
	
	public function __wakeup() {
		// Re-load the$module reference
		$this->module = GLOOModule::mine($this);
	}
		
	public function __construct() {
	
		// Initialize some variables
		$this->vars = array();
	
		// Detect my GLOOModule 
		// (Instead of using GLOOMOdule::fetch(Class) we can directly instance this class
		//  and let the mine() function to do the detection.
		$this->module = GLOOModule::mine($this);
		$this->config = $this->module->get_config_for($this);				
				
		// Initialize the DOMObject, using the class name as DOM class name
		parent::__construct(get_class($this));
		
		// If we have client script, make the binding
		if (isset($this->config['client'])) {
			$client_class = $this->config['client']['class'];
			$this->linkID = GLOOLink::register($this, $client_class);			
		} else {
			$this->linkID = false;
		}
		
	}

	/** 
	  * An inheritable function to notify a variable update
	  * [INHERITED FROM GLDataset]
	  *
	  * @param	string	$name	The variable name that was updated
	  * @param	mixed	$value	The variable value
	  */
	protected function var_changed($name, &$value) {
		// (Inheritable)
	}

	/** 
	  * An inheritable function to notify a variable removal
	  * [INHERITED FROM GLDataset]
	  *
	  * @param	string	$name	The variable name that was removed
	  */
	protected function var_removed($name) {
		// (Inheritable)
	}

	/** 
	  * An inheritable function to notify an object attachment
	  * [INHERITED FROM GLDataset]
	  *
	  * @param	string	$name	The variable name that was updated
	  * @param	object	$object	The object that was added
	  */
	protected function object_added($name, &$object) {
		if ($object instanceof GLDOMElement) {
			$this->add_child($object);
		}
	}

	/** 
	  * An inheritable function to notify an object detachment
	  * [INHERITED FROM GLDataset]
	  *
	  * @param	string	$name	The variable name that was updated
	  * @param	object	$object	The object that was removed
	  */
	protected function object_removed($name, &$object) {
		if ($object instanceof GLDOMElement) {
			$this->remove_child($object);
		}
	}
	
	/**
	  * Prepare the variables that will be sent to the JS interface
	  */
	public function prepare_vars() {
		return $this->vars;
	}

	/**
	  * Bind the object into the rendering template
	  */
	public function _bind($template) {
		
	}

	/**
	  * Perform a call to the JS interface
	  */
	public final function _call($call) {
		$vars = func_get_args();
		array_unshift($vars);
		$this->module->call($call, $vars);
	}
}

?>