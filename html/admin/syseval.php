<?php

// Perform any operations requested
if ($_POST['action'] == 'save') {
	file_put_contents('scripts/'.$_POST['file'].'.txt', $_POST['script']);
} elseif ($_POST['action'] == 'load') {
	$_POST['script'] = file_get_contents('scripts/'.$_POST['file'].'.txt');
} elseif ($_POST['action'] == 'del') {
	unlink('scripts/'.$_POST['file'].'.txt');
}

// Load the scripts
$d = dir("scripts");
$files = '';
while (false !== ($entry = $d->read())) {
	if (substr($entry,0,1)!='.') {
		$sel='';
		$entry = substr($entry,0,-4);
		if ($entry==$_POST['file']) $sel=' selected="selected"';
		$files.="<option value=\"$entry\"$sel>$entry</option>\n";
	}
}
$d->close();

// Load script execution libraries
chdir("..");
require_once("config/config.php");
require_once($_CONFIG[GAME][BASE]."/engine/includes/base.php");
include_once DIROF('DATA.ENGINE')."guid_dictionary.php";
include_once DIROF('DATA.ENGINE')."template_dictionary.php";
define("NOZIP",true);
ob_implicit_flush();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>GloryLands Administration :: Script evaluation</title>
</head>

<body>
<script language="javascript">
function dosave() {
	var combo = document.getElementById('file');
	var file = "";
	if (combo.value=='') {
		file = window.prompt("Please enter the filename to save the file:");
	} else {
		file = combo.value;
	}
	var data = document.getElementById('p_file');
	data.value = file;
	var data = document.getElementById('p_action');
	data.value = 'save';
	document.forms[0].submit();
}
function doload() {
	var combo = document.getElementById('file');
	var file = "";
	if (combo.value=='') {
		window.alert('Please specify a filename first!');
		return;
	} else {
		file = combo.value;
	}
	var data = document.getElementById('p_file');
	data.value = file;
	var data = document.getElementById('p_action');
	data.value = 'load';
	document.forms[0].submit();
}
function dodelete() {
	var combo = document.getElementById('file');
	var file = "";
	if (combo.value=='') {
		window.alert('Please specify a filename first!');
		return;
	} else {
		file = combo.value;
	}
	var data = document.getElementById('p_file');
	data.value = file;
	var data = document.getElementById('p_action');
	data.value = 'del';
	document.forms[0].submit();
}
function doadd(obj) {
	var txt = document.getElementById('script');
	txt.value += obj.value;
}
</script>
<p>Please enter the script you want to be executed. The script will be executed within an initialized game session</p>
<form method="post" action="">
<input type="hidden" name="file" id="p_file" />
<input type="hidden" name="action" id="p_action" />
<p>
<table>
<tr>
	<td>
	<textarea id="script" name="script" cols="120" rows="14"><?php echo stripslashes($_POST['script']); ?></textarea>
	</td>
	<td>Declared Functions<br />
      <select name="select" size="14" onchange="doadd(this)">
<?php
$func = get_defined_functions();
sort($func['user']);
foreach ($func['user'] as $name) {
	echo "<option value=\"$name\">$name</option>\n";
}
?>
      </select>	</td>
</tr>
</table>
</p>
<p>
<span style="margin-right:5px; padding: 5px; background-color:#FFC1C1; border: solid 1px #FF0000">
<input type="submit" value="Run" style="width: 120px;" />
</span>
<span style="padding: 5px; background-color:#D5D5FF; border: solid 1px #6699CC">
<select id="file">
<option value="">(New)</option>
<?php echo $files; ?>
</select>
<input type="button" value="Load" onclick="doload()" />
<input type="button" value="Save" onclick="dosave()" />
<input type="button" value="Delete" onclick="dodelete()" />
</span>
</p>
</form>
<?php
if (isset($_POST['script'])) {
	echo "<hr /><pre>";
	eval(stripslashes($_POST['script']));
	echo "</pre>";
}
?>
</body>
</html>
