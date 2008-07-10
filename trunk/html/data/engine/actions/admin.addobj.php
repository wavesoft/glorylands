<?php

if ($_SESSION[PLAYER][PROFILE]['level']!='ADMIN') { relayMessage(MSG_INTERFACE,'MSGBOX','Sorry, only administrators can use this function!'); return; };

if ($_REQUEST['action'] == 'rect') {

	// Store the request into session (instead of sending it to client and get it back again)
	if (!isset($_SESSION['admin'])) $_SESSION['admin'] = array();
	$_SESSION['admin']['build_request'] = $_REQUEST;

	// Load the model we want to place
	$model = new mapobj('data/models/'.$_REQUEST['model']);
	
	// Get dimensions and display the rectangle
	relayMessage(MSG_INTERFACE,'RECT', true, '?a=admin.addobj&action=place', 
				 $model->width, $model->height, $model->bindX, $model->bindY-1, true, true); // ClickDisposable & Silent
		
	// No interface action
	$act_result=array('mode'=>'NONE');
	
	// Alter the manifest
	$act_profile['post_processor'] = '';
	$outmode = 'json';

} elseif ($_REQUEST['action'] == 'place') {

	// Get the last request
	$request = $_SESSION['admin']['build_request'];
	if (isset($request)) {
	
		// Remove request
		unset($_SESSION['admin']['build_request']);
		
		// Get variables	
		$map = $_SESSION[PLAYER][DATA]['map'];
		$x = $_REQUEST['x'];
		$y = $_REQUEST['y'];
		
		// Build the GUID
		$oguid = gl_make_guid($request['template'],false,'gameobject');
			
		// Obdain variables
		$vars = array();
		if (isset($request['vars'])) {
			$v = explode(';',$request['vars']);
			foreach ($v as $var) {
				$var = explode("=",$var);
				$vars[$var[0]] = $var[1];
			}
		}
		$vars = array_merge($vars, array('x'=>$x, 'y'=>$y, 'z'=>$request['z'], 'map'=>$map, 'model'=>$request['model'], 'name'=>$request['name']));

		// Notify Grid alteration
		callEvent('grid.alter', $_SESSION[PLAYER][GUID], $x,$y,$map); // New object here
		
		// Instance the object
		$guid = gl_instance_object($oguid, $vars);
		if (!$guid) {
			relayMessage(MSG_INTERFACE,'MSGBOX','Unable to instance guid #'.$oguid);

			// No interface action
			$act_result=array('mode'=>'NONE');
			
			// Alter the manifest
			$act_profile['post_processor'] = '';
			$outmode = 'json';

		} else {	
			gl_do('map.grid.get', array('x'=>$_SESSION[PLAYER][DATA]['x'],'y'=>$_SESSION[PLAYER][DATA]['y'],'map'=>$map));
		}
	
	}

} else {
	
	// Get the available models
	$objects = array();
	$d = dir(DIROF('DATA.MODEL',true));
	while (false !== ($entry = $d->read())) {
		if (substr($entry,-2) == '.o') {
			$objects[] = $entry;
		}
	}
	$d->close();
	
	// Get the available templates
	$sql->query("SELECT `template` as `index`, `templatename` as `name` FROM `gameobject_template`");
	$templates = $sql->fetch_array_all(MYSQL_ASSOC);

	// Return result
	$act_result = array(
			'mode' => 'POPUP',
			'title' => 'Change your model',
			'width' => 370,
			'_my' => array(
				'objects' => $objects,
				'templates' => $templates
			)
	);
}

?>