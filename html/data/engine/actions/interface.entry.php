<?php


if ($_REQUEST['action'] == 'login') {

	$name=$_REQUEST['name'];
	$pwd=$_REQUEST['password'];
	if (!gl_user_login($name, $pwd)) {
		$act_result['loginmsg']='Username or password is invalid! Please try again!';
	} else {
		// Load and save player profile
		$act_result['player'] = $_SESSION[PLAYER];
		
		// Try to detect user's chars
		$ans=$sql->query("SELECT `guid`, `name` FROM `char_instance` WHERE `account` = ".$_SESSION[PLAYER][PROFILE]['index']);
		$chars=array();
		if (!$sql->emptyResults) {
			while ($row = $sql->fetch_array(MYSQL_ASSOC)) {
				$chars[]=$row;
			}
		}
		$act_result['chars']=$chars;
	}

} elseif ($_REQUEST['action'] == 'choose') {
	
	gl_user_select_char($_REQUEST['char']);
	gl_redirect('interface.main');
	return;

} elseif ($_REQUEST['action'] == 'logout') {

	gl_user_logout();

} else {
	///// Default interface /////
	

	// If player is loged in, return player variables
	if (isset($_SESSION[PLAYER])) {
		$act_result['player']=$_SESSION[PLAYER];

		// Try to detect user's chars
		$ans=$sql->query("SELECT `guid`, `name` FROM `char_instance` WHERE `account` = ".$_SESSION[PLAYER][PROFILE]['index']);
		$chars=array();
		if (!$sql->emptyResults) {
			while ($row = $sql->fetch_array(MYSQL_ASSOC)) {
				array_push($chars,$row);
			}
		}
		$act_result['chars']=$chars;
	}	
}

////// General information ////

// Find out server statistics
$users = $sql->query_and_get_value("SELECT count(*) FROM `users_accounts` WHERE `online` = 1");
$max_users = 100;
$perc = ceil(100*$users/$max_users);
$act_result['server_load_img'] = ceil(7*$perc/100);
$act_result['server_load_perc'] = $perc;

?>