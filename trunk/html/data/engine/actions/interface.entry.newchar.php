<?php

if ($_REQUEST['action']=='create') {
	
	$tpl = $_REQUEST['template'];
	$name = $_REQUEST['name'];
	if ($tpl && $name) {
		$guid = gl_instance_object(gl_make_guid($tpl, false, 'char'), array('name'=>$name, 'account'=>$_SESSION[PLAYER][PROFILE]['index']));
		if (!$guid) {
			$act_result['error']='Cannot instance charachter';
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

?>