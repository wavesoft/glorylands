<?php

class GLOOLink {
	
	static private $ObjectStack;	
	
	static public function save() {	
		// #@# This should be implemented using GLCache::Set()
		$_SESSION['struct'] = self::$ObjectStack;
	}

	static public function initialize($clean_start=false) {	
		// #@# This should be implemented using GLCache::Get()		
		if ($clean_start) {
			self::$ObjectStack = array();
			$_SESSION['struct'] = array();
		} else {
			if (isset($_SESSION['struct'])) {
				self::$ObjectStack = $_SESSION['struct'];
			} else {
				self::$ObjectStack = array();
			}		
		}
	}
	
	static public function get_object($linkid) {
		foreach (self::$ObjectStack as $info) {
			if ($info['id'] == $linkid) return $info['object'];
		}
		return false;
	}
	
	static public function register($class, $js_class) {			
	
		// Make sure $ObjectStack is defined (since we are static)
		if (!self::$ObjectStack) self::$ObjectStack=array();
		
		// Detect name and ID
		$name = get_class($class);
		$id = sizeof(self::$ObjectStack);
		
		// Register the object on stack
		self::$ObjectStack[] = array(
			'object' => &$class,
			'class' => $js_class,
			'id' => $id
		);
		
		// Return the ID
		// (Note that the class that called this function ($class) 
		//  MUST assign this ID on the linkID property)
		return $id;
	}
	
	static private function compile_structure($structure, &$delay_ref, $path) {		
	
		if (is_array($structure)) {
		
			foreach ($structure as $var => $value) {
				
				// Calculate the variable name to use for path nesting
				$subvar = '.'.$var;
				if (is_numeric($var)) $subvar='['.$var.']';
				
				// If this element is an object, do some additional processing
				if (is_object($value)) {
				
					// The object is a GLOOObject reference, return null
					// for now, but register the variable for a delay-reference
					if ($value instanceof GLOOObject) {
						$delay_ref[] = array(
							'path' => $path.$subvar,
							'id' => $value->linkID
						);
						unset($structure[$var]);
					
					// If the object is not a GLOOObject instance, extract it's
					// variables, and act like an array
					} else {
						$structure[$var] = self::compile_structure(get_object_vars($value), $delay_ref, $path.$subvar);
					}
				
				// If this element is an array, perform a subl-evel structure analysis 
				} elseif (is_array($value)) {
					$structure[$var] = self::compile_structure($value, $delay_ref, $path.'.'.$var);
				}
			
			}
			
			// Now that the array is properly formatted, return the string representation
			return json_encode($structure);
		
		} else {
			
			// Elseways, use json_encode to convert the structure into 
			// something that Javascript can handle
			return json_encode($structure);
			
		}

	}
	
	static public function compile_js() {
	
		// Make sure $ObjectStack is defined (since we are static)
		if (!self::$ObjectStack) self::$ObjectStack=array();
		
		// Initialize script	
		$script = "/* GloryLands Web-Based MMORPG v0.6        */\r\n";
		$script.= "/* Object-Oriented API v0.1 Alpha          */\r\n";
		$script.= "/* (C) Copyright 2009 - John Haralampidis  */\r\n";
		$script.= "// Initialize Object Structure:\r\n";		
		
		// Start creating the object instances, recording in the same
		// time the delay-mapping that should be done in order to properly set-up
		// the object references
		$delay_ref = array();
		foreach (self::$ObjectStack as $object) {
			
			// Prepare the variables that should be used for the initialization of the JS unit
			$init_vars = array_merge($object['object']->prepare_vars(), array(
				'root' => $object['object']->root,
				'parent' => $object['object']->parent,
				'previous' => $object['object']->previous,
				'next' => $object['object']->next,
				'children' => $object['object']->children
			));
			
			// Write the code that should initialize the JS interface, and in the same
			// time extract the delay references
			$script.='GLOOCore.register(new '.$object['class'].'('.$object['id'].','.self::compile_structure($init_vars, $delay_ref, $object['id'].'#').'));'."\r\n";
		}
		
		// Compile the delay-mapping in order to implement
		// the object references.
		if (sizeof($delay_ref) > 0) {
			$script.= "// Perform delay-mapping of references:\r\n";
			$groups = array();
			foreach ($delay_ref as $ref) {
				$part = explode('#', $ref['path']);
				$ref['path'] = substr($part[1],1);
				$group = (int)$part[0];
				
				if (!isset($groups[$group])) $groups[$group]='with(GLOOCore.get('.$group.')){';
				$groups[$group].=$ref['path']."=GLOOCore.get(".$ref['id'].");";
			}
			$script.=implode("};\r\n", $groups)."};\r\n";
		}

		// Finalize the script
		$script.= "$(window).addEvent('domready',function(){GLOOCore.run();});\r\n";
		
		// Return the compiled script
		return $script;
	}
	
}

?>