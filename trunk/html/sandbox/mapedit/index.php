<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>GloryLands Rendering System 2.0 :: Lightweight map Editor v0.2</title>
<script language="javascript" src="../../includes/mootools-release-1.11.js"></script>
<script language="javascript" src="editapi-1.0-src.js"></script>
<link rel="stylesheet" type="text/css" href="edit.css">
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
            <div id="spacer">&nbsp;</div>
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
					<div id="tiles_clear"><a href="javascript:;" onclick="ui_clear()"><img src="images/edit_remove.png" border="0" /></a></div>
					<div id="tiles_put" style="background-color:#FFFFFF;"><a href="javascript:;" onclick="ui_put()"><img src="images/edit_add.png" border="0" /></a></div>
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
<div class="border" id="ui_defobj" style="position: absolute; left: 110px; top: 60px; width: 522px; height: 440px; background-color:#FFFFFF; display: none;">
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
<div id="msg"></div>
</body>
</html>
