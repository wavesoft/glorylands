<?php

$text=$_REQUEST['text'];

callEvent('chat.send', $text);
if (substr($text,0,1)=='/') {
	$text = substr($text,1);
	$parm = explode(" ",$text);
	$cmd = strtolower($parm[0]);
	
	$ans="Unrecognized command: <b>{$cmd}</b>";
	if ($cmd == 'join') {
		if (callEvent('chat.join', $parm[1], $ans)) {
			$sql->query("DELETE FROM `mod_chat_channel_registrations` WHERE `user` = ".$_SESSION[PLAYER][GUID]." AND `channel` = '{$parm[1]}'");
			$sql->query("INSERT INTO `mod_chat_channel_registrations` (`user`,`channel`) VALUES (".$_SESSION[PLAYER][GUID].", '{$parm[1]}')");
			$ans="You have joined room <b>{$parm[1]}</b>";
		}
	} elseif ($cmd == 'part') {
		if (callEvent('chat.part', $parm[1], $ans)) {
			$sql->query("DELETE FROM `mod_chat_channel_registrations` WHERE `user` = ".$_SESSION[PLAYER][GUID]." AND `channel` = '{$parm[1]}'");
			$ans="You have left room <b>{$parm[1]}</b>";
		}
	} elseif ($cmd == 'chan') {
		$sql->query("SELECT `channel` FROM `mod_chat_channel_registrations` WHERE `user` = ".$_SESSION[PLAYER][GUID]." AND `channel` NOT LIKE '#%'");
		$c='';
		while ($chan=$sql->fetch_array(MYSQL_NUM)) {
			if ($c!='') $c.="\n";
			$c.="<li>{$chan[0]}</li>";
		}
		$ans.="</ul>";
		if  ($c=='') {
			$ans='You are not on any channel. Type /join &lt;channel&gt; to join one!';
		} else {
			$ans='You are currently on channels:<br /><ul>'.$c.'</ul>';
		}
		
	} else {
		array_shift($parm);
		callEvent('chat.command', $cmd, $parm, $ans);
	}
	if ($ans) relayMessage(MSG_INTERFACE, 'CHAT', "<font color=\"gold\">$ans</font>", 'system');
 	
} else { 

	$ans=$sql->query("SELECT `channel` FROM `mod_chat_channel_registrations` WHERE `user` = ".$_SESSION[PLAYER][GUID]);
	if (!$ans || $sql->emptyResults) return;
	
	// Prepare the query for the rest of the users
	$q = '';
	while ($row = $sql->fetch_array_fromresults($ans,MYSQL_NUM)) {		
		// Forward chat message to all the enrolled users on the channel
		if (callEvent('chat.sendchannel', $text, $row[0])) {
			if ($q!='') $q.=',';
			$q.="'{$row[0]}'";
		}
	}
	if ($q=='') return;

	// Obdain all the users to inform
	$ans=$sql->query("SELECT
					`mod_chat_channel_registrations`.`user`
					FROM
					`mod_chat_channel_registrations`
					Inner Join `char_instance` ON `mod_chat_channel_registrations`.`user` = `char_instance`.`guid`
					WHERE
					`channel` IN ($q) AND
					`char_instance`.`online` =  1
					GROUP BY
					`mod_chat_channel_registrations`.`user`");

	if (!$ans || $sql->emptyResults) return;

	// Send chats 
	while ($row = $sql->fetch_array_fromresults($ans,MYSQL_NUM)) {
		postMessage(MSG_INTERFACE, $row[0] ,'CHAT',$text, $_SESSION[PLAYER][DATA]['name']);
	}		

}
?>