<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>GloryLands Rendering System 2.0 :: Lightweight map Editor v0.2</title>
<script language="javascript" src="../../includes/mootools-release-1.11.js"></script>
<script language="javascript" src="editapi-1.0-src.js"></script>
<style>
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.border {
	padding: 5px;
	position: relative;
}
.border div.lt {
	background-image: url(images/brbl-lt.png);
	background-repeat: no-repeat;
	position: absolute;
	left: 0px;
	top: 0px;
	width: 5px;
	height: 5px;
}
.border div.rt {
	background-image: url(images/brbl-rt.png);
	background-repeat: no-repeat;
	position: absolute;
	top: 0px;
	right: 0px;
	width: 5px;
	height: 5px;
}
.border div.rb {
	background-image: url(images/brbl-rb.png);
	background-repeat: no-repeat;
	position: absolute;
	bottom: 0px;
	right: 0px;
	width: 5px;
	height: 5px;
}
.border div.lb {
	background-image: url(images/brbl-lb.png);
	background-repeat: no-repeat;
	position: absolute;
	bottom: 0px;
	left: 0px;
	width: 5px;
	height: 5px;
}
.border div.t {
	background-image: url(images/brbl-back.png);
	position: absolute;
	top: 0px;
	left: 5px;
	right: 5px;
	height: 5px;
}
.border div.b {
	background-image: url(images/brbl-back.png);
	position: absolute;
	bottom: 0px;
	left: 5px;
	right: 5px;
	height: 5px;
}
.border div.l {
	background-image: url(images/brbl-back.png);
	position: absolute;
	left: 0px;
	top: 5px;
	bottom: 5px;
	width: 5px;
}
.border div.r {
	background-image: url(images/brbl-back.png);
	position: absolute;
	right: 0px;
	top: 5px;
	bottom: 5px;
	width: 5px;
}
.border div.header {
	background-image: url(images/brbl-back.png);
	right: 0px;
	left: 0px;
	height: 14px;
	padding: 2px;
	font-size: 12px;
	font-weight: bold;
	color: #FFFFFF;
}
.border div.content {
	background-color: #E0E0E0;
	right: 0px;
	left: 0px;
	height: 24px;
	padding: 2px;
	font-size: 12px;
	color: #333333;
	vertical-align: middle;
}
.tiled_data {
	position: absolute;
	left: 0px;
	top: 0px;
}
.tiled_data img {
	position: absolute;
}
#content_data {
	position: absolute;
	left: 0px;
	top: 0px;
}
#content_data img {
	position: absolute;
}
.content_host {
	border-left: solid 1px #CCCCCC;
	border-top: solid 1px #CCCCCC;
	background-image:url(images/grid.gif);
	position: relative; 
	width: 768px; 
	height: 512px; 
	display: block; 
	left:0px; 
	top: 0px; 
	overflow: scroll;
	margin: 3px;
}
#tiles_host {
	width: 281px;
	overflow-y: auto;
	overflow-x: hidden;
	height: 200px;
	background-image:url(images/transparent.png);
	position: relative;
}
#tiles_host img {
	padding-left: 0px;
	padding-top: 0px;
	padding-right: 1px;
	padding-bottom: 1px;
	width: 32px;
	height: 32px;
	float: left;
	cursor: pointer;
}
#tile_select {
	width: 31px;
	height: 31px;
	border: solid 1px #FF0000;
	position:absolute;
	left:0px;
	top: 0px;
}
#pointer {
	width:32px;
	height: 32px;
	position: absolute;
	left: 0px;
	top: 0px;
	opacity: 0.5;
/*	filter: Alpha(opacity=50);*/
	background-color: #FFFFFF;
	z-index: 10000000;
}
.aniborder {
	position: absolute;
	padding: 1px;
	width: 32px;
	height: 32px;
}
.aniborder div.t {
	top:0px;
	left:1px;
	right:1px;
	height:1px;
	background-image:url(images/ani.gif);
	background-position: top;
}
.aniborder div.b {
	bottom:0px;
	left:1px;
	right:1px;
	height:1px;
	background-image:url(images/ani.gif);
	background-position: bottom;
}
.aniborder div.l {
	top:0px;
	bottom:0px;
	left:1px;
	width:1px;
	background-image:url(images/ani.gif);
	background-position: left;
}
.aniborder div.r {
	top:0px;
	bottom:0px;
	right:1px;
	width:1px;
	background-image:url(images/ani.gif);
	background-position: right;
}

.dropdownmenu {
	border: solid 1px #6694E3;
	padding: 0px;
	position: absolute;
	background-color: #FFFFFF;
}

.dropdownmenu div {
	
}

.dropdownmenu a {
	display: block;
	left: 0px;
	right: 0px;
	color: #000000;
	text-decoration: none;
	padding: 2px;
}

.dropdownmenu a:hover {
	display: block;
	left: 0px;
	right: 0px;
	background-color: #6694E3;
	color: #FFFFFF;
}

.navmenu {
	height: 50px;
	padding: 2px;
}

.navmenu a {
	display: block;
	float: left;
	width: 42px;
	height: 40px;
	margin: 2px;
	border: solid 1px #E8E8E8;
	color: #000000;
	text-align: center;
	vertical-align: middle;
	text-decoration: none;
	font-size: 10px;
	padding-top: 4px;
}

.navmenu a:hover {
	background-color: #C4E2FB;
	border-color: #8080FF;
	color: #FFFFFF;
}

.topmenu {
	height: 24px;
}

.topmenu a {
	display: block;
	float: left;
	margin: 2px;
	border: solid 1px #E8E8E8;
	color: #000000;
	text-align: center;
	vertical-align: middle;
	text-decoration: none;
	font-size: 10px;
	padding: 2px;
}

.topmenu a:hover {
	background-color: #C4E2FB;
	border-color: #8080FF;
	color: #FFFFFF;
}

.sidemenu {
	padding: 5px;
	position: absolute;
	width: 25px;
	right: -30px;
	top: 10px;
	color: #FFFFFF;
}
.sidemenu div.rt {
	background-image: url(images/brbl-rt.png);
	background-repeat: no-repeat;
	position: absolute;
	top: 0px;
	right: 0px;
	width: 5px;
	height: 5px;
}
.sidemenu div.rb {
	background-image: url(images/brbl-rb.png);
	background-repeat: no-repeat;
	position: absolute;
	bottom: 0px;
	right: 0px;
	width: 5px;
	height: 5px;
}
.sidemenu div.t {
	background-image: url(images/brbl-back.png);
	position: absolute;
	top: 0px;
	left: 0px;
	right: 5px;
	height: 5px;
}
.sidemenu div.b {
	background-image: url(images/brbl-back.png);
	position: absolute;
	bottom: 0px;
	left: 0px;
	right: 5px;
	height: 5px;
}
.sidemenu div.r {
	background-image: url(images/brbl-back.png);
	position: absolute;
	right: 0px;
	top: 5px;
	bottom: 5px;
	width: 5px;
}
.sidemenu div.content {
	background-image: url(images/brbl-back.png);
}
.sidemenu div.content div {
	width: 20px;
	padding: 2px;
}

.progress_bar {
	border: solid 1px #999999;
	background-color: #E8E8E8;
	display: block;
	height: 6px;
	padding: 2px;
}

.progress_bar div {
	background-color: #333399;
	font-size: 9px;
	height: 6px;
}

.message {
	background-color: #FFFF99;
	border: solid 1px #FFCC33;
	padding: 4px;
	color: #666666;
	text-align: center;
	visibility: hidden;
}

</style>
</head>
<body>
<table>
	<tr>
	<td width="782" valign="top">
	<div class="border">
		<div class="lt"></div><div class="rt"></div><div class="lb"></div><div class="rb"></div>
		<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
		<div class="header">Map Grid</div>
		<div id="content_host" class="content_host">
			<div id="content_layer1" class="tiled_data">&nbsp;</div>
			<div id="content_layer2" class="tiled_data">&nbsp;</div>
			<div id="content_layer3" class="tiled_data">&nbsp;</div>
			<div id="content_objects" class="tiled_data">&nbsp;</div>
			<div id="content_data">
				<div class="aniborder" id="content_selection" style="visibility: hidden">
					<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
				</div>
			</div>
		</div>
	</div>
	</td>
	<td valign="top">
	<div class="border">
		<div class="lt"></div><div class="rt"></div><div class="lb"></div><div class="rb"></div>
		<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
		<div class="header">Menu</div>
		<div class="navmenu">
			<a href="javascript:;" onclick="ui_new();"><img src="images/filenew.png" border="0" /><br /><u>N</u>ew</a>
			<a href="javascript:;" onclick="ui_save();"><img src="images/filesave.png" border="0" /><br /><u>S</u>ave</a>
			<a href="javascript:;" onclick="ui_load();"><img src="images/fileopen.png" border="0" /><br /><u>O</u>pen</a>
			<a href="javascript:;" onclick="ui_objects();"><img src="images/packet.png" border="0" /><br />O<u>b</u>jects</a>
		</div>
		<div class="message" id="json_output"></div>
	</div>
	<br />
	<div class="border">
		<div class="lt"></div><div class="rt"></div><div class="lb"></div><div class="rb"></div>
		<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
		<div class="header">Graphics</div>
		<div class="content">
			<span id="tiles_status"></span>
			<select onchange="tloader_download(this.value)" style="width: 100%;" id="tiles_set">
			<option value="z-field-ext">z-field-ext</option>
			<option value="z-castle-ext">z-castle-ext</option>
			<option value="z-castle-int">z-castle-int</option>
			</select>
		</div>
		<div id="tiles_host">
			<div class="aniborder" id="tiles_select">
				<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
			</div>
		</div>
		<div id="tiles_sidemenu">
			<div class="sidemenu">
				<div class="rt"></div><div class="rb"></div><div class="t"></div><div class="b"></div><div class="r"></div>
				<div class="content" style="height: 40px;">
					<div><a href="#"><img src="images/edit_remove.png" border="0" /></a></div>
					<div style="background-color:#FFFFFF;"><a href="#"><img src="images/edit_add.png" border="0" /></a></div>
				</div>
			</div>
		</div>
	</div>
	<br />
	<div class="border">
		<div class="lt"></div><div class="rt"></div><div class="lb"></div><div class="rb"></div>
		<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
		<div class="header">Objects</div>
		<div class="content">
			<span id="objects_status"></span>
			<select onchange="objloader_download(this.value)" style="width: 100%;" id="objects_set">
			<option value="furniture">furniture</option>
			</select>
		</div>
		<div id="objects_host">

		</div>
	</div>
	</td>
	</tr>
</table>
<div class="border" id="ui_defobj" style="position: absolute; left: 110px; top: 60px; width: 522px; height: 440px; background-color:#FFFFFF;">
	<div class="lt"></div><div class="rt"></div><div class="lb"></div><div class="rb"></div>
	<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
	<div class="header">Define Object</div>
	<div id="content_host" style="width: 512px; height: 384px; overflow: hidden;" class="content_host">
		<div id="object_layer1" class="tiled_data">&nbsp;</div>
		<div id="object_layer2" class="tiled_data">&nbsp;</div>
		<div id="object_layer3" class="tiled_data">&nbsp;</div>
		<div class="content" style="height: 32px;">
			<div class="topmenu">
				<a href="javascript:;" onclick="ui_objects();"><img src="images/packet.png" border="0" align="absmiddle" /> O<u>b</u>jects</a>
				<a href="javascript:;" onclick="ui_objects();"><img src="images/packet.png" border="0" align="absmiddle" /> O<u>b</u>jects</a>
				<a href="javascript:;" onclick="ui_objects();"><img src="images/packet.png" border="0" align="absmiddle" /> O<u>b</u>jects</a>
			</div>			
		</div>
		<div id="object_data">
			<div class="aniborder" id="content_selection" style="visibility: hidden">
				<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
