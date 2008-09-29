<?php 
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Manage package uninstallation files</title>
<link rel="stylesheet" type="text/css" href="res/stylemain.css">
<script language="javascript">
var popUpWin=false;
function winpopup(url) {
  if(popUpWin) {
    if(!popUpWin.closed) popUpWin.close();
  }
  var width = 500;
  var height = 300;
  var left = (screen.width-width)/2;
  var top = (screen.height-height)/2;
  popUpWin = open(url, 'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=yes,width='+width+',height='+height+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
}
</script>
</head>
<body>
<?php

//echo "<pre>".print_r($_REQUEST,true)."</pre>";

$guid = $_REQUEST['guid'];
$ans = $sql->query("SELECT * FROM `system_packages` WHERE `guid` = '{$guid}'");
if (!$ans) die($sql->getError());
if ($sql->emptyResults) die("Cannot find package with guid {$guid}");

$row = $sql->fetch_array();
$pid = $row['index'];

if ($_REQUEST['a'] == 'upload') {

	// Make sure the filesystem is ready to receive the uploaded file
	$root = DIROF('SYSTEM.ADMIN').'/packages/'.$guid;
	if (!is_dir($root)) mkdir($root);
	$scripts = $root.'/scripts';
	if (!is_dir($scripts)) mkdir($scripts);

	if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $scripts.'/'.$_FILES['uploadfile']['name'])) {
		?>
		<center>
		<div class="centerblock" align="center">
		<p>File uploaded successfully</p>
		</div>
		</center>
		<?php
	} else {
		?>
		<center>
		<div class="centerblock" align="center">
		<p><font color="#FF0000">File couldn't be uploaded!</font></p>
		</div>
		</center>
		<?php
	}
	
	// Inport file into database
	$ftype = strtoupper(substr($_FILES['uploadfile']['name'],-3));
	$type = 'SCRIPT';
	if ($ftype=='SQL') $type='SQL';

	$sql->query("INSERT INTO `system_packages_uninstall` (`package`, `umode`, `use`, `data`) VALUES
		({$pid}, '{$type}', 'UNINSTALL', '".mysql_escape_string($_FILES['uploadfile']['name'])."')
	");
	$index=$sql->query_and_get_value("SELECT `index` FROM `system_packages_uninstall` ORDER BY `index` DESC LIMIT 0,1");

	// Edit uploaded package
	$_REQUEST['file'] = array($index => 'yes');
	$_REQUEST['a'] = 'edit';
}

if ($_REQUEST['a'] == 'upload_data') {

	// Make sure the filesystem is ready to receive the uploaded file
	$root = DIROF('SYSTEM.ADMIN').'/packages/'.$guid;
	if (!is_dir($root)) mkdir($root);
	$scripts = $root.'/scripts';
	if (!is_dir($scripts)) mkdir($scripts);

	if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $scripts.'/'.$_FILES['uploadfile']['name'])) {
		?>
		<center>
		<div class="centerblock" align="center">
		<p>File uploaded successfully</p>
		</div>
		</center>
		<?php
	} else {
		?>
		<center>
		<div class="centerblock" align="center">
		<p><font color="#FF0000">File couldn't be uploaded!</font></p>
		</div>
		</center>
		<?php
	}
	
	// Inport file into database
	$mode = $_REQUEST['f_mode'];
	$use = $_REQUEST['f_use'];
	$sql->query("INSERT INTO `system_packages_uninstall` (`package`, `umode`, `use`, `data`) VALUES
		({$pid}, '{$mode}', '{$use}', '".mysql_escape_string($_FILES['uploadfile']['name'])."')
	");

} elseif ($_REQUEST['a'] == 'edit_data') {
	
		// Make sure the filesystem is ready to receive the changed file
		$root = DIROF('SYSTEM.ADMIN').'/packages/'.$guid;
		if (!is_dir($root)) mkdir($root);
		$scripts = $root.'/scripts';
		if (!is_dir($scripts)) mkdir($scripts);
	
		// Update files
		$changed = 0;
		foreach ($_REQUEST['f_name'] as $index => $filename) {
		
			// Rename file if name is changed
			$oldname = $_REQUEST['f_oldname'][$index];
			if ($oldname != $filename) {
				rename($scripts.'/'.$oldname, $scripts.'/'.$filename);
			}
		
			// Update file
			$data = stripslashes($_REQUEST['f_data'][$index]);
			file_put_contents($scripts.'/'.$filename, $data);
			
			// Update SQL entry
			$sql->editRow('system_packages_uninstall', "`index` = $index", array(
				'umode' => $_REQUEST['f_mode'][$index],
				'use' => $_REQUEST['f_use'][$index],
				'data' => $filename
			));
			
			// Update affected records
			$changed += 1;
		}
		
		?>
		<center>
		<div class="centerblock" align="center">
		<p><b><?php echo $changed; ?></b> uninstallation files are updated on the package <b><?php echo $row['name']; ?></b></p>
		<p><a href="packagemanifest.php?guid=<?php echo $guid; ?>">Click here to go back to the manifest management</a></p>
		</div>
		</center>
		<?php

} elseif ($_REQUEST['a'] == 'delete') {
	
		// Find the directory that holds the scripts
		$root = DIROF('SYSTEM.ADMIN').'/packages/'.$guid;
		$scripts = $root.'/scripts';

		// Delete files	
		$changed = 0;
		foreach ($_REQUEST['file'] as $index => $ack) {
		
			$ans = $sql->query("SELECT * FROM `system_packages_uninstall` WHERE `index` = $index");
			$info = $sql->fetch_array();
			
			@unlink($scripts.'/'.$info['data']);

			$ans = $sql->query("DELETE FROM `system_packages_uninstall` WHERE `index` = $index");		
			$changed++;
		}
		
		?>
		<center>
		<div class="centerblock" align="center">
		<p><b><?php echo $changed; ?></b> uninstallation files are deleted on the package <b><?php echo $row['name']; ?></b></p>
		<p><a href="packagemanifest.php?guid=<?php echo $guid; ?>">Click here to go back to the manifest management</a></p>
		</div>
		</center>
		<?php

} elseif ($_REQUEST['a'] == 'edit') {
?>
<form action="" method="post">
<input type="hidden" name="a" value="edit_data" />
<input type="hidden" name="guid" value="<?php echo $guid; ?>" />
<table width="100%">
<tr>
	<td width="35" rowspan="2" valign="top"><img src="../images/edit_remove32.gif" align="absmiddle" /></td>
	<td align="left"><font size="+2"><b> <?php echo $row['name']; ?></b></font></td>
</tr>
<tr>
	<td align="left">Create new uninstallation script or SQL file</td>
</tr>
<tr>
	<td colspan="2">
	
<?php
	$root = DIROF('SYSTEM.ADMIN').'/packages/'.$guid;
	$scripts = $root.'/scripts';

	foreach ($_REQUEST['file'] as $index => $ack) {
	
		$ans = $sql->query("SELECT * FROM `system_packages_uninstall` WHERE `index` = $index");
		$info = $sql->fetch_array();
?>	
	<p>
	<fieldset><legend>Edit file <?php echo $info['data']; ?></legend>
	<input type="hidden" name="f_oldname[<?php echo $index; ?>]" value="<?php echo $info['data']; ?>" />
	<table style="width: 100%;">
		<tr>
			<td width="100"><b>Filename:</b></td>
			<td><input size="32" type="text" name="f_name[<?php echo $index; ?>]" value="<?php echo $info['data']; ?>" /></td>
		</tr>
		<tr>
			<td><b>Script Type:</b></td>
			<td>
			<select name="f_mode[<?php echo $index; ?>]">
			<option value="SCRIPT" <?php if ($info['umode']=='SCRIPT') echo 'selected="selected"'; ?>>PHP Script</option>
			<option value="SQL" <?php if ($info['umode']=='SQL') echo 'selected="selected"'; ?>>SQL Queries</option>
			</select>
			</td>
		</tr>
		<tr>
			<td><b>Executedn on:</b></td>
			<td>
			<select name="f_use[<?php echo $index; ?>]">
			<option value="UNINSTALL" <?php if ($info['use']=='UNINSTALL') echo 'selected="selected"'; ?>>Uninstallation</option>
			<option value="DISABLE" <?php if ($info['use']=='DISABLE') echo 'selected="selected"'; ?>>Disabling</option>
			</select>
			</td>
		</tr>
		<tr>
			<td><b>Script:</b></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
			<textarea name="f_data[<?php echo $index; ?>]" rows="10" style="width: 100%;"><?php echo file_get_contents($scripts.'/'.$info['data']); ?></textarea>
			</td>
		</tr>
		<?php
		if (sizeof($_REQUEST['file']) == 1) {
		?>
		<tr>
			<td>&nbsp;</td>
			<td>
			<input type="button" value="&uArr; Table Drop" onclick="winpopup('popup_tablestruct.php?mode=drop');" />
			<input type="button" value="&uArr; Table Data Remove" onclick="winpopup('popup_tabledata.php?mode=delete');" />
			</td>
		</tr>
		<?php
		}
		?>
	</table>
	</fieldset>
	</p>
<?php
	}
?>		
	</td>
</tr>
</table>
<br />
<input type="button" value="&lt;&lt; Back" onclick="window.location='packagemanifest.php?guid=<?php echo $guid; ?>';" /> <input type="submit" value="Update Files" />
</form>
<?php
} elseif ($_REQUEST['a'] == 'add_data') {

		// Make sure the filesystem is ready to receive the changed file
		$root = DIROF('SYSTEM.ADMIN').'/packages/'.$guid;
		if (!is_dir($root)) mkdir($root);
		$scripts = $root.'/scripts';
		if (!is_dir($scripts)) mkdir($scripts);
			
		// Update file
		$data = stripslashes($_REQUEST['f_data']);
		$filename = $_REQUEST['f_name'];
		file_put_contents($scripts.'/'.$filename, $data);
		
		// Insert new SQL entry
		$sql->addRow('system_packages_uninstall', array(
			'umode' => $_REQUEST['f_mode'],
			'use' => $_REQUEST['f_use'],
			'data' => $filename,
			'package' => $pid
		));
		
		?>
		<center>
		<div class="centerblock" align="center">
		<p>New uninstallation file <b><?php echo $_REQUEST['f_name']; ?></b> is added on the package <b><?php echo $row['name']; ?></b></p>
		<p><a href="packagemanifest.php?guid=<?php echo $guid; ?>">Click here to go back to the manifest management</a></p>
		</div>
		</center>
		<?php


} elseif ($_REQUEST['a'] == 'add') {
?>
<table width="100%">
<tr>
	<td width="35" rowspan="2" valign="top"><img src="../images/edit_remove32.gif" align="absmiddle" /></td>
	<td align="left"><font size="+2"><b> <?php echo $row['name']; ?></b></font></td>
</tr>
<tr>
	<td align="left">Create new uninstallation script or SQL file</td>
</tr>
<tr>
	<td colspan="2">
	
	<p>
	<fieldset><legend>Create custom file</legend>
	<form action="" method="post">
	<input type="hidden" name="a" value="add_data" />
	<input type="hidden" name="guid" value="<?php echo $guid; ?>" />
	<!-- Placeholders used by popup_tablestruct -->
	<input type="hidden" name="ph1" value="" />
	<input type="hidden" name="ph2" value="" />
	<table style="width: 100%;">
		<tr>
			<td width="100"><b>Filename:</b></td>
			<td><input size="32" type="text" name="f_name" /></td>
		</tr>
		<tr>
			<td><b>Script Type:</b></td>
			<td>
			<select name="f_mode">
			<option value="SCRIPT">PHP Script</option>
			<option value="SQL">SQL Queries</option>
			</select>
			</td>
		</tr>
		<tr>
			<td><b>Executedn on:</b></td>
			<td>
			<select name="f_use">
			<option value="UNINSTALL">Uninstallation</option>
			<option value="DISABLE">Disabling</option>
			</select>
			</td>
		</tr>
		<tr>
			<td><b>Script:</b></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
			<textarea name="f_data" rows="10" style="width: 100%;"></textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
			<input type="button" value="&uArr; Table Drop" onclick="winpopup('popup_tablestruct.php?mode=drop');" />
			<input type="button" value="&uArr; Table Data Remove" onclick="winpopup('popup_tabledata.php?mode=delete');" />
			</td>
		</tr>
	</table>
	<input type="submit" value="Create File" />
	</form>
	</fieldset>
	</p>

	<p>
	<fieldset><legend>Upload from your computer</legend>
	<form action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="a" value="upload_data" />
	<input type="hidden" name="guid" value="<?php echo $guid; ?>" />
	<table>
		<tr>
			<td><b>Filename:</b></td>
			<td><input type="file" name="uploadfile" /></td>
		</tr>
		<tr>
			<td><b>Script Type:</b></td>
			<td>
			<select name="f_mode">
			<option value="SCRIPT">PHP Script</option>
			<option value="SQL">SQL Queries</option>
			</select>
			</td>
		</tr>
		<tr>
			<td><b>Executedn on:</b></td>
			<td>
			<select name="f_use">
			<option value="UNINSTALL">Uninstallation</option>
			<option value="DISABLE">Disabling</option>
			</select>
			</td>
		</tr>
	</table>
	<input type="submit" value="Upload File" />
	</form>
	</fieldset>
	</p>
		
	</td>
</tr>
</table>
<br />
<input type="button" value="&lt;&lt; Back" onclick="window.location='packagemanifest.php?guid=<?php echo $guid; ?>';" />
<?php
} else {

}
?>
</body>
</html>
