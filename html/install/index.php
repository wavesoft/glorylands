<?php
define('NOZIP',true);
function _echo($v, $default) {
	if (!$v) {
		echo $default;
	} else {
		echo $v;
	}
}

function _selected($v, $eq, $default) {
	if (!$v) {
		if ($default) {
			echo 'selected="selected"';
		} else {
			echo '';
		}
	} else {
		if ($v == $eq) {
			echo 'selected="selected"';
		} else {
			echo '';
		}	
	}
}

function _checked($v, $eq, $default) {
	if (!$v) {
		if ($default) {
			echo 'checked="checked"';
		} else {
			echo '';
		}
	} else {
		if ($v == $eq) {
			echo 'checked="checked"';
		} else {
			echo '';
		}	
	}
}

session_start();
$step = $_REQUEST['step'];
if (!$step) $step=1;

$buf='';
if (isset($_REQUEST['prev_step'])) {
	ob_start();
	$pstep = $_REQUEST['prev_step'];
	if (file_exists('steps/step'.$pstep.'.apply.php')) include_once('steps/step'.$pstep.'.apply.php');
	$buf = ob_get_contents();
	ob_end_clean();
}
ob_implicit_flush(true);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GloryLands Web-Based MMORPG Installer</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table class="main" cellspacing="0" cellpadding="0" width="750" align="center">
<tr>
	<td colspan="2" class="head"><b>GloryLands Web-Based MMORPG</b><br />Installation wizzard</td>
</tr>
<tr>
	<td width="180" class="steps" valign="top">
		<div <?php if ($step == 1) echo 'class="active"'; ?>>01. Pre-installation Check</div>
		<div <?php if ($step == 2) echo 'class="active"'; ?>>02. Basic Config</div>
		<div <?php if ($step == 3) echo 'class="active"'; ?>>03. Database Connection</div>
		<div <?php if ($step == 4) echo 'class="active"'; ?>>04. Create database</div>
		<div <?php if ($step == 5) echo 'class="active"'; ?>>05. Install packages</div>
		<div <?php if ($step == 6) echo 'class="active"'; ?>>06. Complete installation</div>
	</td>
	<td class="content" valign="top">
	<?php echo $buf; include_once('steps/step'.$step.'.php'); ?>
	</td>
</tr>
</table>
<p align="center" class="footer">&copy; Copyright 2009. Author: John Haralampidis<br />Licenced under the GNU/GPL Licence</p>
</body>
</html>
