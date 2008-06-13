<?php
include "config/config.php";
include "engine/includes/base.php";

// Make sure session is valid
if (!isset($_SESSION[PLAYER][GUID])) die();

if (isset($_REQUEST['a'])) {
	sendMessage(MSG_INTERFACE,'CHAT',"Welcome to GloryLands!", "System");
} else {
	// User is active. Poll database to keep user online
	gl_user_action();

	// Send messages
	echo json_encode(array('messages'=>jsonPopMessages(MSG_INTERFACE)));

}
?>