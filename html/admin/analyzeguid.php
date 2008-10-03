<?php
include "../config/config.php"; 
include "../engine/includes/base.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Analyze Guid</title>
</head>

<body>
<?php
if (isset($_REQUEST['guid'])) {

	if (!gl_guid_valid($_REQUEST['guid'])) {
?>
<table border="1">
<tr>
	<th>GUID Given:</th>
	<td><b><?php echo $_REQUEST['guid']; ?></b></td>
</tr>
<tr>
	<td colspan="2"><font color="#FF0000">This GUID is not valid!</font></td>
</tr>
</table>
<?php
	} else {
		$parts = gl_analyze_guid($_REQUEST['guid']);
		
?>
<table border="1">
<tr>
	<th>GUID Given:</th>
	<td><b><?php echo $_REQUEST['guid']; ?></b></td>
</tr>
<tr>
	<th>Type:</th>
	<td><?php echo $parts['instance']?"<font color=\"green\">Instance</font>":"<font color=\"red\">Template</font>"; ?></td>
</tr>
<tr>
	<th>Group:</th>
	<td><?php echo $parts['group']; ?> (#<?php echo $parts['group_id']; ?>)</td>
</tr>
<?php
if ($parts['instance']) {

	$ans = $sql->query("SELECT `template` FROM `{$parts['group']}_instance` WHERE `index` = ".$parts['index']);
	if (!$ans) return false;
	if ($sql->emptyResults) return false;
	$row = $sql->fetch_array(MYSQL_NUM);	

?>
<tr>
	<th>Entry Index:</th>
	<td><?php echo $parts['index']; ?></td>
</tr>
<tr>
	<th>Template Index:</th>
	<td><?php echo $row[0]; ?></td>
</tr>
<tr>
	<th>Template GUID:</th>
	<td><b><?php echo gl_get_guid_template($_REQUEST['guid']); ?></b></td>
</tr>
<?php
} else {
?>
<tr>
	<th>Template Index:</th>
	<td><?php echo $parts['index']; ?></td>
</tr>
<?php
}
?>
</table>
<?php
	}
}
?>
<br />
<form action="" method="get">
<table>
<tr>
	<td>Enter a GUID to analyze:</td>
	<td><input name="guid" type="text" ></td>
</tr>
<tr>
	<td colspan="2"><input type="submit" value="Analyze" /></td>
</tr>
</table>
</form>
</body>
</html>
