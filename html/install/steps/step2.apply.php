<?php
// Validate
if (!is_dir(stripslashes($_REQUEST['config']['BASE']))) {
	echo '<div class="error">Directory <b>'.stripslashes($_REQUEST['config']['BASE']).'</b> was not found in the server</div>';
	$step=2;
	return;
}

// Store the info in the session
if (!isset($_SESSION['config'])) $_SESSION['config'] = array();
if (!isset($_REQUEST['config']['MC_ENABLE'])) $_REQUEST['config']['MC_ENABLE']='false';
$_SESSION['config']['GAME'] = array();
foreach ($_REQUEST['config'] as $var => $value) {
	$value = stripslashes($value);
	if ($var == 'BASE') $value=str_replace("\\","/",$value);
	$_SESSION['config']['GAME'][$var] = $value;
}
?>
