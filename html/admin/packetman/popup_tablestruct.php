<?php 
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Insert table structure</title>
<link rel="stylesheet" type="text/css" href="res/stylemain.css">
</head>
<body>

<?php
if (isset($_REQUEST['table'])) {

	if ($_REQUEST['mode'] == 'drop') {
		$struct = "DROP TABLE `".$_REQUEST['table']."`";
	} else {
		$ans = $sql->query("SHOW CREATE TABLE `".$_REQUEST['table']."`");
		if (!$ans) die($sql->getError());
		$row = $sql->fetch_array_fromresults($ans, MYSQL_NUM);
		$struct = $row[1];
	}
?>
<center>
<div class="centerblock" align="center">
<p>The structure of table <b><?php echo $_REQUEST['table']; ?></b> is now entered on your script.</b></p>
<p><a href="javascript:window.close();">Click here to close the window</a></p>
</div>
</center>
<script language="javascript">
window.opener.document.forms[0].elements[7].value += '<?php echo str_replace("\n","\\n\\\n",addslashes($struct)); ?>;'+"\n\n";
window.close();
</script>
<?php
} else {
?>
<p>Please select the table to read the structure from:</p>
<table class="filetable" style="width: 100%;">
	<tr class="head">
		<td colspan="2">Available databases</td>
	</tr>
<?php
	$sql->query("SHOW TABLES");
	while ($row = $sql->fetch_array(MYSQL_NUM)) {
		$table = $row[0];
?>
	<tr>
		<td width="16"><img src="../images/db.gif" /></td>
		<td><a href="?table=<?php echo $table; ?>&mode=<?php echo $_REQUEST['mode']; ?>"><?php echo $table; ?></a></td>
	</tr>
<?php
	}
?>
</table>
<?php
}
?>
</body>
</html>
