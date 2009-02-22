<?php

// Validate
if (trim($_REQUEST['config']['HOST'])=='') {
	echo '<div class="error">MySQL server host name not defined!</div>';
	$step=3;
	return;
}
if (trim($_REQUEST['config']['USER'])=='') {
	echo '<div class="error">MySQL user name not defined!</div>';
	$step=3;
	return;
}
if (trim($_REQUEST['config']['DATABASE'])=='') {
	echo '<div class="error">MySQL database name not defined!</div>';
	$step=3;
	return;
}
if (trim($_REQUEST['config']['PASSWORD'])=='') {
	echo '<div class="error">MySQL user password not defined!</div>';
	$step=3;
	return;
}
if ($_REQUEST['config']['PASSWORD']!=$_REQUEST['config']['PASSWORD_CONFIRM']) {
	echo '<div class="error">Passwords do not match!</div>';
	$step=3;
	return;
}
if ($_REQUEST['setupsql']['PASSWORD']!=$_REQUEST['setupsql']['PASSWORD_CONFIRM']) {
	echo '<div class="error">MySQL Suer-User passwords do not match!</div>';
	$step=3;
	return;
}

// Detect the setup user database information
$db_user = $_REQUEST['config']['USER'];
$db_pwd = $_REQUEST['config']['PASSWORD'];
if ($_REQUEST['setupsql']['PASSWORD']!='') $db_pwd=$_REQUEST['setupsql']['PASSWORD'];
if ($_REQUEST['setupsql']['USER']!='') $db_user=$_REQUEST['setupsql']['USER'];
$_SESSION['setupsql'] = $_REQUEST['setupsql'];

// Rremove the confirmation parameter
unset($_REQUEST['config']['PASSWORD_CONFIRM']);

// Try to connect to SQL
@$link = mysql_connect($_REQUEST['config']['HOST'], $db_user, $db_pwd);
if (!$link) {
	echo '<div class="error">Cannot connect to MySQL! Error: '.mysql_error().'</div>';
	$step=3;
	return;
}

// Store the info in the session
if (!isset($_SESSION['config'])) $_SESSION['config'] = array();
$_SESSION['config']['DB'] = array();
foreach ($_REQUEST['config'] as $var => $value) {
	$_SESSION['config']['DB'][$var] = stripslashes($value);
}

// Get MySQL information
$version=mysql_get_server_info($link);
if (!$version) {
	echo '<div class="warn">Unable to detect MySQL Version. Please make sure you have MySQL 5.x or later before continuing!</div>';
} else {
	$parts = explode('.',$version);
	if (!( ((int)$parts[0]>=5) && ((int)$parts[1]>=0) )) {
		echo '<div class="error">MySQL Version 5.x or later is required! Version detected: '.$version.'</div>';
		$step=3;
		return;
	}
}

// Switch to unicode
mb_internal_encoding('utf-8');
mysql_query("SET CHARSET 'utf8'");

// Check if the database already exists
$query = mysql_query("SHOW DATABASES LIKE '".$_REQUEST['config']['DATABASE']."'");
$rows = mysql_num_rows($query);
mysql_free_result($query);
if (isset($_REQUEST['newdb'])) {
	if ($rows == 0) {
		// Database does not exist
		$query = mysql_query("CREATE DATABASE  `".$_REQUEST['config']['DATABASE']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci");
		if (!$query) {
			echo '<div class="error">Cannot create new database! Make sure the MySQL user has CREATE priviledges! MySQL Error: '.mysql_error().'</div>';
			$step=3;
			return;	
		}
		mysql_free_result($query);
		
	} else {
		// Database exists... flush it
		mysql_select_db($_REQUEST['config']['DATABASE']);
		$query = mysql_query("SHOW TABLES");
		if (!$query) {
			echo '<div class="error">Cannot read the database tables! Make sure the MySQL user has SHOW priviledges! MySQL Error: '.mysql_error().'</div>';
			$step=3;
			return;	
		}
		while ($row = mysql_fetch_array($query, MYSQL_NUM)) {
			$ans=mysql_query("DROP TABLE `".$row[0]."`");
			if (!$ans) {
				echo '<div class="error">Cannot drop database table '.$row[0].'! Make sure the MySQL user has DROP priviledges! MySQL Error: '.mysql_error().'</div>';
				$step=3;
				return;	
			}
		}
		mysql_free_result($query);
	}
} else {
	if ($rows == 0) {
		echo '<div class="error">The database '.$_REQUEST['config']['DATABASE'].' was not found in the server!</div>';
		$step=3;
		return;		
	}
}

// Save the configuration file
$config_tpl = file_get_contents("data/files/config.php.tpl");
foreach ($_SESSION['config'] as $group => $vargroup) {
	if  (is_array($vargroup)) {
		foreach ($vargroup as $var => $value) {
			$config_tpl = str_replace("{#{$group}_{$var}#}", addslashes($value), $config_tpl);
		}
	}
}
file_put_contents($_SESSION['config']['GAME']['BASE'].'/config/config.php', $config_tpl);


?>
