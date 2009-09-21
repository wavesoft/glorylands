<?php

class GLXMLCompiler {

	static public function get_cache($file) {
		$root = dirname(dirname(__FILE__)).'/cache';
		$p_dir = dirname($file);
		$p_file = basename($file);
		return $root.'/'.md5($p_dir).'/'.urlencode($p_file);
	}

	static public function compile($file) {
		// Make sure that the file exists
		if (!is_file($file)) return false;
		
		// Calculate file names
		$cahce = self::get_cache($file);
		$c_file = $cache.'.run.php';
		$c_db = $cache.'.db.php';
		
		// Make sure the file is changed before perofming re-compile
		if (filectime($file) <= filectime($c_file)) return $c_file;
		
		// The file is changed, create compile
		
	}
	
}

class GLXMLTemplate {

	public function __construct($file) {
		
	}

}

class GLTemplate extends GLDataset {
	
	private $file;
	
	/**
	  * Construct the GL Template, using the file specified
	  */
	public function __construct($file, $id=NULL) {		
		$this->file = $file;
		$this->vars = array();
	}

	/** 
	  * Convert this into a string
	  *
	  * Returns the 'text' variable, if exists.
	  */
	public function __toString() {
		return $this->fetch();
	}
		
	public function fetch() {
		// Extract the variables
		extract($this->vars, EXTR_OVERWRITE);
		
		// Start OB and render file
		ob_start();
		include $this->file;
		
		// Fetch buffer and quit
		$buf = ob_get_clean();
		ob_end_clean();
		
		// Return buffer
		return $buf;
	}
	
}

?>