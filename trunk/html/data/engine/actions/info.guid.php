<?php


$info = gl_get_guid_vars($_REQUEST['guid']);

$act_result = array(
	'mode' => 'POPUP',
	'title' => 'Details of '.$info['name'],
	'width' => 430,
);

?>