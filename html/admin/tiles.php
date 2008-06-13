<?php ob_implicit_flush(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Chars Waitting for tiling</title>
<style type="text/css">
<!--
a.normal {
	background-image: url(images/button_cancel.gif);
	background-repeat: no-repeat;
	text-indent: 16px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #990000;
	padding-left: 16px;
}
a.done {
	background-image: url(images/ok.gif);
	background-repeat: no-repeat;
	text-indent: 16px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #CCCCCC;
	text-decoration: line-through;
	padding-left: 16px;
}
-->
</style>
</head>
<body>
<table width="100%">
<tr>
<?php

$info = array();
if (file_exists("../images/tiles/chars.ini")) {
	$info = parse_ini_file("../images/tiles/chars.ini");
}

$x=0; $y=0;
$d = dir("../images/chars");
while (false !== ($entry = $d->read())) {
	if (strtolower(substr($entry,-4))=='.png') {
		$cls = 'normal';
		if (isset($info[substr($entry,0,-4)])) $cls = 'done';
		echo "<td><a class=\"{$cls}\" href=\"tiles_process.php?char=".substr($entry,0,-4)."\"><img src=\"../images/chars/{$entry}\"></a></td>";
		$x++;
		if ($x>5) {
			$x=0;
			echo "\n</tr><tr>\n";
		}
	}
}
$d->close(); 
?>
</tr>
</table>
</body>
</html>
