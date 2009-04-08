<?php

registerEvent('chat_admin', 'chat.command');
function chat_admin(&$cmd, $parameters, &$answer) {
	global $sql;

	$x = $_SESSION[PLAYER][DATA]['x'];
	$y = $_SESSION[PLAYER][DATA]['y'];
	$map = $_SESSION[PLAYER][DATA]['map'];

	if ($cmd == 'goto') {
	
		if (isset($parameters[0])) $x=$parameters[0];
		if (isset($parameters[1])) $y=$parameters[1];
		if (isset($parameters[2])) $map=$parameters[2];
		
		$answer = "You have benn teleported to ($x, $y) on map #$map";
		
		$_SESSION[PLAYER][DATA]['x']=$x;
		$_SESSION[PLAYER][DATA]['y']=$y;
		$_SESSION[PLAYER][DATA]['map']=$map;
		
		gl_do('map.grid.get');
		
	} elseif ($cmd == 'gps') {
		
		$answer = "You are on ($x, $y) at map #$map";
		
	} elseif ($cmd =='additem') {
		
		if (!isset($parameters[0])) {
			$answer = 'Please specify an item to add';
			return false;
		}														
		
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

	} elseif (($cmd == 'varshow')||($cmd == 'showvar')) {
		
		if (!isset($parameters[0])) { $answer='';
			relayMessage(MSG_INTERFACE, 'CHAT', '<font color="#FF0000">Please, specify a variable to display!</font>', '<font color="gold">debug</font>');
			return false;
		}
		
		$guid = $_SESSION[PLAYER][GUID];

		if (isset($parameters[1])) { 
			$guid = $parameters[1];
		}
		
		$vars = gl_get_guid_vars($guid);
		$var = $parameters[0];
		if (!isset($vars[$var])) {
			$answer = "Variable '$var' of object $guid is missing";
		} else {
			$answer = "Object $guid, var '$var' = ".print_r($vars[$var],true);
		}

	} elseif (($cmd == 'varset')||($cmd == 'setvar')||($cmd == 'set')) {
		
		if (!isset($parameters[0])) { $answer='';
			relayMessage(MSG_INTERFACE, 'CHAT', '<font color="#FF0000">Please, specify a variable to edit!</font>', '<font color="gold">debug</font>');
			return false;
		}
		if (!isset($parameters[1])) { $answer='';
			relayMessage(MSG_INTERFACE, 'CHAT', '<font color="#FF0000">Please, specify a variable value!</font>', '<font color="gold">debug</font>');
			return false;
		}
		
		$guid = $_SESSION[PLAYER][GUID];

		if (isset($parameters[2])) { 
			$guid = $parameters[2];
		}
		
		$var = $parameters[0];
		$value = $parameters[1];
		gl_update_guid_vars($guid, array($var => $value));

		$vars = gl_get_guid_vars($guid);
		$answer = "Object's $guid, var '$var' updated to ".print_r($vars[$var],true);
		
	} elseif (($cmd == 'delvar')||($cmd == 'vardel')) {
		
		if (!isset($parameters[0])) { $answer='';
			relayMessage(MSG_INTERFACE, 'CHAT', '<font color="#FF0000">Please, specify a variable to edit!</font>', '<font color="gold">debug</font>');
			return false;
		}
		
		$guid = $_SESSION[PLAYER][GUID];

		if (isset($parameters[1])) { 
			$guid = $parameters[1];
		}
		
		$var = $parameters[0];
		$value = $parameters[1];
		gl_update_guid_vars($guid, array($var => false));

		$vars = gl_get_guid_vars($guid);
		if (!isset($vars[$var])) {
			$answer = "Object's $guid, var '$var' got missing :)";
		} else {
			$answer = "Object's $guid, var '$var' cannot be erased";
		}
		
	} elseif (($cmd == 'listvar')||($cmd == 'varlist')||($cmd == 'vardump')) {
		
		$guid = $_SESSION[PLAYER][GUID];

		if (isset($parameters[0])) { 
			$guid = $parameters[0];
		}
		
		$answer = "Variables of object $guid:<ul>\n";
		$vars = gl_get_guid_vars($guid);
		foreach ($vars as $name => $var) {
			$answer .= "<li>$name</li>\n";
		}
		$answer .= "</ul>";
		
	} elseif ($cmd == 'tipsreset') {
		
		$sql->query('SELECT `index` FROM `data_tips`');
		$tips = array();
		while ($tip = $sql->fetch_array(MYSQL_NUM)) {
			$tips[$tip[0]] = true;
		}
		gl_update_guid_vars($_SESSION[PLAYER][GUID], array('tips' => $tips));
		
		$answer = "Tips are reset for user #".$_SESSION[PLAYER][GUID];	

	} elseif ($cmd == 'online') {

		$sql->query("SELECT `guid`,`name`,`x`,`y`,`map` FROM `char_instance` WHERE `online` = 1");
		$answer = "";	
		while ($char = $sql->fetch_array(MYSQL_ASSOC)) {
			$answer.='<li><a href="javascript:gloryIO(\'?a=info.guid&guid='.$char['guid'].'\')">'.$char['name'].'</a> at <a href="javascript:gloryIO(\'?a=chat.send&text=/goto+'.($char['x']-1).'+'.$char['y'].'+'.$char['map'].'\');">('.$char['x'].','.$char['y'].'@'.$char['map'].')</a>';
		}
		if ($answer!='') {
			$answer="Online users:<ul>".$answer."</ul>";
		} else {
			$answer.='No online users! (Wtf? How did YOU send THIS?!)';
		}

	} elseif ($cmd == 'gpsof') {

		if (!isset($parameters[0])) { $answer='';
			relayMessage(MSG_INTERFACE, 'CHAT', '<font color="#FF0000">Please, specify a username or a GUID!</font>', '<font color="gold">debug</font>');
			return false;
		}

		if (is_numeric($parameters[0])) {
			$guid = $parameters[0];
		} else {
			$guid=$sql->query_and_get_value("SELECT `guid` FROM `char_instance` WHERE `name` LIKE '".mysql_escape_string($parameters[0])."'");			
		}

		if (!$guid) {
			$answer = "Player or GUID not found!";		
		} else {
			$row = gl_get_guid_vars($guid);
			$answer = "Object position is <a href=\"javascript:gloryIO('?a=chat.send&text=/goto+{$row['x']}+{$row['y']}+{$row['map']}')\">({$row['x']},{$row['y']}@{$row['map']})</a>";
		}

	} elseif ($cmd == 'help') {
		
		$answer="Commands that can be used:<ul>";
		$answer.="<li><b>goto</b><i> x [y] [map]</i> : Move to new location</li>";
		$answer.="<li><b>gps</b> : Show your current position</li>";
		$answer.="<li><b>spawn</b><i> guid</i> : Place an object on map</li>";
		$answer.="<li><b>instance</b><i> template [parent]</i> : Instance an object";
		$answer.="<li><b>varshow</b><i> variable [guid]</i> : Display a GUID's variable";
		$answer.="<li><b>varset</b><i> variable value [guid]</i> : Update a GUID's variable";
		$answer.="<li><b>listvar</b><i> [guid]</i>: List GUID variables</li>";
		$answer.="<li><b>delvar</b><i> variable [guid]</i>: Erase a GUID's variable</li>";
		$answer.="<li><b>gpsof</b><i> charname/guid</i>: Get character or object position</li>";
		$answer.="<li><b>online</b>: Get a list of the online players</li>";
		$answer.="<li><b>logout</b>: Log out charachter</li>";
		$answer.="</ul>";

	}
	return true;	

}

registerEvent('chat_notify_zidchange', 'map.updategrid');
function chat_notify_zidchange($zid, $map) {
	global $sql;

	// Unenroll from the last channel
	$ans=$sql->query("DELETE FROM `mod_chat_channel_registrations` WHERE `user` = ".$_SESSION[PLAYER][GUID]." AND `channel` LIKE '#%'");
	$ans=$sql->query("INSERT INTO `mod_chat_channel_registrations` (`user`, `channel`)  VALUES (".$_SESSION[PLAYER][GUID].", '#".$map."')");
	relayMessage(MSG_INTERFACE,'CHAT','<font color=\"#00ff00\">You have joined chat area #'.$map.'</font>','System');
	
	return true;
}

$chat_initialized = false;
registerEvent('chat_module_initialize', 'system.init_operation');
function chat_module_initialize($lastop, $newop) {
	global $_VER, $chat_initialized;
	if (($newop == 'interface.main') && !$chat_initialized) {
		relayMessage(MSG_INTERFACE,'CHAT','GloryLands Engine v'.$_VER['VERSION'],'System');
		$chat_initialized = true; /* Send this only once */
	}

	return true;
}

?>