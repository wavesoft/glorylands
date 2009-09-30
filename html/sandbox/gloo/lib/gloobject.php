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

	/**
	  * A flag that defines that objec variables are synced with the JS
	  * @var bool
	  */
	public $synced;
	
	public function __sleep() {
		// Remove the reference to the $module. We don't need it and
		// it also causes trouble on serialization
		$vars = get_object_vars($this);
		unset($vars['module']);
		$vars = array_keys($vars);
		return $vars;		
	}
	
	public function __wakeup() {
		// Re-load the $module reference
		$this->module = GLOOModule::mine($this);
	}
		
	public function __construct() {
	
		// Initialize my variables
		$this->vars = array();
		$this->linkID = false;
		$this->synced = false;
	
		// Detect my GLOOModule 
		// (Instead of using $v = GLOOMOdule::fetch(Class) we can directly instance this class, using $v = new Class()
		//  and let the GLOOModule::mine() function to do the detection of the appropriate module.
		$this->module = GLOOModule::mine($this);
		$this->config = $this->module->get_config_for($this);				
				
		// Initialize the GLDOMElement, using this class name for the DOM class name
		parent::__construct(get_class($this));
		
		// If we have client script, make the PHP-JS binding by allocating
		// a link ID and registering this class to the GLOOLink structure.
		if (isset($this->config['client'])) {
			$client_class = $this->config['client']['class'];
			$this->linkID = GLOOLink::register($this, $client_class);			
		} else {
			$this->linkID = false;
		}
		
	}

	private function encode_structure($struct) {
		if (is_array($struct)) {
			foreach ($struct as $k => $v) {
				$struct[$k] = $this->flatten_structure($v);
			}
			return $struct;
		} elseif (is_object($struct)) {
			if ($struct instanceof GLOOObject) {
				return GLOOLink::get_object_reference($struct);
			} else {
				return encode_structure(get_object_vars($struct));
			}
		} else {
			return $struct;
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
		if ($this->synced) {
			$this->_call('_set', $name, $this->encode_structure($value));
		}
	}

	/** 
	  * An inheritable function to notify a variable removal
	  * [INHERITED FROM GLDataset]
	  *
	  * @param	string	$name	The variable name that was removed
	  */
	protected function var_removed($name) {
		if ($this->synced) {
			$this->_call('_unset', $name);
		}
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
	public function _vars() {
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
		if ($this->linkID === false) return;
		$vars = func_get_args();
		array_shift($vars);
		GLOOLink::store_message($this->linkID, $call, $vars);
	}
}

?>