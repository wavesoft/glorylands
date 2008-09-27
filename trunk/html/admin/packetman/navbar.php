<?php 
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<link rel="stylesheet" type="text/css" href="res/stylenav.css">
<script src="res/custom-form-elements.js"></script>
</head>
<body>
<table width="100%" align="center">
<tr>
	<td align="center">
	<font size="+2"><b>GloryLands</b></font><br />
	Advanced Package Manager
	</td>
</tr>
<tr>
	<td align="center">
	<form method="get" target="main" action="package.php">
	<select name="guid" onchange="this.form.submit();">
	<?php
	
	$ans = $sql->query("SELECT * FROM `system_packages`");
	while ($row = $sql->fetch_array_fromresults($ans)) {
		echo "<option value=\"{$row['guid']}\">{$row['name']}</option>\n";
	}
	?>
	</select>
	</form>
	</td>
</tr>
<tr height="50">
	<td align="left" valign="middle">
	<a href="package_add.php" target="main" title="Create new package"><img src="../images/edit_add.gif" border="0"  align="absmiddle" /> Create New</a><br />
	<a href="package_upload.php" target="main" title="Upload Package"><img src="../images/agt_update_drivers.gif" border="0"  align="absmiddle" /> Install package</a><br />
	</td>
</tr>
<tr>
	<td><hr /></td>
</tr>
</table>
</body>
</html>
