<?php

class GLDataset {

	protected $vars;

	public function __construct() {
		$this->vars = array();
	}

	/** 
	  * Push a value on a variable
	  *
	  * If the variable is not an array, it is conerted to.
	  * If the variable is not definet, it will be created as an array.
	  *
	  * @param	string	$name	The variable name
	  * @param	mixed	$value	The variable value
	  */
	public function push($var, $value) {
		if (isset($this->vars[$var])) {
			// Exists? Is it array then?
			if (is_array($this->vars[$var])) {
				// Yes it is, stack it
				$this->vars[$var][] = $value;
			} else {
				// No, convert it into an array
				$this->vars[$var] = array(
					$this->vars[$var],
					$value
				);
			}
		} else {
			// Not exists, create new array
			$this->vars[$var] = array(
				$value
			);
		}

		// Notify a variable change
		$this->var_changed($var, $this->vars[$var]);
		if (is_object($value)) {
			$this->object_added($var, $value);
		}
	}

	/** 
	  * Push a value on a variable, but make sure it's only one on the array
	  *
	  * This function checks for an existing $value on the array. If it already
	  * exists, it is not appended twice.
	  *
	  * If you specify the $uid parameter, the unique matching is based on the value
	  * of this variable.
	  *
	  * @param	string	$name	The variable name
	  * @param	mixed	$value	The variable value
	  * @param	mixed	$uid	[Optional] The unique ID to perform single matching
	  */
	public function push_once($var, $value, $uid=false) {	
		if (isset($this->vars[$var])) {
			// Exists? Make sure it doesn't exist
			if ($uid === false) {
				if (in_array($value, $this->vars[$var])) return;
			} else {
				if (isset($this->vars[$var][$uid])) return;
			}
			
			// It don't exists... append it
			if (is_array($this->vars[$var])) {
			
				// Yes it is, stack it
				if ($uid === false) {
					$this->vars[$var][] = $value;
				} else {
					$this->vars[$var][$uid] = $value;
				}
				
			} else {
				// No, convert it into an array
				if ($uid === false) {
					$this->vars[$var] = array(
						$this->vars[$var],
						$value
					);
				} else {
					$this->vars[$var] = array($this->vars[$var]);
					$this->vars[$var][$uid] = $value;
				}
			}
		} else {
			// Not exists, create new array
			if ($uid === false) {
				$this->vars[$var] = array($value);
			} else {
				$this->vars[$var] = array();
				$this->vars[$var][$uid] = $value;
			}
		}
		
		// Notify a variable change
		$this->var_changed($var, $this->vars[$var]);
		if (is_object($value)) {
			$this->object_added($var, $value);
		}
	}

	/** 
	  * Unshift a value on a variable
	  *
	  * If the variable is not an array, it is conerted to.
	  * If the variable is not definet, it will be created as an array.
	  *
	  * @param	string	$name	The variable name
	  * @param	mixed	$value	The variable value
	  */
	public function unshift($var, $value) {
		if (isset($this->vars[$var])) {
			// Exists? Is it array then?
			if (is_array($this->vars[$var])) {
				// Yes it is, stack it
				array_unshift($this->vars[$var], $value);
			} else {
				// No, convert it into an array
				$this->vars[$var] = array(
					$this->vars[$var],
					$value
				);
			}
		} else {
			// Not exists, create new array
			$this->vars[$var] = array(
				$value
			);
		}
		
		// Notify a variable change
		$this->var_changed($var, $this->vars[$var]);
		if (is_object($value)) {
			$this->object_added($var, $value);
		}
		
	}
	
	/** 
	  * Pop a value from a variable
	  *
	  * If the variable is not an array, the variable is returned and then unsetted
	  * If the variable is not defined, NULL is returned
	  *
	  * @param	string	$var	The variable name
	  */
	public function pop($var) {	
		if (isset($this->vars[$var])) {
			// Exists? Is it array then?
			if (is_array($this->vars[$var])) {		
				// Yes it is, pop the array
				$v = array_pop($this->vars[$var]);

				// If the variable is an object, notify object removal
				if (is_object($v)) {
					$this->object_removed($var, $v);
				}
								
				// The array is now blank? Remove the variable
				if (sizeof($this->vars[$var]) == 0) {
					unset($this->vars[$var]);
				}
				
				// Notify a variable change
				$this->var_changed($var, $this->vars[$var]);
								
				return $v;
			} else {
				// No, return the variable and unset the array
				$v = $this->vars[$var];

				// Notify a variable removal
				$this->var_removed($var, $this->vars[$var]);
											
				unset($this->vars[$var]);
				return $v;
			}
		} else {
			// Not defined? Return null...
			return NULL;
		}
	}
	
	/** 
	  * Shift a value from a variable
	  *
	  * If the variable is not an array, the variable is returned and then unsetted
	  * If the variable is not defined, NULL is returned
	  *
	  * @param	string	$var	The variable name
	  */
	public function shift($var) {
		if (isset($this->vars[$var])) {
			// Exists? Is it array then?
			if (is_array($this->vars[$var])) {
				// Yes it is, pop the array
				$v = array_shift($this->vars[$var]);
				
				// If the variable is an object, notify object removal
				if (is_object($v)) {
					$this->object_removed($var, $v);
				}
				
				// The array is now blank? Remove the variable
				if (sizeof($this->vars[$var]) == 0) {
					unset($this->vars[$var]);
				}
				
				// Notify a variable change
				$this->var_changed($var, $this->vars[$var]);
				
				return $v;
			} else {
				// No, return the variable and unset the array
				$v = $this->vars[$var];				
				
				// Notify a variable removal
				$this->var_removed($var, $this->vars[$var]);
				
				unset($this->vars[$var]);
				return $v;
			}
		} else {
			// Not defined? Return null...
			return NULL;
		}
	}
	
	/** 
	  * Sort the contents of a variable
	  *
	  * If the variable is not an array or not defined, nothing happens	  
	  *
	  * @param	string	$var	The variable name
	  * @param	int		$sort_flags	The sort flags, as defined in sort()
	  */
	public function sort($var, $sort_flags=0) {
		if (isset($this->vars[$var])) {
			if (is_array($this->vars[$var])) {
				sort($this->vars[$var], $sort_flags);
			}
		}
	}
	
	/** 
	  * Sort the contents of a variable using a user callback
	  *
	  * If the variable is not an array or not defined, nothing happens	  
	  *
	  * @param	string	$var			The variable name
	  * @param	int		$cmp_function	The sorting function
	  */
	public function usort($var, $cmp_function) {
		if (isset($this->vars[$var])) {
			if (is_array($this->vars[$var])) {
				usort($this->vars[$var], $cmp_function);
			}
		}
	}

	/** 
	  * Assign a value into a variable
	  *
	  * @param	string	$name	The variable name to define
	  * @param	mixed	$value	The variable value
	  */
	public function assign($name, $value) {
		$this->var_changed($name, $value);
		$this->vars[$name] = $value;

		if (is_object($value)) {
			$this->object_added($name, $value);
		}
	}

	/** 
	  * Import variables from an array or object
	  *
	  * @param	mixed	$src	The object to scan for variables
	  */
	public function import($src) {
		foreach ($src as $var => $value) {
			$this->assign($var, $value);
		}
	}

	/** 
	  * Unset a variable
	  *
	  * @param	string	$name	The variable name to unset
	  */
	public function __unset($name) {
		$this->var_removed($name, $this->vars[$name]);
		unset($this->vars[$name]);
	}
	
	/** 
	  * Get data variables
	  *
	  * @param	string	$name	The variable name to return
	  */
	public function __get($name) {
		if (isset($this->vars[$name])) {
			return $this->vars[$name];
		} else {
			return NULL;
		}
	}
	
	/** 
	  * Set data variables
	  *
	  * @param	string	$name	The variable name to define
	  * @param	mixed	$value	The variable value
	  */
	public function __set($name, $value) {
		$this->assign($name, $value);
	}
	
	/** 
	  * Check for defined variables
	  *
	  * @param	string	$name	The variable name to check
	  */
	public function __isset($name) {
		return isset($this->vars[$name]);
	}

	/** 
	  * An inheritable function to notify a variable update
	  *
	  * @param	string	$name	The variable name that was updated
	  * @param	mixed	$value	The variable value
	  */
	protected function var_changed($name, &$value) {
		// (Inheritable)
	}

	/** 
	  * An inheritable function to notify a variable removal
	  *
	  * @param	string	$name	The variable name that was removed
	  */
	protected function var_removed($name) {
		// (Inheritable)
	}

	/** 
	  * An inheritable function to notify an object attachment
	  *
	  * @param	string	$name	The variable name that was updated
	  * @param	object	$object	The object that was added
	  */
	protected function object_added($name, &$object) {
		// (Inheritable)
	}

	/** 
	  * An inheritable function to notify an object detachment
	  *
	  * @param	string	$name	The variable name that was updated
	  * @param	object	$object	The object that was removed
	  */
	protected function object_removed($name, &$object) {
		// (Inheritable)
	}
	
}

?>