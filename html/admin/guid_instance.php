<?php
include "../config/config.php";
define("NOZIP",true);
include "../engine/includes/base.php";
ob_implicit_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Instance GUID</title>
</head>

<body>
<?php
if ($_REQUEST['action'] == 'insert') {

	$tpl_guid = gl_make_guid($_POST['tpl_group'],false,$_POST['tpl_index']);
	if (!$tpl_guid) {
		echo "<font color=\"red\">Unable to build a GUID based on template information provided!</fond><br />";
	} else {
	
		echo "Using instance guid <b>#{$tpl_guid}</b><br />\n";
		
		$vars = array();
		$parms = explode("\n", $_POST['vars']);
		foreach ($parms as $parm) {
			if ($parm!='') {
				$parm = explode("=",$parm);
				$vars[$parm[0]] = $parm[1];
			}
		}
		if ($_POST['name']!='') $vars['name'] = $_POST['name'];
		if ($_POST['parent']!='') $vars['parent'] = $_POST['parent'];
			
		echo "Using:<pre>".print_r($vars,true)."</pre>";
			
		$guid = gl_instance_object($tpl_guid, $vars);
		if (!$guid) {
			echo "<font color=\"red\">Unable instance a new object!</fond><br />";
		} else {		
			echo "Object instanced. New GUID is <b>#{$tpl_guid}</b><br />\n";
		}
	}

?>
<?php
}
?>
<form action="" method="post">
<table>
<tr>
	<td>Template Group</td>
    <td><input type="text" name="tpl_group" /></td>
</tr>
<tr>
	<td>Template Index</td>
    <td><input type="text" name="tpl_index" /></td>
</tr>
<tr>
	<td>Name</td>
    <td><input type="text" name="name" /></td>
</tr>
<tr>
	<td>Parent GUID</td>
    <td><input type="text" name="parent" value="0" /></td>
</tr>
<tr>
	<td>Variables</td>
    <td><textarea name="vars" cols="50" rows="3"></textarea></td>
</tr>
<tr>
	<td colspan="2"><input type="submit" value="Create" /></td>
</tr>
</table>
<input type="hidden" name="action" value="insert" />
</form>
<?php

?>
</body>
</html>
