<?php 
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Package Files</title>
<link rel="stylesheet" type="text/css" href="res/stylemain.css">
<script language="javascript">
function doAction(action, confirmMsg) {
	if (!(!confirmMsg)) {
		if (!window.confirm(confirmMsg)) return;
	}
	document.forms[0].a.value = action;
	document.forms[0].submit();
}
</script>
</head>

<body>
<?php

if ($_POST['a'] == 'delete') {
	?>
	<center>
	<div class="centerblock" align="center">
	<?php
	$count=0;
	foreach ($_POST['files'] as $index => $ack) {
		$filename = $sql->query_and_get_value("SELECT `filename` FROM `system_files` WHERE `index` = $index");
		//unlink($filename);
		//$sql->query("DELETE FROM `system_files` WHERE `index` = $index");
		$count++;
	}
	?>
	<p><b><?php echo $count; ?></b> files were deleted and removed from database</p>
	<p style="color: #FF0000;"><em>(For debugging protection, no erase action is taken)</em></p>
	</div>
	</center>
	<br />
	<?php
	
} elseif ($_POST['a'] == 'exclude') {
	?>
	<center>
	<div class="centerblock" align="center">
	<?php
	$count=0;
	foreach ($_POST['files'] as $index => $ack) {
		$sql->query("DELETE FROM `system_files` WHERE `index` = $index");
		$count++;
	}
	?>
	<p><b><?php echo $count; ?></b> files were excluded from the package</p>
	</div>
	</center>
	<br />
	<?php
} else

?>
<form action="" method="post">
<input type="hidden" name="a" value="" />
<table width="100%" class="filetable" cellpadding="1" cellspacing="1">
<tr class="head">
	<td width="15" align="center"><img src="../images/bin.gif" /></td>
	<td>Filename</td>
	<td width="50">Check</td>
	<td width="50">Size</td>
</tr>
<?php

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

// Find package ID By GUID
$ans = $sql->query("SELECT `index` FROM `system_packages` WHERE `guid` = '{$_REQUEST['guid']}'");
if (!$ans) die($sql->getError());
if ($sql->emptyResults) die("Cannot find package with guid {$_REQUEST['guid']}");
$pid = $sql->fetch_array(MYSQL_NUM);
$pid = $pid[0];

// Find and group the files
$ans = $sql->query("SELECT * FROM `system_files` WHERE `package` = {$pid}");
if (!$ans) die($sql->getError());

$groups = array();
$sz_total = 0;
$num_total = 0;

while ($row = $sql->fetch_array_fromresults($ans, MYSQL_ASSOC)) {
	$num_total++;
	$ar = $groups[$row['type']];
	if (!$ar) $ar = array();

	$fsz = false;
	if (is_file($row['filename'])) {
		$fsz = filesize($row['filename']);
		$sz_total += $fsz;
		$fsz = fixSize($fsz);
	}
	$chk = false;
	if (md5_file($row['filename']) == $row['hash']) $chk = true;
	
	$prefix = DIROF($row['type']);
	$file = array(str_replace($prefix, '', $row['filename']), $row['index'], $fsz, $chk);
	array_push($ar, $file);
	
	$groups[$row['type']] = $ar;
}

foreach ($groups as $group => $files) {
	$groupdesc = DESCOF($group);
	echo "<tr class=\"folder\"><td colspan=\"4\">$groupdesc <em>($group)</em></td></tr>\n";
	foreach ($files as $file) {
		if ($file[2] === false) {
			echo "<tr><td style=\"background-color: #FF3333\" width=\"15\"><input type=\"checkbox\" title=\"Delete\" id=\"f{$file[1]}\" name=\"remove[{$file[1]}]\"></td><td><label for=\"f{$file[1]}\">{$file[0]}</label></td><td align=\"center\"><em>(Missing)</em></td><td align=\"center\"><em>(Missing)</em></td></tr>\n";
		} else {
			$ccol = "#FFFF33";
			$check = "<img src=\"../images/critical.gif\" title=\"File changed since the install\">";
			if ($file[3]) {
				$check = "<img src=\"../images/ok.gif\" title=\"Hash check OK\">";
				$ccol = "#66FF66";
			}
			echo "<tr><td style=\"background-color: {$ccol}\" width=\"15\"><input type=\"checkbox\" title=\"Delete\" id=\"f{$file[1]}\" name=\"files[{$file[1]}]\"></td><td><label for=\"f{$file[1]}\">{$file[0]}</label></td><td align=\"center\">{$check}</td><td>{$file[2]}</td></tr>\n";
		}
	}
}

if ($num_total == 0) {
echo "<tr><td colspan=\"5\"><em>(No files found)</em></td>\n";
}

$sz_total = fixSize($sz_total);
echo "<tr class=\"folder\"><td colspan=\"2\">Total Size:</td><td align=\"center\" colspan=\"2\">$sz_total</td></tr>\n";
echo "<tr class=\"folder\"><td colspan=\"2\">Total Files:</td><td align=\"center\" colspan=\"2\">$num_total</td></tr>\n";

//echo "<pre>".print_r($groups,true)."</pre>";

?>
</table>
</form>
<div class="navbar">
<a href="#" onclick="doAction('delete','Warning! This action is not undoable! The selected files are going to be removed permanately!');"><img src="../images/trash22.gif" border="0" align="absmiddle" /> Delete Selected Files</a>
<a href="#" onclick="doAction('exclude','');"><img src="../images/db_remove.gif" border="0" align="absmiddle" /> Exclude Selected Files</a>
<a href="#"><img src="../images/db_comit.gif" border="0" align="absmiddle" /> Update file hashes</a>
<a href="packagefiles_import.php?guid=<?php echo $_REQUEST['guid']; ?>"><img src="../images/db_add.gif" border="0" align="absmiddle" /> Import Files</a>
</div>
</body>
</html>
