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
#content {
	position: absolute;
}
#content img {
	position: absolute;
}
#content_host {
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
	width: 280px;
	overflow: scroll;
	height: 460px;
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
	filter: Alpha(opacity=50);
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
}
.aniborder div.b {
	bottom:0px;
	left:1px;
	right:1px;
	height:1px;
	background-image:url(images/ani.gif);
}
.aniborder div.l {
	top:0px;
	bottom:0px;
	left:1px;
	width:1px;
	background-image:url(images/ani.gif);
}
.aniborder div.r {
	top:0px;
	bottom:0px;
	right:1px;
	width:1px;
	background-image:url(images/ani.gif);
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
		<div id="content_host">
			<img src="" id="pointer" />
			<div id="content">&nbsp;</div>
		</div>
	</div>
	</td>
	<td valign="top">
	<div class="border">
		<div class="lt"></div><div class="rt"></div><div class="lb"></div><div class="rb"></div>
		<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
		<div class="header">Menu</div>
		Here is some text
	</div>
	<br />
	<div class="border">
		<div class="lt"></div><div class="rt"></div><div class="lb"></div><div class="rb"></div>
		<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
		<div class="header">Graphics</div>
		<div id="tiles_host">
			<div class="aniborder">
				<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
			</div>
		</div>
	</div>
	</td>
	</tr>
</table>
</body>
</html>
