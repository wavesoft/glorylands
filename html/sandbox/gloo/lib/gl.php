<?php

/**
  * GLREQ_ROOT Flag
  * This flag means that the request is root-type.
  * Root-type request means that the PHP-JS objects will be rebuilt
  * and the content will be resynced.
  *
  * The reply is an HTML document with the desired layout that 
  * contans the syncronization script.
  */ 
define(GLREQ_ROOT, 1);

/**
  * GLREQ_EXT Flag
  * This flag means that the request is external.
  * An external request is performed when the browser wants to run a module action
  * that does not require a DOM structure.
  *
  * The reply is an HTML document with the desired layout that 
  * contans the response data of the executed script.
  */ 
define(GLREQ_EXT, 2);

/**
  * GLREQ_API Flag
  * This flag means that the request is made from GLOO JS API.
  * Theese requests, that are performed by the GL.call() function, do not
  * destroy the PHP-side of the DOM. This means that all the classes that
  * were instanced (and utilized the JS that made this request) are still availab.e
  *
  * The reply format depends on the X-GLOO-API value, and it varies between
  * a JSON-Encoded variable dump, an XML document, a url-encoded request or
  * an other form, defined and handled by interrupts.
  *
  * However, each data reply contains:
  * - The elementary stream that contains the response to the call 
  * - The secondary stram that holds all the queued requests/updates to the interface.
  */ 
define(GLREQ_API, 3);

class GL {	

	/**
	  * The response stream
	  * @var GLTemplate
	  */
	static public $stream;
	
	/**
	  * The request format
	  * @var int
	  */
	static public $mode;

	/**
	  * A Server-independant function to get the request headers
	  *
	  * @return array	The headers in key-name - value format
	  */
	static public function get_headers() {
		$headers = array();
		foreach ($_SERVER as $k => $v) {
		if (substr($k, 0, 5) == "HTTP_") {
			$k = str_replace('_', ' ', substr($k, 5));
			$k = str_replace(' ', '-', ucwords(strtolower($k)));
			$headers[$k] = $v;
			}
		}
		return $headers;	
	}
	
	static private function get_request_type() {
		
		// Check for existing Async header
		$headers = self::get_headers();
		if (isset($headers['X-GLOO-API'])) {
			return GLREQ_API;
		}
		
		// Check for request mode variable
		$m = $_REQUEST['m'];
		if (isset($m)) {		
			if ($m == 'api') {
				return GLREQ_API;				
			} elseif ($m == 'ext') {
				return GLREQ_EXT;
			}
		}
		
		// Nothing found? Root
		return GLREQ_ROOT;
	}
	
	static public function initialize() {					
		
		// Analyze request and detect response format
		self::$mode = self::get_request_type();
		
		// Initialize stream
		self::$stream = new GLTemplate('resources/page.php');
		
		// Initialize stream variables
		self::$stream->headers = array(
			'js' => array(
				'../../includes/mootools-1.2.2-core-yc.js',
				'../../includes/mootools-1.2.2.2-more.js',
				'../../includes/glapi/debug/trace.js',
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
		if (self::$mode == GLREQ_API) {
			GLOOLink::initialize(false);
		} else {
			GLOOLink::initialize(true);
		}
	}
	
	static public function render() {
		
		if (self::$mode == GLREQ_API) {
		
			// Create the GLOOLink message data
			$buffer = json_encode(array(
				'msg' => GLOOLink::peek_messages()
			));
			
			// Unescape special strings
			//$buffer = preg_replace('/[\'"]#(.+?)#[\'"]/', '\\1', $buffer);
			
			// Echo buffer
			echo $buffer;
		
		} else {
	
			// Render the PHP-JS linking script into an in-line javscript
			self::$stream->inline['js'][] = GLOOLink::compile_js();
		
			// Render the final output
			echo self::$stream->fetch();

		}	
		
	}

	static public function finalize() {

		// Save PHP - JS link status
		GLOOLink::save();
	
	}

}

?>