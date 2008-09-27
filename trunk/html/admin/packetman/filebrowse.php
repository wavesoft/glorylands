<?php 
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Browse files</title>
<link rel="stylesheet" type="text/css" href="res/stylemain.css">
<script language="javascript">
function changeDir(dir) {
	document.forms[0].path.value = dir;
	document.forms[0].submit();
}
function submitInfo() {
	document.forms[0].submit();
}
window.onblur = submitInfo;
</script>
</head>

<body>
<?php //echo "<pre>".print_r($_REQUEST,true)."</pre>"; ?>
<form action="" method="post">
<?php

	// Helper functions
	function fixSize($sz) {
		if ($sz>1024) {
			$sz = ($sz/1024);
			if ($sz > 1024) {
				$sz = ($sz/1024);
				if ($sz > 1024) {
					$sz = ($sz/1024);
					return number_format($sz, 2)." Gb";
				} else {
					return number_format($sz, 2)." Mb";
				}
			} else {
				return number_format($sz, 2)." Kb";
			}
		} else {
			return "$sz b";
		}
	}
	
	// Find out relative and root paths
	global $_CONFIG;
	$bp = $_CONFIG[GAME][BASE];
	$prevpath = '';
	if (isset($_REQUEST['path'])) {
		$rp = $_REQUEST['path'];
		$rp = str_replace('../','',$rp);
		if (substr($rp,-1)=='/') $rp=substr($rp,0,-1);
		if (substr($rp,0,1)=='/') $rp=substr($rp,1);
		$bp = $bp.'/'.$rp;
		
		$f = explode('/', $rp);
		unset($f[sizeof($f)-1]);
		$prevpath = implode('/', $f);
	}
	
	// Update selected file cache
	if (!isset($_SESSION[TEMP]['checked_files'])) $_SESSION[TEMP]['checked_files'] = array();
	if (isset($_REQUEST['ref_path'])) {
		$path = $_REQUEST['ref_path'];
		$_SESSION[TEMP]['checked_files'][$path] = $_REQUEST['file'];
	}

?>
<input type="hidden" name="path" value="<?php echo $rp ?>" />
<input type="hidden" name="ref_path" value="<?php echo $rp ?>" />
<table width="100%" class="filetable" cellpadding="1" cellspacing="1">
<tr class="head">
	<td width="15" align="center">&nbsp;</td>
	<td width="15" align="center">&nbsp;</td>
	<td>Filename</td>
	<td width="50">Size</td>
</tr>
<?php	
	echo "<tr class=\"folder\"><td colspan=\"4\">&nbsp;$bp</td></tr>\n";

	if ($rp!='') {
?>
<tr>
	<td width="15" align="center">&nbsp;</td>
	<td width="15" align="center"><img src="../images/up.gif" /></td>
	<td><a href="javascript:changeDir('<?php echo $prevpath; ?>');">Up one level</a></td>
	<td width="50">&nbsp;</td>
</tr>
<?php
	}
	
	$d = dir($bp);
	$elmid = 0;
	
	// Traverse directory on the specified relative path
	while (false !== ($entry = $d->read())) {
		if (substr($entry,0,1)!='.') {
			$elmid++;
			$file = $bp.'/'.$entry;
			
			// Check if the item is checked
			$checked="";
			if (isset($_SESSION[TEMP]['checked_files'][$rp][$entry])) {
				$checked='checked="checked"';
			}
			
			// Check if the item is directory
			if (is_dir($file)) {
?>
<tr>
	<td width="15" align="center"><input <?php echo $checked; ?> id="e<?php echo $elmid; ?>" type="checkbox" name="file[<?php echo $entry; ?>]" /></td>
	<td width="15" align="center"><img src="../images/folder_yellow.gif" /></td>
	<td><a href="javascript:changeDir('<?php echo $rp.'/'.$entry ?>');"><?php echo $entry; ?></a></td>
	<td width="50"><label for="e<?php echo $elmid; ?>">---</label></td>
</tr>
<?php			
			} else {
			
				// Check if the item is used by another package
				$class = '';
				$title = '';
				if ($sql->poll("SELECT `index` FROM `system_files` WHERE `filename` = '".mysql_escape_string($file)."'")) {
					$class='class="disabled"';
					$title='title="Used by another package"';
				}
?>
<tr <?php echo $class; ?>>
	<td width="15" align="center"><input <?php echo $checked; ?> id="e<?php echo $elmid; ?>" type="checkbox" name="file[<?php echo $entry; ?>]" /></td>
	<td width="15" align="center"><img src="../images/file.gif" /></td>
	<td><label <?php echo $title; ?> for="e<?php echo $elmid; ?>"><?php echo $entry; ?></label></td>
	<td width="50"><label <?php echo $title; ?> for="e<?php echo $elmid; ?>"><?php echo fixSize(filesize($file)); ?></label></td>
</tr>
<?php			
			}
		}
	}
	$d->close();
?>
</table>
</form>
</body>
</html>
