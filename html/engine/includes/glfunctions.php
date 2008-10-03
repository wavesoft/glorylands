<?php

## ==========================================
##             PLAYER OPERATIONS
## ==========================================

## Online detection system ##
##~~~~~~~~~~~~~~~~~~~~~~~~ ##

// User has performed an action. Update user's timeout
function gl_user_action() { /* Execution time must be as less as possible */
	global $sql;
	if (isset($_SESSION[PLAYER])) {
		$now = time();
		// Keep online user's account
		$ans=$sql->query("UPDATE `users_accounts` SET `online` = 1, `lastaction` = $now WHERE `index` = ".$_SESSION[PLAYER][PROFILE]['index']);
		if (!$ans) relayMessage(MSG_INTERFACE,'POPUP',$sql->getError(),'SQL Error');
		
		// Keep online user player account
		//$ans=$sql->query("UPDATE `char_instance` SET `online` = 1 WHERE `guid` = ".$_SESSION[PLAYER][GUID]);
		//if (!$ans) relayMessage(MSG_INTERFACE,'POPUP',$sql->getError(),'SQL Error');
	}
}

// Check expired users and expire their online sessions if so
function gl_expire_users() { /* Execution time must be as less as possible */
	global $sql;
	$timeout = 60;	/* Expire timeout (in seconds) */
	$timeout = time() - $timeout;
	$ans=$sql->query("UPDATE `users_accounts` SET `online` = 0 WHERE `lastaction` < $timeout");
	if (!$ans) relayMessage(MSG_INTERFACE,'POPUP',$sql->getError(),'SQL Error');
	
	// Logoff chars of logged-off users
	/* [Do NOT. Just the account is enough :P]
	$ans=$sql->query("UPDATE `char_instance`
				 Inner Join `users_accounts` ON `char_instance`.`account` = `users_accounts`.`index`
				 SET `char_instance`.`online` = 0
				 WHERE `users_accounts`.`online` =  0");
	if (!$ans) relayMessage(MSG_INTERFACE,'POPUP',$sql->getError(),'SQL Error');
	*/
}

## User Login/Logout and sessioning system ##
## ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ ##

function gl_user_login($username, $password) {
	global $sql;
	
	// Return TRUE if $text is a valid MD5 hash
	function ismd5($text) {
		$validchars='01234567890abcdef';
		if (strlen($text)!=32) return false;
		for ($i=0; $i<strlen($text); $i++) {
			if (!strstr($validchars, $text[$i])) return false;
		}
		return true;
	}
	
	// Hash the password if it is not already hashed
	if (!ismd5($password)) $password=md5($password);
	
	// Try to find a user with those info
	$ans=$sql->query("SELECT * FROM `users_accounts` WHERE `name` = '$username' AND `password` = '$password'");
	if ($sql->emptyResults) {
		return false;
	} else {
		// Load and save player profile
		$row=$sql->fetch_array(MYSQL_ASSOC);
		$_SESSION[PLAYER][PROFILE] = $row;
	}
	
	// Set every user's chars as logged out
	$sql->query("UPDATE `char_instance` SET `online` = 0 WHERE `account` = ".$row['index']);

	// Update some account information
	$sql->query("UPDATE `users_accounts` SET `online` = 1, `lastlogin` = NOW(), `lastip` = '".$_SERVER['REMOTE_ADDR']."'  WHERE `index` = ".$row['index']);

	// Notify event chains
	callEvent('user.login', $row['name'], $row['index']);

	// Evertyhing went OK!	
	return true;
}

// Destroy session, cleanup variables and database
function gl_user_logout() {
	global $sql;
	
	// Notify all event chains
	callEvent('user.logout', $_SESSION[PLAYER][PROFILE]['name'], $_SESSION[PLAYER][PROFILE]['index']);

	// Notify Grid alteration
	callEvent('grid.alter', $_SESSION[PLAYER][GUID], $_SESSION[PLAYER][DATA]['x'], $_SESSION[PLAYER][DATA]['y'], $_SESSION[PLAYER][DATA]['map']); // Missing object here

	// Logout chars and profile
	$sql->query("UPDATE `char_instance` SET `online` = 0 WHERE `account` = ".$_SESSION[PLAYER][PROFILE]['index']);
	$sql->query("UPDATE `users_accounts` SET `online` = 0 WHERE `index` = ".$_SESSION[PLAYER][PROFILE]['index']);
	
	// Cleanup user dynamic updates
	gl_dynupdate_cleanup();
	
	// Cleanup session
	unset($_SESSION[PLAYER]);
	session_destroy();
		
	// Everything went OK!
	return true;
}

// Choose a user's charachter and load all the required information on Session
function gl_user_select_char($char_guid) {
	global $sql;

	// Notify all event chains and allow interrupt
	if (!callEvent('user.choose', $_SESSION[PLAYER][PROFILE]['name'], $_SESSION[PLAYER][PROFILE]['index'], $char_guid)) {
		return false;
	}

	// Unset previous information
	unset($_SESSION[PLAYER][DATA]);
	$sql->query("UPDATE `char_instance` SET `online` = 0 WHERE `account` = ".$_SESSION[PLAYER][PROFILE]['index']);
	
	// Update data
	$_SESSION[PLAYER][DATA]=gl_get_guid_vars($char_guid);
	$_SESSION[PLAYER][GUID]=$char_guid;
	$sql->query("UPDATE `char_instance` SET `online` = 1 WHERE `account` = ".$_SESSION[PLAYER][PROFILE]['index']." AND `guid` = $char_guid");

	// Notify Grid alteration
	callEvent('grid.alter', $_SESSION[PLAYER][GUID], $_SESSION[PLAYER][DATA]['x'], $_SESSION[PLAYER][DATA]['y'], $_SESSION[PLAYER][DATA]['map']); // New object here
	
	// Everything went OK!s
	return true;
}

// User storage
function gl_user_getvar($variable, $player=false) {
	// If player GUID is missing, use default player
	if (!$player) $player=$_SESSION[PLAYER][GUID];
	
	// Get var
	$vars=gl_get_guid_vars($player);
	return $vars[$variable];
}

## ==========================================
##            SYSTEM OPERATIONS
## ==========================================

// Redirect to a new operation
function gl_redirect($new_operation, $delay_load=false) {
	if (defined("IN_PROCESS") && !$delay_load) {
		// We are inside a process system (ex. called by an action script)
		// [Direct contact with the browser]
		header('location: index.php?a='.$new_operation);
		die('<script language="javascript">setTimeout(function() { window.location=\'index.php?a='.$new_operation.'\'; }, 100);</script><p align="center">If nothing happens, <a href="index.php?a='.$new_operation.'">click here</a></p>');

	} else {
		// We are outside the process system (ex. called by message hook)
		// So, force client to change the location to the new action (since there is no way to
		// redirect output through this pipe)
		// [Indirect contact with the browser (through glAPI)]
		relayMessage(MSG_INTERFACE, 'NAVIGATE', $new_operation);
	}
}

// Stack a new operation
function gl_do($new_operation, $new_parameters=false) {
	global $operationstack;
	
	if (defined("IN_PROCESS")) {
		// We are inside a process system (ex. called by an action script)
		// [Direct contact with the browser]
		array_push($operationstack, array('op' => $new_operation, 'parm' => $new_parameters));
	} else {
		// We are outside the process system (ex. called by message hook)
		// So, force client to call the new action (since there is no way to
		// use an existing session to feed it)
		// [Indirect contact with the browser (through glAPI)]
		$parm='';
		if ($new_parameters && is_array($new_parameters)) {
			foreach ($new_parameters as $name => $value) {
				$parm.='&'.$name.'='.urlencode($value);
			}
		}
		relayMessage(MSG_INTERFACE, 'CALL', '?a='.$new_operation.$parm, true);
	}
}

## ==========================================
##             HELPER FUNCTIONS
## ==========================================

// Mathematic operations
function gl_distance($x1,$y1,$x2,$y2) {
	return sqrt(pow(($x1-$x2),2) + pow(($y1-$y2),2));
}

?>