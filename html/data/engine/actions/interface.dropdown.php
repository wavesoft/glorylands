<?php

// Get object's variables
$vars = gl_get_guid_vars($_REQUEST['guid']);
if (isset($vars['x'], $vars['y'])) {
	
	$distance = gl_distance($_SESSION[PLAYER][DATA]['x'],$_SESSION[PLAYER][DATA]['y'],$vars['x'],$vars['y']);
	if ($distance > 2) {
		// We are too far away
		$act_result = array(
			'mode' => 'DROPDOWN',
			'menus'=> array(array('images/UI/piemenu/help.gif', 'View information', '?a=info.guid&guid='.$_REQUEST['guid'])),
			'text' => '<small><em>(Too far away!)</em></small>'
		);
		return;
	}
}

// Initialize basic reply stack
$data = array(
	array('url'=>'?a=info.guid&guid='.$_REQUEST['guid'], 'icon' => 'images/UI/piemenu/help.gif', 'text'=>'View information'),
	array('url'=>'?a=interface.container&guid='.$_REQUEST['guid'], 'icon' => 'images/UI/piemenu/find.gif', 'text'=>'Search Object')
);

// Add more specific options
if (strstr($vars['flags'], 'VENDOR')) $data[]=array('url'=>'?a=merchant.sell&guid='.$_REQUEST['guid'], 'icon' => 'images/UI/piemenu/trade.gif', 'text'=>'Trade');
if (strstr($vars['flags'], 'CHAT')) $data[]=array('url'=>'?a=npc.chat&guid='.$_REQUEST['guid'], 'icon' => 'images/UI/piemenu/chat.gif', 'text'=>'Chat');
if (strstr($vars['flags'], 'QUEST')) $data[]=array('url'=>'?a=merchant.sell&guid='.$_REQUEST['guid'], 'icon' => 'images/UI/piemenu/quest.gif', 'text'=>'Quest');

$text = '';

// Dropdown system is mostly based on the interrupt system
if (callEvent('interface.dropdown', $data, $_REQUEST['guid'], $text)) {

	// Compress menu items reply by removing text keys and replacing them with indexies
	$menus = array();
	foreach ($data as $entry) {
		$menu = array('','','');
		if (isset($entry['icon'])) $menu[0]=$entry['icon'];
		if (isset($entry['text'])) $menu[1]=$entry['text'];
		if (isset($entry['url'])) $menu[2]=$entry['url'];
		array_push($menus, $menu);
	}

	// Reply data
	$act_result = array(
		'mode' => 'DROPDOWN',
		'menus' => $menus
	);
	
} else {
	
	// Do nothing if some interrupt returned false
	$act_result = array(
		'mode' => 'DROPDOWN',
		'menus' => array(),
		'text' => '<small><em>(Nothing here)</em></small>'
	);
}

?>
