<?php 
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Import files</title>
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

if ($_REQUEST['a'] == 'selimport') {

	function findBaseType($base) {
		global $_CONFIG;				
		$pathparts = explode("/",$base);		
		foreach ($_CONFIG[DIRS][ALIAS] as $alias => $apath) {
			// Perform a per-subdir match 
			$refparts = substr_count($apath,'/');
			$check = '/'.implode('/',array_slice($pathparts,0,$refparts));
			if ($check=='/') $check='';
			if (strtolower($check) == strtolower($apath)) {
				return $alias;
			}
		}
		return 'UNKNOWN';
	}

	function traverseDir($root, $base) {
		$d = dir($root);
		while (false !== ($entry = $d->read())) {
			if (substr($entry,0,1)!='.') {
			   	importFile($base, $entry);
			}
		}
		$d->close();
	}
		
	function importFile($base, $file) {
		global $count, $sql, $pid, $_CONFIG;
		// Calculate filename and type
		if ($base=='') {
			$filename = $_CONFIG[GAME][BASE].'/'.$file;
		} else {
			$filename = $_CONFIG[GAME][BASE].'/'.$base.'/'.$file;
		}
		$type = findBaseType($base);
		
		// In case the file is directory, use traverseModel 
		// and do not import this entry
		if (is_dir($filename)) {
			traverseDir($filename,$base.'/'.$file);
			return;
		}
		
		// Import file
		$sql->addRow('system_files', array(
			'type' => $type,
			'package' => $pid,
			'filename' => $filename,
			'version' => 1,
			'hash' => md5_file($filename)
		));
		
		// Update counter
		$count++;
	}
	
	$count = 0;
	foreach ($_SESSION[TEMP]['checked_files'] as $base => $files) {
		foreach ($files as $file => $ack) {
			importFile($base, $file);
		}
	}
	
?>
<center>
<div class="centerblock" align="center">
<p><b><?php echo $count; ?></b> files imported into the package <b><?php echo $row['name']; ?></b></p>
<p><a href="packagefiles.php?guid=<?php echo $guid; ?>">Click here to go back to the package files management</a></p>
</div>
</center>
<?php

} elseif ($_REQUEST['a'] == 'pattern') {

	$basedir = DIROF($_REQUEST['dest']);
	$count = 0;
	foreach (glob($basedir.$_REQUEST['pattern']) as $filename) {
		$count++;

		// Import file
		$sql->addRow('system_files', array(
			'type' => $_REQUEST['dest'],
			'package' => $pid,
			'filename' => $filename,
			'version' => 1,
			'hash' => md5_file($filename)
		));
	}

?>
<center>
<div class="centerblock" align="center">
<p><b><?php echo $count; ?></b> files imported into the package <b><?php echo $row['name']; ?></b></p>
<p><a href="packagefiles.php?guid=<?php echo $guid; ?>">Click here to go back to the package files management</a></p>
</div>
</center>
<?php

} else {
	// Clear selected file cache
	unset($_SESSION[TEMP]['checked_files']);

?>
<table width="100%">
<tr>
	<td width="35" rowspan="2" valign="top"><img src="../images/db_add32.gif" align="absmiddle" /></td>
	<td align="left"><font size="+2"><b> <?php echo $row['name']; ?></b></font></td>
</tr>
<tr>
	<td align="left">Import files on this package</td>
</tr>
<tr>
	<td colspan="2">
		
	<p>
	<fieldset><legend>Browse existing files</legend>
	<iframe style="width: 100%; height: 400px;" src="filebrowse.php" frameborder="0"></iframe>
	<input type="button" value="Import the selected files" onclick="window.location='?a=selimport&guid=<?php echo $guid; ?>';" />
	</fieldset>
	</p>
	
	<p>
	<fieldset><legend>Select files by matching pattern</legend>
	<form action="" method="post">
	<input type="hidden" name="a" value="pattern" />
	<table>
	<tr>
		<td><b>Match pattern:</b></td>
		<td><input type="text" name="pattern" /></td>
	</tr>
	<tr>
		<td><b>Search on:</b></td>
		<td>
		<select name="dest">
		<?php
		foreach ($_CONFIG[DIRS][NAMES] as $alias => $name) {
		echo "<option value=\"$alias\">$name</option>";
		}
		?>
		</select>
		</td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" value="Import files matching this pattern" /></td>
	</tr>
	</table>
	</form>
	</fieldset>
	</p>

	<p>
	<fieldset><legend>Upload from your machine</legend>
	<form action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="a" value="upload" />
	<table>
	<tr>
		<td><b>Filename:</b></td>
		<td><input type="file" name="file" /></td>
	</tr>
	<tr>
		<td><b>File type:</b></td>
		<td>
		<select name="dest">
		<?php
		foreach ($_CONFIG[DIRS][NAMES] as $alias => $name) {
		echo "<option value=\"$alias\">$name</option>";
		}
		?>
		</select>
		</td>
	</tr>
	<tr>
		<td><b>Subdirectory:</b></td>
		<td><input type="text" name="subdir" value="/" /></td>
	</tr>
	<tr>
		<td><input type="submit" value="Upload file" /></td>
	</tr>
	</table>
	</form>
	</fieldset>
	</p>

	</td>
</tr>
</table>
<br />
<input type="button" value="&lt;&lt; Back" onclick="window.location='packagefiles.php?guid=<?php echo $guid; ?>';" />
<?php
}
?>

</body>
</html>
