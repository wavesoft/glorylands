<?php

class GLDOM {

	public static $root;

}

class GLDOMElement extends GLDataset {
	
	public $children;
	public $parent;
	public $next;
	public $previous;
	public $root;
	
	public $classname;
	public $id;
	
	public function __construct($class=NULL, $id=NULL) {
		parent::__construct();
		
		$this->children = array();
		if ($class != NULL) $this->classname = $class;
		if ($id != NULL) $this->id = $id;
		$this->root = $this;
	}
	
	public function add_child(&$object) {
		// Add the child and find it's ID
		$this->children[] = &$object;
		$id = sizeof($this->children)-1;
		
		// Assign parent
		$object->parent = $this;
		$object->root = &$this->root;
		$object->next = false;
		
		// Calculate next/previous
		if ($id == 0) {
			$object->previous = false;
		} else {
			$object->previous = &$this->children[$id-1];
			$object->previous->next = $object;
		}
		
		// Return the object
		return $object;
	}
	
	public function remove_child($objid) {
		// Calculate the object id
		if (!is_numeric($objid)) {
			$objid = array_search($objid, $this->vars);
		}
		
		// Make sure we have a valid id
		if (($objid < 0)  || ($objid === false)) return false;
		
		// Unset parent
		$this->vars[$objid]->parent = false;
		
		// Calculate previous/next
		if ($this->vars[$objid]->previous !== false) {
			$this->vars[$objid]->previous->next = &$this->vars[$objid]->next;
		} 
		
		// Unbind previous/next
		$this->vars[$objid]->previous = false;
		$this->vars[$objid]->next = false;
		$this->vars[$objid]->root = false;
	}

	/**
	  * Return all the matching elements
	  * 
	  * Find a child using the following search pattern simmilar
	  * to CSS selector on HTML DOM:
	  *
	  *  [<classs>].[<id>] : Both Class and ID can be ommited. If ommited, 
	  *						 everything will be selected
	  *
	  * @param	string	$search	The search pattern 
	  * @param	int		$count	[Optional] The maximum entries to return
	  * @return	array			Return the child objects that match the criteria
	  *
	  */
	public function find_children($search,$count=0) {
		
		// Extract class/id from search string
		if (strstr($search,'.')) {
			$parts = explode('.', $search);
			if ($parts[0] == '') {
				$class = false;
				$name = $parts[0];
			} else {
				$class = $parts[0];
				$name = $parts[1];
			}
		} else {
			$class = $search;
			$name = false;
		}
		
		// Search strings
		$res = array();
		foreach ($c as $this->children) {
			$match = true;
			if ($class !== false) $match = $match && ($c->classname == $class);  
			if ($name !== false) $match = $match && ($c->id == $name); 
			if ($match) $res[] = $c; 
			
			// Check for maximum elements
			if (($count != 0) && (sizeof($res) >= $count)) break;
		}
		
		// Return the arrays
		return $res;
		
	}

	/**
	  * Return the first matching element
	  * 
	  * Find a child using the following search pattern simmilar
	  * to CSS selector on HTML DOM:
	  *
	  *  [<classs>].[<id>][@<index>] 
	  *
	  *  Both Class and ID can be ommited. If ommited, everything will be selected.
	  *  Also, in contrast with find_children, you can specify a specific child
	  *  instead of the first. <index> is 1-based.
	  *
	  * @param	string	$search	The search pattern 
	  * @return	array|bool			Return the child objects that match the criteria
	  */
	public function find_child($search) {
	
		// Locate the index
		$max_items = 1;
		$index = false;
		if (strstr($search, '@')) {
			$pars = explode('@', $search);
			$search = $parts[0];
			$index = (int)$parts[1];
			if ($index != 0) {
				$max_items = $index;				
			}
		}
		
		// Perform the search
		$items = $this->find_children($search,1);
		if (sizeof($items) < 1) {
			return false;
		} else {
			if ($index) {
				if (isset($items[$index])) {
					return $items[$index];
				} else {
					return false;
				}
			} else {
				return $items[0];
			}
		}
		
	}

}

?>