<?php 
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Package Manifest</title>
<link rel="stylesheet" type="text/css" href="res/stylemain.css">
<script language="javascript">
function makeEditable(id) {
	var v_elm = document.getElementById('view'+id);
	var e_elm = document.getElementById('edit'+id);
	var v_value = document.getElementById('v'+id);
	var e_value = document.getElementById('e'+id);
	
	v_elm.style['display'] = 'none';
	e_elm.style['display'] = '';
	
	v_value.value = e_value.value;
	v_value.focus();
	v_value.select();
}
function commitEdit(id) {
	var v_elm = document.getElementById('view'+id);
	var e_elm = document.getElementById('edit'+id);
	var v_value = document.getElementById('v'+id);
	var e_value = document.getElementById('e'+id);
	
	v_elm.style['display'] = '';
	e_elm.style['display'] = 'none';
	
	e_value.value = v_value.value;
	
	var value = v_value.value;
	if (value=='') { value='&nbsp;'; };
	v_elm.innerHTML = value;
}
</script>
</head>
<body>
<?php
$guid = $_GET['guid'];
$ans = $sql->query("SELECT * FROM `system_packages` WHERE `guid` = '{$_GET['guid']}'");
if (!$ans) die($sql->getError());
if ($sql->emptyResults) die("Cannot find package with guid {$_GET['guid']}");

$row = $sql->fetch_array();
$pid = $row['index'];

// Perform some general actions
if ($_REQUEST['a'] == 'gen_edit') {
	$row = $_REQUEST['package'];
	$sql->editRow('system_packages', "`index` = {$pid}", $row);
	$row['index'] = $pid;

	?>
	<center>
	<div class="centerblock" align="center">
	<p>The package parameters are updated</p>
	</div>
	</center>
	<form action="navbar.php" target="left">
	<input type="hidden" name="rand" value="<?php echo md5(time().rand(0,100)); ?>" />
	<input type="hidden" name="guid" value="<?php echo $guid; ?>" />
	</form>
	<script language="javascript">
	document.forms[0].submit();
	</script>
	<?php
}

?>
<table width="100%">
<tr>
	<td width="35" rowspan="2" valign="top"><img src="../images/manifest32.gif" align="absmiddle" /></td>
	<td align="left"><font size="+2"><b> <?php echo $row['name']; ?></b></font></td>
</tr>
<tr>
	<td align="left">Package manifest decleration of package <?php echo $row['name']; ?></td>
</tr>
</table>
<form action="" method="post">
<input type="hidden" name="guid" value="<?php echo $guid; ?>" />
<input type="hidden" name="a" value="gen_edit" />
<table width="100%" class="filetable" cellpadding="1" cellspacing="1">
<tr class="head">
	<td colspan="6">General Parameters</td>
</tr>
<?php

$elmid = 0;
function echoParam($name, $value, $displayname, $icon) {
	global $elmid;
	$elmid++;
	$svalue = $value;
	if ($svalue=='') $svalue='&nbsp;';
	echo "<tr>";
	echo "<td width=\"16\"><img src=\"../images/$icon\" /></td><td width=\"200\"><b>{$displayname}: </b></td>";
	echo "<td colspan=\"3\"><span style=\"display:none\" id=\"edit{$elmid}\"><input type=\"text\" onblur=\"commitEdit($elmid)\" style=\"width: 100%\" id=\"v{$elmid}\" /></span><div style=\"width: 100%\" id=\"view{$elmid}\" id=\"s{$elmid}\" ondblclick=\"makeEditable($elmid)\">$svalue</div><input type=\"hidden\" id=\"e{$elmid}\" name=\"$name\" value=\"$value\" /></td>";
	echo "</tr>\n";
}
function echoDropdown($name, $value, $displayname, $icon, $values) {
	global $elmid;
	$elmid++;
	$svalue = $value;
	if ($svalue=='') $svalue='&nbsp;';
	echo "<tr>";
	echo "<td width=\"16\"><img src=\"../images/$icon\" /></td><td width=\"200\"><b>{$displayname}: </b></td>";
	echo "<td colspan=\"3\"><span style=\"display:none\" id=\"edit{$elmid}\"><select onblur=\"commitEdit($elmid)\" style=\"width: 100%\" id=\"v{$elmid}\">";
	foreach ($values as $value => $cname) {
		echo "<option value=\"$value\">$cname</option>";
	}
	echo "</select></span><div style=\"width: 100%\" id=\"view{$elmid}\" id=\"s{$elmid}\" ondblclick=\"makeEditable($elmid)\">$svalue</div><input type=\"hidden\" id=\"e{$elmid}\" name=\"$name\" value=\"$value\" /></td>";
	echo "</tr>\n";
}

echoParam("package[guid]", $row['guid'], "Package GUID", "parameter.gif");
echoParam("package[name]", $row['name'], "Display Name", "parameter.gif");
echoDropdown("package[type]", $row['type'], "Package Type", "parameter.gif", array(
	"TILESET" => "Tileset",
	"MAP" => "World Map",
	"MODELSET" => "Model Set",
	"PLUGIN" => "System Plugin",
	"DATABASE" => "Database Parameters",
	"PATCH" => "Patch or bugfix",
	"COMPONENT" => "Engine component",
	"ADMIN" => "Administration component",
	"THEME" => "Interface theme",
	"MIXED" => "Mixed Types"
));
echoParam("package[version]", $row['version'], "Version", "parameter.gif");
echoParam("package[description]", $row['description'], "Description", "parameter.gif");
echoParam("package[author]", $row['author'], "Author", "parameter.gif");
echoParam("package[copyright]", $row['copyright'], "Copyright", "parameter.gif");
echoParam("package[website]", $row['website'], "Website", "parameter.gif");

?>
<tr class="folder">
	<td colspan="6" align="right"><input type="submit" value="Update Changes" /></td>
</tr>
</table>
</form>
<br />

<form action="package_depend.php" method="post">
<input type="hidden" name="a" id="a" value="" />
<input type="hidden" name="guid" value="<?php echo $guid; ?>" />
<table width="100%" class="filetable" cellpadding="1" cellspacing="1">
<tr class="head">
	<td colspan="6">Dependencies</td>
</tr>
<?php
################### DEPENDENCIES #######################
$depends = unserialize($row['require']);
$i = 0;
if ($denepds) {
	foreach ($depends as $depend) {
		$elmid++;
		$ver = "<em>(Any)</em>";
		if ($depends['ver']>0) $ver = $depends['ver'];
	?>
	<tr>
		<td width="16"><input id="e<?php echo $elmid; ?>" name="depend[<?php echo $i; ?>]" type="checkbox" /></td>
		<td width="16"><img src="../images/depend.gif" /></td>
		<td width="178"><label for="e<?php echo $elmid; ?>"><b><?php echo $depend['name']; ?></b></label></td>
		<td><label for="e<?php echo $elmid; ?>"><?php echo $depend['guid']; ?></label></td>
		<td width="50"><label for="e<?php echo $elmid; ?>"><b>Version</b></label></td>
		<td width="100"><label for="e<?php echo $elmid; ?>"><?php echo $ver; ?></label></td>
	</tr>
	<?php
		$i++;
	}
}
if (sizeof($depends)==0) {
?>
<tr>
	<td colspan="6"><em>(No Dependencies)</em></td>
</tr>
<?php
}
//======================================================
?>
<tr class="folder">
	<td colspan="6" align="right"><input onclick="window.location='package_depend.php?a=add&guid=<?php echo $guid; ?>';" type="button" value="Create New" /> <input onclick="this.form.a.value='delete';" type="submit" value="Delete Selected" /> <input onclick="this.form.a.value='edit';" type="submit" value="Edit Selected" /></td>
</tr>
</table>
</form>
<br />

<form action="package_dictionaries.php" method="post">
<input type="hidden" name="guid" value="<?php echo $guid; ?>" />
<input type="hidden" name="a" value="gen_edit" />
<table width="100%" class="filetable" cellpadding="1" cellspacing="1">
<tr class="head">
	<td colspan="4">Dictionary Entries</td>
</tr>
<?php
################### DICTIONARY ########################

$ans = $sql->query("SELECT * FROM `system_dictionaries` WHERE `package` = {$pid}");
while ($info = $sql->fetch_array()) {
$elmid++;
?>
<tr>
	<td width="16"><input id="e<?php echo $elmid; ?>" name="entry[<?php echo $info['index']; ?>]" type="checkbox" /></td>
	<td width="16"><img src="../images/comment.gif" /></td>
	<td width="178"><label for="e<?php echo $elmid; ?>"><b><?php echo $info['group'] ?></b></label></td>	
	<td><label for="e<?php echo $elmid; ?>"><em><?php echo $info['name']; ?></em> = <?php echo "<em>'".$info['value']."'</em>"; if ($info['mode']=='DYNAMIC') echo " <small><em>(Dynamic)</em></small>" ?></label></td>
</tr>
<?php
}
if ($sql->emptyResults) {
?>
<tr>
	<td colspan="4"><em>(No Dictionary Entries)</em></td>
</tr>
<?php
}
//======================================================
?>
<tr class="folder">
	<td colspan="4" align="right"><input onclick="window.location='package_dictionaries.php?a=add&guid=<?php echo $guid; ?>';" type="button" value="Create New" /> <input type="submit" onclick="this.form.a.value='delete';" value="Delete Selected" /> <input type="submit" onclick="this.form.a.value='edit';" value="Edit Selected" /></td>
</tr>
</table>
</form>
<br />

<form action="package_hooks.php" method="post">
<input type="hidden" name="a" id="a" value="" />
<input type="hidden" name="guid" value="<?php echo $guid; ?>" />
<table width="100%" class="filetable" cellpadding="1" cellspacing="1">
<tr class="head">
	<td colspan="5">Script Hooks</td>
</tr>
<?php
##################### HOOKS ##########################

$ans = $sql->query("SELECT * FROM `system_hooks` WHERE `package` = {$pid}");
while ($info = $sql->fetch_array()) {
$elmid++;
?>
<tr>
	<td width="16"><input id="e<?php echo $elmid; ?>" name="hook[<?php echo $info['index']; ?>]" type="checkbox" /></td>
	<td width="16"><img src="../images/link.gif" /></td>
	<td width="178"><label for="e<?php echo $elmid; ?>"><b><?php echo $info['hook']; ?></b></label></td>
	<td><label for="e<?php echo $elmid; ?>"><em><?php echo $info['function']; ?>()</em> on <em><?php echo $info['filename']; ?></em></label></td>
</tr>
<?php
}
if ($sql->emptyResults) {
?>
<tr>
	<td colspan="5"><em>(No Interrupt Hooks)</em></td>
</tr>
<?php
}
//======================================================
?>
<tr class="folder">
	<td colspan="5" align="right"><input onclick="window.location='package_hooks.php?a=add&guid=<?php echo $guid; ?>';" type="button" value="Create New" /> <input type="submit" onclick="this.form.a.value='delete';" value="Delete Selected" /> <input type="submit" onclick="this.form.a.value='edit';" value="Edit Selected" /></td>
</tr>
</table>
</form>
<br />

<form action="package_installfiles.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="a" id="a" value="" />
<input type="hidden" name="guid" value="<?php echo $guid; ?>" />
<table width="100%" class="filetable" cellpadding="1" cellspacing="1">
<tr class="head">
	<td colspan="5">Installation Files</td>
</tr>
<?php
#################### INSTALL FILES #########################

$ans = $sql->query("SELECT * FROM `system_packages_install` WHERE `package` = {$pid}");
while ($info = $sql->fetch_array()) {
	$elmid++;
	if ($info['imode'] == 'SCRIPT') {
		$type = "PHP Script";
		$data = $info['data'];
	} else if ($info['imode'] == 'INLINE') {
		$type = "InLine Script";
		$data = "[Data]";
	} else if ($info['imode'] == 'SQL') {
		$type = "SQL Script";
		$data = $info['data'];
	} 
?>

<tr>
	<td width="16"><input id="e<?php echo $elmid; ?>" name="file[<?php echo $info['index']; ?>]" type="checkbox" /></td>
	<td width="16"><img src="../images/edit_add.gif" /></td>
	<td width="60"><label for="e<?php echo $elmid; ?>"><em><?php echo $info['use']; ?></em></label></td>
	<td width="100"><label for="e<?php echo $elmid; ?>"><b><?php echo $type ?></b></label></td>
	<td><label for="e<?php echo $elmid; ?>"><?php echo $data; ?></label></td>
</tr>
<?php
}
if ($sql->emptyResults) {
?>
<tr>
	<td colspan="5"><em>(No SQL Files)</em></td>
</tr>
<?php
}
//======================================================
?>
<tr class="folder">
	<td colspan="5" align="right"><input type="file" name="uploadfile" /> <input onclick="this.form.a.value='upload';" type="submit" value="Upload File" /> <input onclick="window.location='package_installfiles.php?a=add&guid=<?php echo $guid; ?>';" type="button" value="Create New" /> <input type="submit" onclick="this.form.a.value='delete';" value="Delete Selected" /> <input type="submit" onclick="this.form.a.value='edit';" value="Edit Selected" /></td>
</tr>
</table>
</form>
<br />

<form action="package_uninstallfiles.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="a" id="a" value="" />
<input type="hidden" name="guid" value="<?php echo $guid; ?>" />
<table width="100%" class="filetable" cellpadding="1" cellspacing="1">
<tr class="head">
	<td colspan="5">Uninstallation Files</td>
</tr>
<?php
#################### UNINSTALL ########################

$ans = $sql->query("SELECT * FROM `system_packages_uninstall` WHERE `package` = {$pid}");
while ($info = $sql->fetch_array()) {
	$elmid++;
	if ($info['umode'] == 'SCRIPT') {
		$type = "PHP Script";
		$data = $info['data'];
	} else if ($info['umode'] == 'INLINE') {
		$type = "InLine Script";
		$data = "[Data]";
	} else if ($info['umode'] == 'SQL') {
		$type = "SQL Script";
		$data = $info['data'];
	} 
?>
<tr>
	<td width="16"><input id="e<?php echo $elmid; ?>" name="file[<?php echo $info['index']; ?>]" type="checkbox" /></td>
	<td width="16"><img src="../images/edit_remove.gif" /></td>
	<td width="60"><label for="e<?php echo $elmid; ?>"><em><?php echo $info['use']; ?></em></label></td>
	<td width="100"><label for="e<?php echo $elmid; ?>"><b><?php echo $type ?></b></label></td>
	<td><label for="e<?php echo $elmid; ?>"><?php echo $data; ?></label></td>
</tr>
<?php
}
if ($sql->emptyResults) {
?>
<tr>
	<td colspan="5"><em>(No Uninstall Scripts)</em></td>
</tr>
<?php
}//======================================================
?>
<tr class="folder">
	<td colspan="5" align="right"><input type="file" name="uploadfile" /> <input onclick="this.form.a.value='upload';" type="submit" value="Upload File" /> <input onclick="window.location='package_uninstallfiles.php?a=add&guid=<?php echo $guid; ?>';" type="button" value="Create New" /> <input type="submit" onclick="this.form.a.value='delete';" value="Delete Selected" /> <input type="submit" onclick="this.form.a.value='edit';" value="Edit Selected" /></td>
</tr>
</table>
</form>
<br />

</body>
</html>
