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
		$sql->query("SELECT `channel` FROM `mod_chat_channel_registrations` WHERE `user` = ".$_SESSION[PLAYER][GUID]);
		$ans='You are currently on channels:<br /><ul>';
		while ($chan=$sql->fetch_array(MYSQL_NUM)) {
			$ans.="<li>{$chan[0]}</li>\n";
		}
		$ans.="</ul>";
		
	} else {
		array_shift($parm);
		callEvent('chat.command', $cmd, $parm, $ans);
	}
	if ($ans) relayMessage(MSG_INTERFACE, 'CHAT', "<font color=\"gold\">$ans</font>", 'system');
 	
} else {

	$ans=$sql->query("SELECT `channel` FROM `mod_chat_channel_registrations` WHERE `user` = ".$_SESSION[PLAYER][GUID]);
	if ($ans && !$sql->emptyResults) {
		while ($row = $sql->fetch_array_fromresults($ans,MYSQL_ASSOC)) {		
			if (callEvent('chat.sendchannel', $text, $row['channel'])) {
				// Forward chat message to all the enrolled users on the channel
				$chans=$sql->query("SELECT `user` FROM `mod_chat_channel_registrations` WHERE `channel` = '{$row['channel']}'");
				if ($chans && !$sql->emptyResults) {
					while ($user = $sql->fetch_array_fromresults($chans,MYSQL_NUM)) {
						postMessage(MSG_INTERFACE, $user[0] ,'CHAT',$text, $_SESSION[PLAYER][DATA]['name']);
					}
				}
			}
		}
	}

}
?>