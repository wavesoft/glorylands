<?php

if ($_REQUEST['action']=='create') {
	
	$tpl = $_REQUEST['template'];
	$name = $_REQUEST['name'];
	if ($tpl && $name) {
		$tpl = gl_make_guid($tpl, false, 'char');
		$guid = gl_instance_object($tpl, array('name'=>$name, 'account'=>$_SESSION[PLAYER][PROFILE]['index']));
		if (!$guid) {
			$act_result['error']='Cannot instance character';
		} else {
			$_SESSION[PLAYER][GUID]=$guid;
			gl_redirect('interface.entry');
		}
	} else {
		if (!$tpl)	$act_result['error']='Invalid template selected!';
		if (!$name)	$act_result['error']='Invalid name entered';
	}
}

// Default case
$sql->query("SELECT `template`, `race` FROM `char_template`");
$act_result['templates'] = $sql->fetch_array_all(MYSQL_ASSOC);

if (isset($_SESSION[PLAYER])) {
	$act_result['player']=$_SESSION[PLAYER];
	$act_result['chars']=$_SESSION[PLAYER][CHARS];
}

// Find out server statistics
$users = $sql->query_and_get_value("SELECT count(*) FROM `users_accounts` WHERE `online` = 1");
$max_users = 100;
$perc = ceil(100*$users/$max_users);
$act_result['server_load_img'] = ceil(7*$perc/100);
$act_result['server_load_perc'] = $perc;

?>