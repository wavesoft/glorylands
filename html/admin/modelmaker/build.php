<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Untitled Document</title>
</head>

<body>
<?php
$data = stripslashes($_REQUEST['js']);
$data = json_decode($data,true);
?>
<p align="center">Object drawn:</p>
<table align="center" border="1" cellspacing="0" cellpadding="0">
<?php
foreach ($data as $y => $row) {
	echo "<tr>\n";
	foreach ($row as $x => $img) {
		echo "<td><img src=\"../../images/tiles/{$img}\"></td>";
	}
	echo "</tr>\n";
}
?>
</table>
<form action="save.php" method="post">
<p align="center">
<input type="hidden" name="data" value='<?php echo serialize($data) ?>' />
Select Name: <input name="objname" /><br />
<input type="submit" value="Create Element" />
</p>
</form>
</body>
</html>
