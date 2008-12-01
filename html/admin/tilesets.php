<?php ob_implicit_flush(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>GloryLands Tilesets management</title>
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
a.cache {
	background-image: url(images/agt_aisles.gif);
	background-repeat: no-repeat;
	text-indent: 16px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #FF9900;
	padding-left: 16px;
}
-->
</style>
</head>

<body>
<?php

$bp = 'Z:\(Downloads)\[UNSORTED]\== Glory Lands ==\2D Tilesets\old';

$d = dir($bp);
while (false !== ($entry = $d->read())) {
	if (strtolower(substr($entry,-4)) == '.png') {
		$f = substr($entry,0,-4);
		$tp = 'normal';
		if (file_exists("cache/{$f}-0-0.png")) $tp = 'cache';
		//if (file_exists("../images/tiles/{$f}-0-0.gif")) $tp = 'done';
		if (file_exists("cache/done/{$f}-0-0.png")) $tp = 'done';
		echo "File: <b><a class=\"{$tp}\" href=\"tilesets_process.php?f=$entry\">$entry</a></b><br>\n";
	}
}
$d->close();

?>
</body>
</html>
