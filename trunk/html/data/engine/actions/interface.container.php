<?php

$objects = array();

// Search objects with the same root
if (isset($_REQUEST['guid'])) {
	$root_guid = $_REQUEST['guid'];
} else {
	$act_result = array('mode'=>'NONE');
	return;
}
$ans=$sql->query("SELECT `guid` FROM `item_instance` WHERE `parent` = {$root_guid}");

if ($ans) {
	while ($row = $sql->fetch_array_fromresults($ans,MYSQL_ASSOC)) {
		$vars = gl_get_guid_vars($row['guid']);
		relayMessage(MSG_INTERFACE,'POPUP',print_r($vars,true),'Debug');

		array_push($objects, array(
			'name' => $vars['name'], 
			'image' => $vars['icon'], 
			'guid' => $row['guid'], 
			'desc' => $vars['description'], 
			'cost' => 0
		));
	}
}

$object_vars = gl_get_guid_vars($root_guid);

$act_result = array(
	'mode' => 'POPUP',
	'title' => 'Contents of '.$object_vars['name'],
	'width' => 430,
	
	'_my'=>array(
		'objects' => $objects,
	)
);

?>