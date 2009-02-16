<?php
// Validate
if (!is_dir(stripslashes($_REQUEST['config']['BASE']))) {
	echo '<div class="error">Directory <b>'.stripslashes($_REQUEST['config']['BASE']).'</b> was not found in the server</div>';
	$step=2;
	return;
}

// Store the info in the session
if (!isset($_SESSION['config'])) $_SESSION['config'] = array();
$_SESSION['config']['GAME'] = array();
foreach ($_REQUEST['config'] as $var => $value) {
	$_SESSION['config']['GAME'][$var] = stripslashes($value);
}
?>
