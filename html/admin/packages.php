<?
include "../config/config.php";
include "../engine/includes/base.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Game Packages</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<p>Packages in database:</p>
<table width="100%" class="general">
<tr>
	<th class="general" width="16">&nbsp;</th>
	<th class="general" width="60">Type</th>
	<th class="general" width="60">Status</th>
	<th class="general">Name</th>
	<th class="general" width="60">Version</th>
	<th class="general" width="120">Owner</th>
	<th class="general" width="120">Actions</th>
</tr>
<?php

$rows = $sql->query("SELECT * FROM `system_packages`");
while ($row = $sql->fetch_array_fromresults($rows)) {

	$icon = "ok.gif";
	$state = "state_active";
	$status = 'Active';
	
	switch ($row['status']) {
	
		case "INACTIVE":	$icon  = 'button_cancel.gif';
							$state = 'state_inactive';
							$status = 'Inactive';
							break;
		
		case "INCOMPLETED":	$icon  = 'package.gif';
							$state = 'state_incomplete';
							$status = 'Incompleted!';
							break;

		case "BUGGY":		$icon  = 'button_cancel.gif';
							$state = 'state_error';
							$status = 'Buggy!';
							break;

	}
	
?>
<tr>
	<td><img src="images/<? echo $icon; ?>" /></td>
	<td class="tileset"><? echo $row['type'] ?></td>
	<td class="<? echo $state; ?>"><? echo $status; ?></td>
	<td title="<? echo $row['description']; ?>"><? echo $row['name']; ?></td>
	<td>v <? echo $row['version']; ?></td>
	<td><? echo $row['author']; ?></td>
	<td>
		<a title="Disable Package" href="packages.php?action=disable&id=<?php echo $row['index']; ?>"><img border="0" src="images/button_cancel.gif" /></a>
		<a title="Upgrade Package" href="packages_upgrade.php?id=<?php echo $row['index']; ?>"><img border="0" src="images/agt_update_drivers.gif" /></a>
		<a title="Backup Package" href="packages_backup.php?id=<?php echo $row['index']; ?>"><img border="0" src="images/revert.gif" /></a>
		<a title="Restore to previous version" href="packages_restore.php?id=<?php echo $row['index']; ?>"><img border="0" src="images/recur.gif" /></a>
		&nbsp;
		<a title="Display Files" href="packages_files.php?id=<?php echo $row['index']; ?>"><img border="0" src="images/14_layer_novisible.gif" /></a>
		<a title="Uninstall Package" href="packages_uninstall.php?id=<?php echo $row['index']; ?>"><img border="0" src="images/14_layer_deletelayer.gif" /></a>
	</td>
</tr>
<?php
}
?>
</table>
<p>Install new Package:</p>
<form enctype="multipart/form-data" action="packages_upload.php" method="post">
<table class="general">
<tr>
	<td>Upload package:</td>
	<td><input type="file" name="file" /></td>
</tr>
<tr height="32">
	<td>&nbsp;</td>
	<td><input type="submit" value="Upload & Install" /></td>
</tr>
</table>
</form>
</body>
</html>
