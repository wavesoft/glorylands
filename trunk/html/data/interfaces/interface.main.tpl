<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Test for Map Z-Buffer</title>
{literal}
<script language="javascript">
// PHP-Generated variables
var mapOfsX = 12;
var mapOfsY = 8;
</script>
{/literal}
<script language="javascript" src="includes/mootools-release-1.11.js"></script>
<script language="javascript" src="includes/glapi-1.0.src.js"></script>
{$javascript}
<link href="{$theme}/style.css" rel="stylesheet" type="text/css" />
{$stylesheet}
{literal}
<style>
.dragger {
	width:100%;
	background-color:#EFE;
	background-image: url(images/UI/infobox_repeat.gif);
	background-repeat: repeat-x;
	cursor: move;
	height: 23px;
	color: #FFCC00;
}
.dragger .left {
	background-image: url(images/UI/infobox_left.gif);
	height: 23px;
	width: 5px;
	background-repeat: no-repeat;
	background-position: left;
	position: absolute;
	top: 0px;
}
.dragger .center {
	position: absolute;
	top: 4px;
	left: 5px;
}
.container {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	background-image:url(images/UI/light2.jpg);
	color: #000000;
	width: 310px; 
	border:1px solid #333333;
	position: absolute;
	left: 60px;
	top: 60px;
	z-index:50;
}
.container .content {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	height: 100px;
	margin: 4px;
	padding: 4px;
}
.container .dispose {
	position: absolute;
	right: 0px;
	background-image: url(images/UI/infobox_close.gif);
	background-repeat: no-repeat;
	height: 23px;
	top: 0px;
	width: 23px;
}
.container .toggle {
	position: absolute;
	right: 23px;
	background-image: url(images/UI/infobox_minimize.gif);
	background-repeat: no-repeat;
	height: 23px;
	width: 28px;
	top: 0px;
}
</style>
{/literal}
</head>
<body>
<table id="waiter_host"><tr><td valign="middle" align="center"><div id="waiter">Loading Graphics</div></td></tr></table>
<!-- Hover info -->
<div id="hoverLayer" style="visibility: hidden;  z-index: 1998; background-image:url(images/UI/backblack.png);"></div>
<div id="dropdownLayer" style="visibility: hidden; z-index: 1999;"></div>
<!-- Dedicated Window -->
<div class="dd_blackie" id="dd_host" style="display: none">
<table class="dd_maxsize"><tr valign="middle"><td align="center">
<div class="dd_popup" id="dd_popup">
<div class="dd_maxsize" id="dd_content">
Please&nbsp; <img src="images/UI/loading2.gif" align="absmiddle" /> &nbsp;wait...
</div>
</div>
</td></tr></table>
</div>
<table width="100%">
 <tr>
  <td align="left">
	<table class="backload" cellspacing="0" cellpadding="0">
	<tr height="12">
		<td class="br1_lt" width="10">&nbsp;</td>
		<td class="br1_t"></td>
		<td class="br1_rt" width="10"></td>
	</tr>
	<tr>
		<td class="br1_l" width="10">&nbsp;</td>
		<td>
		<div style="position: relative; width: 768px; height: 512px; display: block; left:0px; top: 0px;" id="datapane">
		<!-- Datapane Contents -->
		</div>
		</td>
		<td class="br1_r" width="10"></td>
	</tr>
	<tr height="12">
		<td class="br1_lb" width="10">&nbsp;</td>
		<td class="br1_b"></td>
		<td class="br1_rb" width="10"></td>
	</tr>
	</table>
   </td>
   <td valign="top" align="center">
   <table>
	<tr>
		<td align="center"><img src="images/UI/gl_chaos.png" /></td>
	</tr>
	<tr>
		<td align="center">
		<a href="#" onclick="javascript:gloryIO('?a=interface.buildselect');" title="Place a building"><img border="0" src="images/UI/navbtn_repair.gif" /></a>
		<a href="#" onclick="javascript:gloryIO('?a=interface.inventory');" title="Open Inventory"><img border="0" src="images/UI/navbtn_explore.gif" /></a>
		<a href="#" onclick="javascript:gloryIO('?a=interface.ad.swapchar');" title="Change Characther"><img border="0" src="images/UI/navbtn_act.gif" /></a>
		</td>
	</tr>
	<tr>
		<td align="center">
		{$modules.2}
		</td>
	</tr>
   </table>
   </td>
 </tr>
</table>
<div style="" id="prompt"></div>
</body>
</html>
