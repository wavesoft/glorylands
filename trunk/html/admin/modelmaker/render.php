<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<script src="../../includes/mootools-release-1.11.js" language="javascript"></script>
<title>Model Generator</title>
<style type="text/css">
<!--
table td {
	height: 32px;
	width: 32px;
}
table td img {
	height: 32px;
	width: 32px;
	display: block;
	text-decoration: none;
}
-->
</style>
</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th scope="col">
	<table border="1" cellspacing="0" cellpadding="0" bordercolor="#999999">
<?php
	for ($y=0; $y<12; $y++) {
		echo "<tr>";
		for ($x=0; $x<16; $x++) {
			echo "<td><img onclick=\"javascript:putTile(this,$x,$y)\" /></td>";
		}
		echo "</tr>\n";
	}
?>	
	</table>	</th>
    <td align="right" valign="top" scope="col">
	<table border="1">
	<tr><td align="center"><img id="cv_img" width="32" height="32" /></td></tr>
	<tr><td align="center"><img onclick="selectTile('')" style="cursor: pointer" src="../images/button_cancel.gif" /></td></tr>
	<tr><td>
	<input type="checkbox" id="af" /> <label for="af">AutoFeed</label>
	<input type="checkbox" id="an" /> <label for="an">AutoNewline</label>
	</td></tr>
	</table>	</td>
  </tr>
  <tr>
    <td headers="32" colspan="2" scope="col"><input type="button" value="Proceed to object generation >>" onclick="genObj()" /></td>
  </tr>
</table>
<script language="javascript">
var tile='';
var to=0;
var grid = new Object();
var xp, yp, bs;
function selectTile(elm) {
	if (elm!='') {
		$('cv_img').src = '../../images/tiles/'+elm;
		var ar = elm.split('-');
		bs = "";
		for (var i=0; i<ar.length-2; i++) {
			if (bs!='') bs+='-';
			bs+=ar[i];
		}
		xp = ar[ar.length-2];
		yp = String(ar[ar.length-1]).split('.');
		yp = yp[0];
	} else {
		$('cv_img').src = '';
	}
	tile=elm;
}
function putTile(e,x,y) {
	$(e).src = '../../images/tiles/'+tile
	if (!$defined(grid[y])) grid[y]=new Object();
	grid[y][x] = tile;
	if ($('af').checked) {		
		if (++xp>7) {xp=0; yp++;};
		selectTile(bs+'-'+xp+'-'+yp+'.gif');
	}
	if ($('an').checked) {		
		clearTimeout(to);
		to=setTimeout(newLine, 1500);
	}
}
function newLine() {
	yp++;
	xp=0;
	selectTile(bs+'-'+xp+'-'+yp+'.gif');	
}
function genObj() {
	window.location='build.php?js='+escape(Json.toString(grid));
}
</script>
</body>
</html>
