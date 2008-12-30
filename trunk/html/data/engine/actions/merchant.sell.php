<?php

// Some validations
$vars = gl_get_guid_vars($_REQUEST['guid']);
if (!strstr($vars['flags'],'VENDOR')) {
	$act_result=array('mode'=>'NONE');
	relayMessage(MSG_INTERFACE,'MSGBOX','This object cannot provide vendor ability!');
	return;
}
$distance = gl_distance($_SESSION[PLAYER][DATA]['x'],$_SESSION[PLAYER][DATA]['y'],$vars['x'],$vars['y']);
if ($distance>3) { /* At least within 3 boxes */
	$act_result=array('mode'=>'NONE');
	relayMessage(MSG_INTERFACE,'MSGBOX','You are too far away! Get closer!');
	return;	
}

// Sell items
$text = 'Welcome to my shop traveler! What do you want to sell to me?';
if (isset($_REQUEST['sell'])) {
	$gitem = $_REQUEST['sell'];
	if ($_REQUEST['count']) {
		$parent = $_REQUEST['guid'];
		$guids = gl_get_guid_simmilar($gitem, $_SESSION[PLAYER][GUID], $_REQUEST['count']);
		$vars = gl_get_guid_vars($gitem);	
		$gold=0;
		foreach ($guids as $gitem) {
			$vars = gl_get_guid_vars($gitem);
			gl_update_guid_vars($gitem, array('parent' => $parent));
			gl_update_guid_vars($_SESSION[PLAYER][GUID], array('money' => ($_SESSION[PLAYER][DATA]['money']+$vars['sell_price'])));
			$gold+=$vars['sell_price'];
		}
		$text = '<font color="#006600">Thank you for your '.$vars['name'].'s. Here are your '.$gold.' coins.</font>';
	} else {
		$vars = gl_get_guid_vars($gitem);	
		gl_update_guid_vars($gitem, array('parent' => $_REQUEST['guid']));
		gl_update_guid_vars($_SESSION[PLAYER][GUID], array('money' => ($_SESSION[PLAYER][DATA]['money']+$vars['sell_price'])));
		$text = '<font color="#006600">Thank you for your '.$vars['name'].'. Here are your '.$vars['sell_price'].' coins.</font>';
	}
}

$objects = array();

function add_objects_of($guid) {
	global $objects;
	$item_guids = gl_get_guid_children($guid, 'item',STACK_ALWAYS);
	foreach ($item_guids as $guid => $count) {
		$vars = gl_get_guid_vars($guid);
		if ($vars['sell_price']) {
			$objects[]=array('name' => $vars['name'], 'icon' =>  $vars['icon'], 'desc' => $vars['description'], 'cost' => $vars['sell_price'], 'guid'=>$guid, 'count'=>$count);
		}
		if (gl_count_guid_children($guid)>0) add_objects_of($guid);
	}
}

add_objects_of($_SESSION[PLAYER][GUID]);

$act_result=array(

	# Theese variables will reach JSON untouched
	'mode'=>'DEDICATED',
	'width'=>800,
	'height'=>500, 
	'rollback'=>true,
	/* 'text' Will be added automatically before post-procession */
	
	# `_my ` is unsetted after the pre-buffer process.
	# Theese are used only from SMARTY processor
	'_my'=>array(
		'name'=>$row['name'],
		'desc'=>$row['desc'],
		'objects'=>$objects,
		'gold' => $_SESSION[PLAYER][DATA]['money'],
		'slots' => 12,
		'welcome' => $text,
		'guid' => $_REQUEST['guid']
	)
);

?>