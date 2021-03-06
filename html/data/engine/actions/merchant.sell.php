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
$text = '{#SHOP_SELL_WELCOME#}';
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
		$text = '<font color="#006600">{#SHOP_SELL_THANKS_PL_1#} '.$vars['name'].'. {#SHOP_SELL_THANKS_PL_2#} '.$gold.' {#COINS#}.</font>';
	} else {
		$vars = gl_get_guid_vars($gitem);	
		gl_update_guid_vars($gitem, array('parent' => $_REQUEST['guid']));
		gl_update_guid_vars($_SESSION[PLAYER][GUID], array('money' => ($_SESSION[PLAYER][DATA]['money']+$vars['sell_price'])));
		$text = '<font color="#006600">{#SHOP_SELL_THANKS_1#} '.$vars['name'].'. {#SHOP_SELL_THANKS_2#} '.$vars['sell_price'].' {#COINS#}.</font>';
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

// Change to merchand background music
relayMessage(MSG_INTERFACE, 'MUSIC', 'merchant');

$act_result=array(

	# Theese variables will reach JSON untouched
	'mode'=>'MAIN',
	'width'=>800,
	'height'=>500, 
	'title' => 'Sell goods',
	'head_image' => 'UI/navbtn_back.gif',
	'head_link' => '?a=map.grid.get',
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