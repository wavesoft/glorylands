<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- 

  (C) Copyright 2007-2008, John Haralampidis - Wavesoft
  Website: http://www.wavesoft.gr/
 
  Licenced under GNU/GPL Licence
  
-->
<meta http-equiv="Content-Type" content="text/html; charset={$CONFIG.GAME.CHARSET}" />
<title>{$CONFIG.GAME.TITLE} v{$VERSION.VERSION}</title>
{literal}
<script language="javascript">
// PHP-Generated variables
var mapOfsX = 12;
var mapOfsY = 8;
</script>
{/literal}
<script language="javascript" src="includes/mootools-release-1.11.js"></script>
<!-- <script language="javascript" src="includes/glapi-1.1.src.js"></script> -->
<script language="javascript" src="includes/glapi-2.0.src.js"></script>
<script language="javascript" src="includes/popup.js"></script>
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
	margin: 0px;
	padding: 0px;
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

#datapane {
	position: absolute; 
	left:0px; 
	top: 0px; 
}

#dataloader {
	position: absolute; 
	left: 0px; 
	top: 0px; 
	z-index: 250000; 
	background-color: #000000; 
	width: 768px; 
	height: 512px; 
	background-image: url(images/loading.gif); 
	background-repeat: no-repeat; 
	background-position: center;
}
#dataloader div {
	top: 280px;
	position: absolute;
	color: #CCCCCC;
	width: 768px;
	text-align: center;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 12px;
}

#datahost {
	background-color: #000000;
	position: relative; 
	width: 768px; 
	height: 512px; 
	display: block; 
	left:0px; 
	top: 0px; 
	overflow: hidden;
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
 <tr height="536">
  <td align="left" width="788" valign="top">
	<table class="backload" cellspacing="0" cellpadding="0">
	<tr height="12">
		<td class="br1_lt" width="10">&nbsp;</td>
		<td class="br1_t"></td>
		<td class="br1_rt" width="10"></td>
	</tr>
	<tr>
		<td class="br1_l" width="10">&nbsp;</td>
		<td>
		<div id="datahost">
			<div id="datapane">
			<!-- Datapane Contents -->	
			<!-- <div style="border: solid 2px #FF0000; z-index: 1000000; text-align:center; width: 32px; height:32px; position:absolute;" id="zp">&nbsp;</div> -->
			</div>
			<div id="dataloader"><div id="dataloader_text"></div></div>
		</div>
		<div style="position: absolute; visibility: hidden; padding: 12px;" id="actionpane"></div>		
		</td>
		<td class="br1_r" width="10"></td>
	</tr>
	<tr height="12">
		<td class="br1_lb" width="10">&nbsp;</td>
		<td class="br1_b"></td>
		<td class="br1_rb" width="10"></td>
	</tr>
	</table>   </td>
   <td align="center" valign="top">
   <table width="100%" height="534" cellspacing="0" cellpadding="0">
	<tr>
		<td align="center" valign="top">
			<table class="backload" style="background: none #000000; height: 100%" width="100%" cellspacing="0" cellpadding="0">
			<tr height="12">
				<td class="br1_lt" width="10">&nbsp;</td>
				<td class="br1_t"></td>
				<td class="br1_rt" width="10"></td>
			</tr>
			<tr>
				<td class="br1_l" width="10">&nbsp;</td>
				<td>
				{$modules.0}				</td>
				<td class="br1_r" width="10"></td>
			</tr>
			<tr height="12">
				<td class="br1_lb" width="10">&nbsp;</td>
				<td class="br1_b"></td>
				<td class="br1_rb" width="10"></td>
			</tr>
			</table>		</td>
	</tr>
	<tr>
		<td>
			<table class="backload" style="background: none #000000; height: 100%" width="100%" cellspacing="0" cellpadding="0">
			<tr height="12">
				<td class="br1_lt" width="10">&nbsp;</td>
				<td class="br1_t"></td>
				<td class="br1_rt" width="10"></td>
			</tr>
			<tr>
				<td class="br1_l" width="10">&nbsp;</td>
				<td align="left">
				<a href="#" onclick="javascript:gloryIO('?a=admin.addobj');" title="Place a building"><img border="0" src="images/UI/navbtn_repair.gif" /></a>
				<a href="#" onclick="javascript:gloryIO('?a=interface.inventory');" title="Open Inventory"><img border="0" src="images/UI/navbtn_explore.gif" /></a>
				<a href="#" onclick="javascript:gloryIO('?a=interface.ad.swapchar');" title="Change Characther"><img border="0" src="images/UI/navbtn_act.gif" /></a>
				{$modules.1}				</td>
				<td class="br1_r" width="10"></td>
			</tr>
			<tr height="12">
				<td class="br1_lb" width="10">&nbsp;</td>
				<td class="br1_b"></td>
				<td class="br1_rb" width="10"></td>
			</tr>
			</table>		</td>
	</tr>
	<tr>
		<td align="center" valign="bottom">
			<table class="backload" style="background: none #000000; height: 100%" width="100%" cellspacing="0" cellpadding="0">
			<tr height="12">
				<td class="br1_lt" width="10">&nbsp;</td>
				<td class="br1_t"></td>
				<td class="br1_rt" width="10"></td>
			</tr>
			<tr>
				<td class="br1_l" width="10">&nbsp;</td>
				<td>
				{$modules.2}				</td>
				<td class="br1_r" width="10"></td>
			</tr>
			<tr height="12">
				<td class="br1_lb" width="10">&nbsp;</td>
				<td class="br1_b"></td>
				<td class="br1_rb" width="10"></td>
			</tr>
			</table>		</td>
	</tr>
   </table>   </td>
 </tr>
 
 <tr>
   <td colspan="2" align="center">{$modules.4}</td>
  </tr>
  <tr>
    <td colspan="2" align="center" class="footer"><small>Released under the GNU/GPL Licence. Author: John Haralampidis<br /><a href="javascript:popUpWindow('debug.php',700,300,true,true);">Debug Console</a></small></td>
  </tr>
</table>
<div style="" id="prompt"></div>
{$modules.5}
</body>
</html>
