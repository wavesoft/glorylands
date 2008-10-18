<?php

// Register new user if told so
if ($_REQUEST['action']=='create') {
	
	$pwd = $_REQUEST['password'];
	$pwd2 = $_REQUEST['password2'];
	$name = $_REQUEST['username'];
	$mail = $_REQUEST['email'];
	if ($pwd && $name && $mail) {
		if ($pwd!=$pwd2) {
			$act_result['error']='The passwords do not match!';
		} else {
			$ans=$sql->addRow('users_accounts', array(
				'name' => $name,
				'password' => md5($pwd),
				'email' => $mail,
				'level' => 'USER'
			));
			if (!$ans) {
				$act_result['error']='Cannot create account! Internal SQL error:'. $sql->getError();
				debug_error($sql->getError());
			} else {
				// Log in new user
				gl_user_login($name, $pwd);
				gl_redirect('interface.entry');
			}
		}
	} else {
		if (!$name)	$act_result['error']='Please enter your name!';
		if (!$pwd)	$act_result['error']='Please enter a desired password!';
		if (!$mail)	$act_result['error']='Please enter your e-mail!';
	}
}


// Find out server statistics
$users = $sql->query_and_get_value("SELECT count(*) FROM `users_accounts` WHERE `online` = 1");
$max_users = 100;
$perc = ceil(100*$users/$max_users);
$act_result['server_load_img'] = ceil(7*$perc/100);
$act_result['server_load_perc'] = $perc;

?>