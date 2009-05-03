<?php
include "config/config.php";
include "engine/includes/base.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
td.stats {
	font-weight: bold;
	color: #336699;
}
-->
</style>
</head>
<body>
<table>
	<tr>
		<td><a href="">[Refresh Debug Window]</a></td>
	</tr>
	<tr>
		<td style="padding: 5px;"><b>Statistics</b></td>
	</tr>
	<tr>
		<td>
		<?php
		if (isset($_SESSION['stats'])) {
			echo "<table>";
			foreach ($_SESSION['stats'] as $name => $value) {
				echo "<tr><td class=\"stats\">$name</td><td>".htmlspecialchars($value)."</td></tr>\n";
			}
			echo "</table>";
		} else {
			echo "<em>(Stats not recorded yet)</em>";
		}
		?>
		</td>
	</tr>
	<tr>
		<td style="padding: 5px;"><b>Engine Errors</b></td>
	</tr>
	<tr>
		<td>
		<?php
		$ans = debug_render_errors();
		if ($ans=='') {
			echo '<em>(No errors occured)</em>';
		} else {
			echo $ans;
		}
		unset($_SESSION[DATA]['errors']);
		?>		
		</td>
	</tr>
</table>
</body>
</html>
