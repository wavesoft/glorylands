<?
include "../config/config.php";
include "../engine/includes/base.php";

if ($_REQUEST['a'] == 'import') {
	$sql->query('INSERT INTO `data_maps` (`name`,`filename`,`background`) VALUES (\''.$_REQUEST['name'].'\', \''.$_REQUEST['name'].'\', \'z-field-ext-1-1.png\')');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Game Maps</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<p>Maps in database:</p>
<table width="100%" class="general">
<tr>
	<th class="general" width="16">&nbsp;</th>
	<th class="general" width="60">Elevation</th>
	<th class="general" width="60">Background</th>
	<th class="general">Name</th>
	<th class="general" width="60">Actions</th>
</tr>
<?php

$maps = array();
$rows = $sql->query("SELECT * FROM `data_maps`");
while ($row = $sql->fetch_array_fromresults($rows)) {
	
	$maps[] = $row['filename'];
	$icon = 'ok.gif';
	
?>
<tr>
	<td><img src="images/<? echo $icon; ?>" /></td>
	<td class="tileset"><? echo $row['z-base'] ?></td>
	<td class="tileset"><img src="../images/tiles/<? echo $row['background']; ?>" /></td>
	<td><b><? echo $row['name']; ?></b><br /><? echo $row['description']; ?></td>
	<td>
		<a title="Edit Package Information" href="maps_edit.php?id=<?php echo $row['index']; ?>"><img src="images/edit.gif" width="16" height="16" border="0" /></a>
		<a title="Display Map" href="rendermap.php?map=<?php echo $row['filename']; ?>"><img border="0" src="images/14_layer_novisible.gif" /></a>
		<a title="Delete Map" href="maps_delete.php?map=<?php echo $row['filename']; ?>"><img border="0" src="images/14_layer_deletelayer.gif" /></a>	</td>
</tr>
<?php
}
?>
</table>
<p>Maps not imported, available for installation:</p>
<table width="100%" class="general">
<tr>
	<th class="general" width="16">&nbsp;</th>
	<th class="general">Name</th>
	<th class="general" width="60">Actions</th>
</tr>
<?php

$d = dir(DIROF('DATA.MAPS'));
while (false !== ($entry = $d->read())) {
	
	if (substr($entry,0,1) == '.') continue;
	if (strtolower(substr($entry,-4)) != '.map') continue;
	$entry = substr($entry,0,-4);
	
	if (!in_array($entry, $maps)) {
		$icon = 'folder_tar_small.gif';	
?>
<tr>
	<td><img src="images/<? echo $icon; ?>" /></td>
	<td><? echo $entry; ?></td>
	<td>
		<a title="Insert into database" href="maps.php?a=import&name=<?php echo urlencode($entry); ?>"><img src="images/edit_add.gif" width="16" height="16" border="0" /></a>
	</td>
</tr>
<?php
	}
}
$d->close();
?>
</table>
<p>Install new Map:</p>
<form enctype="multipart/form-data" action="maps_upload.php" method="post">
<table class="general">
<tr>
	<td>Upload map: <br /><small>Archive including .jmap & .zmap files</small></td>
	<td><input type="file" name="file" /></td>
</tr>
<tr height="32">
	<td>&nbsp;</td>
	<td valign="middle"><input type="submit" value="Upload & Install" /></td>
</tr>
</table>
</form>
</body>
</html>
