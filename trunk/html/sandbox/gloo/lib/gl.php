<?php

class GL {	

	static public $stream;
	
	static public function initialize() {					
		
		// Initialize stream
		self::$stream = new GLTemplate('resources/page.php');
		
		// Initialize stream variables
		self::$stream->headers = array(
			'js' => array(
				'../../includes/mootools-1.2.2-core-yc.js',
				'../../includes/mootools-1.2.2.2-more.js',
				'resources/gloo.js'
			),
			'css' => array(),
			'meta' => array(
				array(
					'http-equiv' => '',
					'content' => 'text/html; charset=utf-8'
				)
			),
		);
		self::$stream->inline = array(
			'js' => array(),
			'css' => array()
		);		
		self::$stream->title = 'GLOO Test';				
		
		// Install active extensions
		GLOOModule::install('test');
		
		// Start session
		session_start();
		
		// Initialize PHP - JS link
		GLOOLink::initialize();
	}
	
	static public function render() {
	
		// Render the PHP-JS linking script into an in-line javscript
		self::$stream->inline['js'][] = GLOOLink::compile_js();
	
		// Render the final output
		echo self::$stream->fetch();
	
	}

	static public function finalize() {

		// Save PHP - JS link status
		GLOOLink::save();
	
	}

}

?>