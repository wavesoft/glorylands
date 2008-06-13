<?php

function chat_admin(&$cmd, $parameters, &$answer) {
	global $sql;

	$x = $_SESSION[PLAYER][DATA]['x'];
	$y = $_SESSION[PLAYER][DATA]['y'];
	$map = $_SESSION[PLAYER][DATA]['map'];

	if ($cmd == 'goto') {
	
		if (isset($parameters[0])) $x=$parameters[0];
		if (isset($parameters[1])) $y=$parameters[1];
		if (isset($parameters[2])) $map=$parameters[2];
		
		$answer = "You have benn teleported to ($x, $y) at map #$map";
		
		$_SESSION[PLAYER][DATA]['x']=$x;
		$_SESSION[PLAYER][DATA]['y']=$y;
		$_SESSION[PLAYER][DATA]['map']=$map;
		
		gl_do('map.grid.get');
		
	} elseif ($cmd == 'gps') {
		
		$answer = "You are on ($x, $y) at map #$map";
		
	} elseif ($cmd == 'spawn') {

		if (!isset($parameters[0])) { $answer='';
			relayMessage(MSG_INTERFACE, 'CHAT', '<font color="#FF0000">Please, specify a template to use!</font>', '<font color="gold">admin</font>');
			return false;
		}
		
		/*
		if (!isset($parameters[1])) { $answer='';
			relayMessage(MSG_INTERFACE, 'CHAT', '<font color="#FF0000">Please, specify a model!</font>', '<font color="gold">admin</font>');
			return false;
		}
		*/
		
		$template = $parameters[0];
		$guid = gl_instance_object(gl_make_guid($template,false,'npc'), array("x" => $x, "y" => $y, 'map' => $map));

		/*
		$guid=$parameters[0];
		$model=$parameters[1];
		
		$ans=$sql->query("INSERT INTO `data_grid` (`guid`,`x`,`y`,`map`,`model`) VALUES ({$guid},{$x},{$y},{$map},'{$model}')");
		if (!$ans) {
			$answer=$sql->getError();
		} else {
			$answer="Object @($guid) as '$model' placed on ($x,$y,$map)";
			gl_do('map.grid.get');
		}
		*/
		if ($guid>0) {
			$answer="Object #$template (GUID: $guid) placed on ($x,$y,$map)";
			gl_do('map.grid.get');
		} else {
			$answer="Spawn error!";
		}

	} elseif ($cmd == 'instance') {

		if (!isset($parameters[0])) { $answer='';
			relayMessage(MSG_INTERFACE, 'CHAT', '<font color="#FF0000">Please, specify a template to instance!</font>', '<font color="gold">debug</font>');
			return false;
		}
		
		$additional='';
		$parent = 0;
		if (isset($parameters[1])) {
			$parent=$parameters[1];
			$additional=" and parent the guid".$parent;
		}
				
		$guid = gl_instance_object(gl_make_guid($parameters[0], false, 'item'), array('parent' => $parent));
		if ($guid) {
			$answer="Item instanced with guid{$additional}: $guid";
		} else {
			$answer="Cannot instance item with template id #".$parameters[0].$additional;
		}

	} elseif ($cmd == 'logout') {
		
		$answer='';
		gl_redirect('interface.entry', true);
		
	}
	return true;	

}


function chat_notify_zidchange($zid, $map) {
	global $sql;

	// Unenroll from the last channel
	$ans=$sql->query("DELETE FROM `mod_chat_channel_registrations` WHERE `user` = ".$_SESSION[PLAYER][GUID]." AND `channel` LIKE '#%'");
	$ans=$sql->query("INSERT INTO `mod_chat_channel_registrations` (`user`, `channel`)  VALUES (".$_SESSION[PLAYER][GUID].", '#".$map."')");
	relayMessage(MSG_INTERFACE,'CHAT','<font color=\"#00ff00\">You have joined chat area #'.$map.'</font>','System');
}


?>