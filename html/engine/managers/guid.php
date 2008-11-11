<?php
/**
  * GUID Manager
  *
  * This class provides the GUID URL manager that enables easy access to all 
  * the GUID-related operations
  *
  * @package GloryLands
  * @subpackage Managers
  * @author John Haralampidis <johnys2@gmail.com>
  * @copyright Copyright (C) 2007-2008, John Haralampidis
  * @version 2.5
  */

/**
  * @package GloryLands
  * @subpackage Managers
  */
class mgr_guid {

	/**
	 * GUID Variables
	 * @var array
	 */
	private $vars;

	/**
	 * Changed GUID Variables (For committing)
	 * @var array
	 */
	private $changed_vars;

	/**
	 * The related GUID value
	 * @var int
	 */
	var $guid;

	/**
	 * The parent GUID number
	 * @var int
	 */
	var $parent;

	/**
	 * The template that has instanced this GUID
	 * @var int
	 */
	var $templace;

	/**
	 * The child GUID numbers
	 * @var array
	 */
	var $children;

	/**
	 * The analyzed GUID info
	 * @var array
	 */
	var $info;

	/**
	 * The GUID is instance
	 * @var bool
	 */
	 var $instance;

	/**
	 * The the template GUID that has created this entry
	 * @var bool
	 */
	 var $template;
	
	/**
	  * GUID Variables Overload
	  *
	  * This function overloads the classe's variable Output. This is used to quickly
	  * refer to the GUID internals without using the set/get functions
	  *
	  * @param string 	$var	The GUID variable to get
	  * @return mixed			The variable value
	  */
 	function __get($var) {
		if (!isset($this->vars[$var])) return false;
		return $this->vars[$var];
	}
		
	/**
	  * GUID Variables Overload
	  *
	  * This function overloads the classe's variable Output. This is used to quickly
	  * refer to the GUID internals without using the set/get functions
	  *
	  * @param 	string 	$var	The GUID variable to set
	  * @param  mixed 	$value	The variable value
	  * @return mixed			The variable value
	  */
 	function __set($var, $value) {
		$this->vars[$var] = $value;
		$this->changed_vars[$var] = $value;
	}

	/**
	  * Class Destructor Overload
	  *
	  * This function is called when the class is about to be destroyed. This is used
	  * to commint any changed GUID variables back to the database
	  */
	function __destruct() {
		$this->commit();
	}

	/**
	  * Commit changed variables
	  *
	  * This function stores the changed variables back to the database. 
	  * If you use the overloaded $class->var reference, you should call this function
	  * to make the variables active
	  *
	  * @return bool				Returns TRUE if the operation was successfull
	  */
	function commit() {	
		$ans = $this->set($this->changed_vars);
		$this->changed_vars = array();
		return $ans;
	}

	/**
	  * Initializes a new GUID manager Class
	  *
	  * @param int 	$guid 	The initialization string as provided in the URL
	  */
	function mgr_guid($guid) {
	
		// Initialize object by loading all the GUID variables
		$this->vars = gl_get_guid_vars($guid);
		
		// Precache all the possible requests
		$this->guid = $guid;
		$this->parent = gl_get_guid_parent($guid);
		$this->children = gl_get_guid_children($guid);
		$this->info = gl_analyze_guid($guid);
		$this->instance = $this->info['instance'];
		
		// Find out some other information
		if ($this->instance) {
			$this->template = gl_get_guid_template($guid);
		} else {
			$this->template = $guid;
		}
	}
	
	/**
	  * Gets a GUID variable
	  *
	  * @param string 		$var 	The variable name
	  * @return bool|string			The variable value
	  */
	function get($var) {
		if (!isset($this->vars[$var])) return false;
		return $this->vars[$var];
	}	

	/**
	  * Updates a GUID variable
	  *
	  * @param string|array	 $var 	The variable name or an array in (variable => value) format
	  * @param mixed|missing $value The variable value. This is missing if array form is used
	  * @return bool				Returns TRUE if the operation was successfull
	  */
	function set($var, $value=false) {
		if (is_array($var)) {
			foreach ($var as $name => $value) {
				$this->vars[$name] = $value;
			}
			return gl_update_guid_vars($this->guid, $var);
		} else {
			$this->vars[$var] = $value;
			return gl_update_guid_vars($this->guid, array($var => $value));
		}
	}	

	/**
	  * Changes the GUID parent
	  *
	  * @param int		$new_parent	The variable name or an array in (variable => value) format
	  * @return bool				Returns TRUE if the operation was successfull
	  */
	function move($new_parent) {
		return gl_update_guid_vars($this->guid, array('parent' => $new_parent));
	}
	
	/**
	  * Instance (or duplicate in case of template plugin) 
	  *
	  * @param array	$vars	The variable name or an array in (variable => value) format
	  * @return bool				Returns TRUE if the operation was successfull
	  */
	function instance($vars) {
		return gl_update_guid_vars($this->guid, array('parent' => $new_parent));
	}
	
}

/**
  * Initialization and instance information
  */
$inf['class'] = 'mgr_guid';
$inf['name'] = 'guid';
$inf['lib'] = array();
return $inf;

?>