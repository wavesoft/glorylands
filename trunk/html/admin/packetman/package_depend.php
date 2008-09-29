<?php 
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Manage package dependencies</title>
<link rel="stylesheet" type="text/css" href="res/stylemain.css">
</head>
<body>
<?php

$guid = $_REQUEST['guid'];
$ans = $sql->query("SELECT * FROM `system_packages` WHERE `guid` = '{$guid}'");
if (!$ans) die($sql->getError());
if ($sql->emptyResults) die("Cannot find package with guid {$guid}");

$row = $sql->fetch_array();
$pid = $row['index'];

//echo "<pre>".print_r($_REQUEST,true)."</pre>";


if ($_REQUEST['a'] == 'add') {
?>
<table>
<tr>
	<td width="35" rowspan="2" valign="top"><img src="../images/depend_add32.gif" align="absmiddle" /></td>
	<td align="left"><font size="+2"><b> <?php echo $row['name']; ?></b></font></td>
</tr>
<tr>
	<td align="left">Insert new dependency for this package</td>
</tr>
<tr>
	<td colspan="2">
	
	<p>
	<fieldset><legend>Select one of the pre existing packages</legend>
	<table>
	<?php
		$ans=$sql->query("SELECT * FROM `system_packages`");
		if ($sql->emptyResults) {
		
		} else {
			while ($package = $sql->fetch_array(MYSQL_ASSOC)) {
			?>
			<tr>
				<td width="16"><img src="../images/block16.gif" /></td>
				<td><a href="?a=add_data&guid=<?php echo $guid; ?>&p_name=<?php echo $package['name']; ?>&p_guid=<?php echo $package['guid']; ?>&p_ver=<?php echo $package['version']; ?>"><?php echo $package['name']; ?></a></td>
			</tr>
			<?php	
			}
		}
	?>
	</table>
	</fieldset>
	</p>
	
	<p>
	<fieldset><legend>Enter a custom package</legend>
	<form action="" method="post">
	<input type="hidden" name="a" value="add_data" />
	<input type="hidden" name="guid" value="<?php echo $guid; ?>" />
	<table>
		<tr>
			<td><b>Package Name:</b></td>
			<td><input size="32" type="text" name="p_name" /></td>
		</tr>
		<tr>
			<td><b>Package GUID:</b></td>
			<td><input size="32" type="text" name="p_guid" /></td>
		</tr>
		<tr>
			<td><b>Minimum Version:</b></td>
			<td><input size="5" maxlength="5" type="text" name="p_ver" /></td>
		</tr>
	</table>
	<input type="submit" value="Add Dependency" />
	</form>
	</fieldset>
	</p>
	
	</td>
</tr>
</table>
<br />
<input type="button" value="&lt;&lt; Back" onclick="window.location='packagemanifest.php?guid=<?php echo $guid; ?>';" />
<?php
}else if ($_REQUEST['a'] == 'add_data') {
	if ($row['require'] == '') {
		$old_data = array();
	} else {
		$old_data = unserialize($row['require']);
	}
	
	$data = array('name' => $_REQUEST['p_name'], 'guid' => $_REQUEST['p_guid'], 'ver' => $_REQUEST['p_ver']);
	array_push($old_data, $data);

	$data = serialize($old_data);	
	$sql->query("UPDATE `system_packages` SET `require` = '".mysql_escape_string($data)."' WHERE `index` = {$pid}");
?>
<center>
<div class="centerblock" align="center">
<p>A new dependant package named <b><?php echo $_REQUEST['p_name']; ?></b> is added on the package <b><?php echo $row['name']; ?></b></p>
<p><a href="packagemanifest.php?guid=<?php echo $guid; ?>">Click here to go back to the manifest management</a></p>
</div>
</center>
<?php
}else if ($_REQUEST['a'] == 'delete') {
?>
<center>
<div class="centerblock" align="center">
<p>The following packages are removed from the dependencies:<ul>
<?php
	if ($row['require'] == '') {
		$old_data = array();
	} else {
		$old_data = unserialize($row['require']);
	}
	foreach ($_REQUEST['depend'] as $index => $value) {
		echo "<li>".$old_data[$index]['name']."</li>";
		unset($old_data[$index]);
	}
	$data = serialize($old_data);	
	$sql->query("UPDATE `system_packages` SET `require` = '".mysql_escape_string($data)."' WHERE `index` = {$pid}");
?>
</ul></p>
<p><a href="packagemanifest.php?guid=<?php echo $guid; ?>">Click here to go back to the manifest management</a></p>
</div>
</center>
<?php
}else if ($_REQUEST['a'] == 'edit') {
	if ($row['require'] == '') {
		$old_data = array();
	} else {
		$old_data = unserialize($row['require']);
	}
?>
<form action="" method="post">
<input type="hidden" name="a" value="edit_data" />
<input type="hidden" name="guid" value="<?php echo $guid; ?>" />
<table>
<tr>
	<td width="35" rowspan="2" valign="top"><img src="../images/depend_add32.gif" align="absmiddle" /></td>
	<td align="left"><font size="+2"><b> <?php echo $row['name']; ?></b></font></td>
</tr>
<tr>
	<td align="left">Insert new dependency for this package</td>
</tr>
<tr>
	<td colspan="2">	
<?php
	foreach ($_REQUEST['depend'] as $index => $value) {
?>
	<p>
	<fieldset><legend>Edit package <b><?php echo $old_data[$index]['name']; ?></b></legend>
	<table>
		<tr>
			<td><b>Package Name:</b></td>
			<td><input size="32" type="text" name="p_name[<?php echo $index; ?>]" value="<?php echo $old_data[$index]['name']; ?>" /></td>
		</tr>
		<tr>
			<td><b>Package GUID:</b></td>
			<td><input size="32" type="text" name="p_guid[<?php echo $index; ?>]" value="<?php echo $old_data[$index]['guid']; ?>" /></td>
		</tr>
		<tr>
			<td><b>Minimum Version:</b></td>
			<td><input size="5" maxlength="5" type="text" name="p_ver[<?php echo $index; ?>]" value="<?php echo $old_data[$index]['ver']; ?>" /></td>
		</tr>
	</table>
	</fieldset>
	</p>
<?php
	}
?>
	</td>
</tr>
</table>
<input type="button" value="&lt;&lt; Back" onclick="window.location='packagemanifest.php?guid=<?php echo $guid; ?>';" /> <input type="submit" value="Update Dependencies" />
</form>
<?php
}else if ($_REQUEST['a'] == 'edit_data') {
	if ($row['require'] == '') {
		$old_data = array();
	} else {
		$old_data = unserialize($row['require']);
	}
?>
<center>
<div class="centerblock" align="center">
<p>The following packages are modified:<ul>
<?php
	if ($row['require'] == '') {
		$old_data = array();
	} else {
		$old_data = unserialize($row['require']);
	}
	foreach ($_REQUEST['p_name'] as $index => $name) {
		echo "<li>".$old_data[$index]['name']."</li>";
		$old_data[$index]['name'] = $name;
		$old_data[$index]['guid'] = $_REQUEST['p_guid'][$index];
		$old_data[$index]['ver'] = $_REQUEST['p_ver'][$index];
	}
	$data = serialize($old_data);	
	$sql->query("UPDATE `system_packages` SET `require` = '".mysql_escape_string($data)."' WHERE `index` = {$pid}");
?>
</ul></p>
<p><a href="packagemanifest.php?guid=<?php echo $guid; ?>">Click here to go back to the manifest management</a></p>
</div>
</center>
<?php
}
?>
</body>
</html>
