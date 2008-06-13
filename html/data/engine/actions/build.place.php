<?php

if (!isset($_REQUEST['model'])) {
	$act_result=array('mode'=>'NONE');
	return;
}

$map = $_SESSION[PLAYER][DATA]['map'];
$x = $_REQUEST['x'];
$y = $_REQUEST['y'];

$oguid = gl_make_guid(3,false,'gameobject');
$guid = gl_instance_object($oguid, array('x'=>$x, 'y'=>$y, 'map'=>$map, 'model'=>$_REQUEST['model']));
if (!$guid) {
	relayMessage(MSG_INTERFACE,'MSGBOX','Unable to instance guid #'.$oguid);
	$act_result=array('mode'=>'NONE');
	return;	
} else {	
	gl_do('map.grid.get', array('x'=>$_SESSION[PLAYER][DATA]['x'],'y'=>$_SESSION[PLAYER][DATA]['y'],'map'=>$map));
}

?>