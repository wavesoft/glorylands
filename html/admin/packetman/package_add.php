<?php 
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Create package</title>
<link rel="stylesheet" type="text/css" href="res/stylemain.css">
</head>

<body>
<?php
if ($_REQUEST['a'] == 'add_data') {

	$name = mysql_escape_string($_REQUEST['p_name']);
	$guid = mysql_escape_string($_REQUEST['p_guid']);
	$ctime = time();
	
	// Create data folder and folder structure
	mkdir(DIROF('SYSTEM.ADMIN').'packages/'.$guid);
	mkdir(DIROF('SYSTEM.ADMIN').'packages/'.$guid.'/disabled');
	mkdir(DIROF('SYSTEM.ADMIN').'packages/'.$guid.'/uninstall');
	mkdir(DIROF('SYSTEM.ADMIN').'packages/'.$guid.'/source');
	
	// Create database entry
	$sql->query("INSERT INTO `system_packages` (`name`,`guid`,`require`,`type`,`installdate`) VALUES ('{$name}','{$guid}','a:0:{}','FILES',{$ctime})");
	
?>
<center>
<div class="centerblock" align="center">
<p>A new black package named <b><?php echo $_REQUEST['p_name']; ?></b> is added on the database. Please proceed with the manifest setup.</b></p>
<p><a href="packagemanifest.php?guid=<?php echo $guid; ?>">Click here to go to the manifest management</a></p>
</div>
</center>
<?php

} else {
?>
<table>
<tr>
	<td width="35" rowspan="2" valign="top"><img src="../images/package_block.gif" align="absmiddle" /></td>
	<td align="left"><font size="+2"><b> Create new package</b></font></td>
</tr>
<tr>
	<td align="left">Create a new blank package</td>
</tr>
<tr>
	<td colspan="2">	
	<p>
	<form action="" method="post">
	<input type="hidden" name="a" value="add_data" />
	<table>
		<tr>
			<td><b>Package Name:</b></td>
			<td><input size="35" type="text" name="p_name" /></td>
		</tr>
		<tr>
			<td><b>Package GUID:</b></td>
			<td><input size="35" type="text" name="p_guid" value="<?php echo md5( rand(1,100000) . microtime() . date("YmdHis") . rand(1,100000) . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']); ?>" /></td>
		</tr>
	</table>
	<input type="submit" value="Create package" />
	</form>
	</p>
	</td>
</tr>
</table>
<?php
}
?>
</body>
</html>
