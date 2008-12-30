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

// Buy items
$text = 'Welcome to my shop traveler! What do you want to buy?';
if (isset($_REQUEST['buy'])) {
	$gitem = $_REQUEST['buy'];
	if (isset($_REQUEST['count'])) {
		$guids = gl_get_guid_simmilar($gitem, $_REQUEST['guid'], $_REQUEST['count']);
		foreach ($guids as $gitem) {
			$vars = gl_get_guid_vars($gitem);	
			if ($_SESSION[PLAYER][DATA]['money'] >= $vars['buy_price']) {
				gl_update_guid_vars($gitem, array('parent' => $_SESSION[PLAYER][GUID]));
				gl_update_guid_vars($_SESSION[PLAYER][GUID], array('money' => ($_SESSION[PLAYER][DATA]['money']-$vars['buy_price'])));
				$text = '<font color="#006600">Here are your '.$vars['name'].'s. Thank you!</font>';
			} else {
				$text = '<font color="#660000">Oops, it looks like you don\'t have enough money for all of them!</font>';
				break;
			}
		}
	} else {
		$vars = gl_get_guid_vars($gitem);	
		if ($_SESSION[PLAYER][DATA]['money'] >= $vars['buy_price']) {
			gl_update_guid_vars($gitem, array('parent' => $_SESSION[PLAYER][GUID]));
			gl_update_guid_vars($_SESSION[PLAYER][GUID], array('money' => ($_SESSION[PLAYER][DATA]['money']-$vars['buy_price'])));
			$text = '<font color="#006600">Here is  your '.$vars['name'].'. Thank you!</font>';
		} else {
			$text = '<font color="#660000">Oops, it looks like this item is too expensive for you!</font>';
		}
	}
}

$item_guids = gl_get_guid_children($_REQUEST['guid'],'item',STACK_ALWAYS);
$objects = array();

foreach ($item_guids as $guid => $count) {
	$vars = gl_get_guid_vars($guid);
	if ($vars['buy_price']) {
		$objects[]=array(
			'name' => $vars['name'],
			'icon' =>  $vars['icon'],
			'desc' => $vars['description'],
			'cost' => $vars['buy_price'], 
			'guid'=>$guid,
			'count'=>$count
		);
	}
}


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