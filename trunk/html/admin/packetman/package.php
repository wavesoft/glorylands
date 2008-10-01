<?php 
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Package Overview</title>
<link rel="stylesheet" type="text/css" href="res/stylemain.css">
</head>
<body>
<?php
if (!isset($_GET['guid'])) {
	die("No package specified");
}

function getStatus($rstatus) {
	$icon = "ok.gif";
	$state = "state_active";
	$status = 'Active';
	
	switch ($rstatus) {
	
		case "INACTIVE":	$icon  = 'button_cancel.gif';
							$state = 'state_inactive';
							$status = 'Inactive';
							break;
		
		case "INCOMPLETED":	$icon  = 'package.gif';
							$state = 'state_incomplete';
							$status = 'Incompleted!';
							break;

		case "UNINSTALLINIG":$icon  = 'bin.gif';
							$state = 'state_uninstalling';
							$status = 'Uninstalling';
							break;

		case "BUGGY":		$icon  = 'button_cancel.gif';
							$state = 'state_error';
							$status = 'Buggy!';
							break;

	}
	
	return "<span class=\"$state\"><img src=\"../images/$icon\" align=\"absmiddle\" /> $status</span>";
}

$ans = $sql->query("SELECT * FROM `system_packages` WHERE `guid` = '{$_GET['guid']}'");
if (!$ans) die($sql->getError());
if ($sql->emptyResults) die("Cannot find package with guid {$_GET['guid']}");

$row = $sql->fetch_array();

?>
<table>
<tr>
	<td width="35" rowspan="2" valign="top"><img src="../images/folder_tar.gif" align="absmiddle" /></td>
	<td align="left"><font size="+2"><b> <?php echo $row['name']; ?></b> <i>(#<?php echo $row['index']; ?>)</i></font></td>
</tr>
<tr>
	<td align="left"><?php echo $row['description'] ?></td>
</tr>
<tr>
	<td colspan="2">
	<table>
	<tr>
		<td><b>Installed On: </b></td>
		<td width="200"><?php echo date("d/m/Y h:i:s", $row['installdate']); ?></td>
		<td><b>Current status: </b></td>
		<td width="200"><?php echo getStatus($row['status']); ?></td>
	</tr>
	<tr>
		<td><b>Author: </b></td>
		<td><?php echo $row['author']; ?></td>
		<td><b>Copyright: </b></td>
		<td><?php echo $row['copyright']; ?></td>
	</tr>
	<tr>
		<td colspan="4"><b>Requires packages: </b></td>
	</tr>
	<tr>
		<td colspan="4">
		<?php 
		
		if (($row['require'] == '') || ($row['require'] == 'a:0:{}')) {
			echo "<em>(None)</em>\n";
		} else {
			echo "<ul class=\"dependencies\">";
			
			$data = unserialize($row['require']);
			foreach ($data as $depend) {
				$guid = $depend['guid'];				
				$sql->query("SELECT `name` FROM `system_packages` WHERE `guid` = '$guid'");
				if ($sql->emptyResults) {
					echo "<li><em>(Missing package <b>{$depend['name']}</b>)</em></li>";
				} else {
					$info = $sql->fetch_array(MYSQL_NUM);
					echo "<li><a href=\"package.php?guid=$guid\">{$info[0]}</a></li>";
				}
			}
			echo "</ul>";
		}
		
		?>
		</td>
	</tr>
	<tr height="32">
		<td colspan="4">&nbsp;</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<div class="navbar">
<?php 
if ($row['status'] == 'INACTIVE') {
?>
<a href="package_enable.php?guid=<?php echo $_GET['guid']; ?>"><img src="../images/ok22.gif" border="0" align="absmiddle" /> Enable this package</a>
<a href="package_delete.php?guid=<?php echo $_GET['guid']; ?>"><img src="../images/trash22.gif" border="0" align="absmiddle" /> Delete Package</a>
<?php 
} else {
?>
<a href="packagefiles.php?guid=<?php echo $_GET['guid']; ?>"><img src="../images/folderdoc22.gif" border="0" align="absmiddle" /> Manage Files</a>
<a href="packagemanifest.php?guid=<?php echo $_GET['guid']; ?>"><img src="../images/exec22.gif" border="0" align="absmiddle" /> Manage Manifest</a>
<a href="package_disable.php?guid=<?php echo $_GET['guid']; ?>"><img src="../images/cancel22.gif" border="0" align="absmiddle" /> Disable this package</a>
<a href="package_pack.php?guid=<?php echo $_GET['guid']; ?>"><img src="../images/save22.gif" border="0" align="absmiddle" /> Pack and download</a>
<a href="package_delete.php?guid=<?php echo $_GET['guid']; ?>"><img src="../images/trash22.gif" border="0" align="absmiddle" /> Delete Package</a>
<?php 
}
?>
</div>
</body>
</html>
