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
<script language="javascript" src="includes/mootools.js"></script>
<script language="javascript" src="includes/glapi.js"></script>
<script language="javascript" src="includes/popup.js"></script>
<script language="javascript" src="includes/merchant-functions.js"></script>
<script language="javascript" src="includes/battleapi.js"></script>
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
	width: 100%; 
	height: 512px; 
	background-image: url(images/loading.gif); 
	background-repeat: no-repeat; 
	background-position: center;
}
#dataloader div {
	top: 280px;
	position: absolute;
	color: #CCCCCC;
	width: 100%;
	text-align: center;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 12px;
}

#datahost {
	background-color: #000000;
	position: relative; 
	width: 100%; 
	height: 512px; 
	display: block; 
	left:0px; 
	top: 0px; 
	overflow: hidden;
}

.lower_bar {
	position: absolute;
	top: 465px;
	z-index: 250002;
	text-align: center;
}

.right_bar {
	position: absolute;
	right: 20px;
	top: 20px;
	z-index: 250003;
	background-color: #000000;
	padding: 5px;
}

.right_bar .floater {
	float: left;
}

.right_bar .contenthost {
	max-width: 300px;
	float: left;
	border: solid 1px #999999;
}

.right_bar .tabhost {
	float:left;
	width: 18px;
	padding: 1px;
	margin: 2px;
}

.right_bar .tabhost a {
	position: relative;
	text-decoration: none;
	display: block;
	width: 16px;
	height: 16px;
	color: #CCCCCC;
	border-top: solid 1px #999999;
	border-right: solid 1px #999999;
	border-bottom: solid 1px #999999;
	border-left: solid 1px #999999;
	margin-top: 2px;
	left: -1px;
}

.right_bar .tabhost a.active {
	left: -4px;
	border-left: solid 1px #000000;
}

.right_bar .tabhost a:hover {
	text-decoration: none;
	display: block;
	background-color:#CCCCCC;
	color: #333333;
}

</style>
{/literal}
</head>
<body>
<table id="waiter_host"><tr><td valign="middle" align="center"><div id="waiter">{#LOADING_GRAPHICS#}</div></td></tr></table>
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

<div id="datahost">
	<div id="datapane">
	<!-- GRID Contents -->	
	<!-- <div style="border: solid 2px #FF0000; z-index: 1000000; text-align:center; width: 32px; height:32px; position:absolute;" id="zp">&nbsp;</div> -->
	</div>
	<div id="databuffer">
	<!-- MAIN Data buffer contents -->
	</div>
	<div id="dataloader"><div id="dataloader_text"></div></div>
</div>
<div class="lower_bar" align="center">
	{$modules.201}
</div>
<div class="right_bar" align="center">
	<div class="contenthost">
		<div>
			{$modules.100}
		</div>
		<div id="tb1">			
			{$modules.101}
		</div>
		<div id="tb2">			
			{$modules.102}
		</div>
		<div id="tb3">			
			{$modules.103}		
		</div>
		<div id="tb4">			
			{$modules.104}		
		</div>
		<div>
			{$modules.199}		
		</div>
	</div>
	<div class="tabhost">
		<a id="tl1" href="javascript:;" onclick="iface_selecttab(1)" title="Statistics"><img border="0" src="images/UI/navmenu/stats.png" /></a>
		<a id="tl2" href="javascript:;" onclick="iface_selecttab(2)" title="Player Inventory"><img border="0" src="images/UI/navmenu/inventory.png" /></a>
		<a id="tl3" href="javascript:;" onclick="iface_selecttab(3)" title="Chat and messages"><img border="0" src="images/UI/navmenu/chat.png" /></a>
		<a id="tl4" href="javascript:;" onclick="iface_selecttab(4)" title="Main Menu"><img border="0" src="images/UI/navmenu/settings.png" /></a>
	</div>
</div>
<div style="position: absolute; visibility: hidden; padding: 12px;" id="actionpane"></div>		
<p align="center" class="footer"><small>Released under the GNU/GPL Licence. Author: John Haralampidis<br /><a href="javascript:popUpWindow('debug.php',700,300,true,true);">Debug Console</a></small></p>
<div style="font-size: 10px" id="prompt"></div>
{$modules.500}
</body>
</html>
