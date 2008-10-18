<?php
include "config/config.php";
include "engine/includes/base.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Debug Console</title>
<style>
<!--
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #333333;
}
div.debug_window {
	font-size: 12px; 
	border: dashed 2px #999999; 
	background-color:#F0F0F0; 
	color: #333333; 
	font-family: Arial, Helvetica, sans-serif;
}
div.debug_window div.head {
	font-size: 10px; 
	font-weight: bold; 
	color: #FFFFFF; 
	background-color:#666666
}
div.debug_window div.expand {
	height: 14px;
	overflow: hidden;
	color: #333333; 
	padding: 1px;
}
div.debug_window div.expand:hover {
	background-color: #FFFFFF;
	border: solid 1px #666666;
	padding: 0px;
	height: inherit;
	color: #333333; 
	overflow: visible;
}
-->
</style>
</head>
<body>
<p><a href="">[Refresh]</a> Engine Errors:</p>
<p>
<?php
$ans = debug_render_errors();
if ($ans=='') {
	echo '<em>(No errors occured)</em>';
} else {
	echo $ans;
}
unset($_SESSION[DATA]['errors']);
?>
</p>
</body>
</html>
