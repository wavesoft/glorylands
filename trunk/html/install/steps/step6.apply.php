<?php

// Detect the setup user database information
$db_gameuser = $_SESSION['config']['DB']['USER'];
$db_host = $_SESSION['config']['DB']['HOST'];
$db_user = $_SESSION['config']['DB']['USER'];
$db_pwd = $_REQUEST['config']['DB']['PASSWORD'];
$db_name = $_SESSION['config']['DB']['DATABASE'];
if ($_SESSION['setupsql']['PASSWORD']!='') $db_pwd=$_SESSION['setupsql']['PASSWORD'];
if ($_SESSION['setupsql']['USER']!='') $db_user=$_SESSION['setupsql']['USER'];

// Try to connect to SQL
@$link = mysql_connect($db_host, $db_user, $db_pwd);
if (!$link) {
	echo '<div class="error">Cannot connect to MySQL! Error: '.mysql_error().'</div>';
	$step=6;
	return;
}

// Should we change the user permissions?
$html_ans = '';
if (isset($_REQUEST['finalize']['sql'])) {
	$ans=mysql_query("REVOKE ALL PRIVILEGES ON `{$db_name}` . * FROM  '{$db_gameuser}'@'{$db_host}'");
	$ans=mysql_query("REVOKE GRANT OPTION ON `{$db_name}` . * FROM  '{$db_gameuser}'@'{$db_host}'");
	$ans=mysql_query("GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,INDEX,ALTER,CREATE TEMPORARY TABLES,CREATE VIEW,SHOW VIEW ON `{$db_name}` . * TO  '{$db_gameuser}'@'{$db_host}'");
	if (!$ans) {
		echo "<div class=\"error\">Cannot grant permissions of MySQL user {$db_gameuser}@{$db_host}! Error: ".mysql_error()."</div>";
		$step=6;
		return;
	}
	$html_ans .= "<li>Permissions for user {$db_user} on database {$db_name} are now limited to: SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, CREATE VIEW, SHOW VIEW</li>\n";
}

// Should we change the config file permissions?
if (isset($_REQUEST['finalize']['files'])) {
	$ans=chmod($_SESSION['config']['GAME']['BASE'].'/config/config.php', 0400);
	if (!$ans) {
		echo '<div class="error">Cannot change config/config.php permissions!</div>';
		$step=6;
		return;
	}
	$html_ans .= "<li>config/config.php is now non-writable</li>\n";
}
// Session is no longer needed! 
session_destroy();

// Go to the game
header("Refresh: 6;URL=..");

?>
<p>
<div class="completed">Operation completed. Steps performed: 
<ul>
	<li>Setup finalization</li>
	<?php echo $html_ans; ?>
</ul>
</div>
</p><p>
<div class="warn">
	<strong>Make sure you erase the install folder!</strong>
</div>
</p>
<p>You will be redirected to the game in 6 seconds. If nothing happens, <a href="..">click here</a></p>
