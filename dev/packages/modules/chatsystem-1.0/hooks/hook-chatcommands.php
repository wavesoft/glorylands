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
		
	} elseif ($cmd == 'place') {

		if (!isset($parameters[0])) { $answer='';
			relayMessage(MSG_INTERFACE, 'CHAT', '<font color="#FF0000">Please, specify a guid!</font>', '<font color="gold">admin</font>');
			return false;
		}
		
		if (!isset($parameters[1])) { $answer='';
			relayMessage(MSG_INTERFACE, 'CHAT', '<font color="#FF0000">Please, specify a model!</font>', '<font color="gold">admin</font>');
			return false;
		}
		
		$guid=$parameters[0];
		$model=$parameters[1];
		
		$ans=$sql->query("INSERT INTO `data_grid` (`guid`,`x`,`y`,`map`,`model`) VALUES ({$guid},{$x},{$y},{$map},'{$model}')");
		if (!$ans) {
			$answer=$sql->getError();
		} else {
			$answer="Object @($guid) as '$model' placed on ($x,$y,$map)";
			gl_do('map.grid.get');
		}

	} elseif ($cmd == 'logout') {
		
		$answer='';
		relayMessage(MSG_INTERFACE, 'NAVIGATE', 'interface.entry');
		
	}
	return true;	

}

?>