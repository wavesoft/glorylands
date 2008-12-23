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
			<div id="content_collision" class="tiled_data">&nbsp;</div>
			<div id="content_data" style="z-index:10000">
				<div class="aniborder" id="content_selection" style="visibility: hidden">
					<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
				</div>
			</div>
            <div id="spacer">&nbsp;</div>
		</div>
	</div>
	</td>
	<td valign="top" width="295">
	<div class="border">
		<div class="lt"></div><div class="rt"></div><div class="lb"></div><div class="rb"></div>
		<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
		<div class="header">Menu</div>
		<div class="navmenu">
			<a href="javascript:;" onclick="ui_new();" accesskey="n"><img src="images/filenew.png" border="0" /><br /><u>N</u>ew</a>
			<a href="javascript:;" onclick="ui_save();" accesskey="s"><img src="images/filesave.png" border="0" /><br /><u>S</u>ave</a>
			<a href="javascript:;" onclick="ui_load();" accesskey="o"><img src="images/fileopen.png" border="0" /><br /><u>O</u>pen</a>
			<a href="javascript:;" onclick="ui_compile();" accesskey="c"><img src="images/packet.png" border="0" /><br /><u>C</u>ompile</a>
		</div>
		<div class="message" id="json_output">&nbsp;</div>
	</div>
	<div class="border">
		<div class="lt"></div><div class="rt"></div><div class="lb"></div><div class="rb"></div>
		<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
		<div class="header">Graphics</div>
		<div class="content">
			<span id="tiles_status"></span>
			<select onchange="tloader_download(this.value)" style="width: 100%;" id="tiles_set">
			<?php			
			$d = dir("../../images/tiles");
			while (false !== ($entry = $d->read())) {
				if (substr($entry,-8) == '-0-0.png') {
					echo "			<option value=\"".substr($entry,0,-8)."\">".substr($entry,0,-8)."</option>\n";
				}
			}
			$d->close();
			?>
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
	<div class="border">
		<div class="lt"></div><div class="rt"></div><div class="lb"></div><div class="rb"></div>
		<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
		<div class="header">Objects</div>
		<div class="content">
			<span id="objects_status"></span>
			<select onchange="oloader_download(this.value)" style="width: 100%;" id="objects_set">
			<option value="furniture" selected="selected">Furniture</option>
			<option value="plant">Plants</option>
			<option value="building">Buildings</option>
			<option value="char">Charcters</option>
			<option value="element">Elements</option>
			<option value="nature">Nature</option>
			<option value="village">Village</option>
			<option value="dungeon">Dungeon</option>
			<option value="internal">Internal</option>
			</select>
		</div>
		<div class="objects_host" id="objects_host">
			&nbsp;
		</div>
        <div class="sidemenu">
            <div class="rt"></div><div class="rb"></div><div class="t"></div><div class="b"></div><div class="r"></div>
            <div class="content" style="height: 80px;">
                <div id="objects_clear"><a href="javascript:;" onclick="ui_objclear()" title="Remove an object from map"><img src="images/edit_remove.png" border="0" /></a></div>
                <div id="objects_put"><a href="javascript:;" onclick="ui_objput()" title="Put an object on map"><img src="images/edit_add.png" border="0" /></a></div>
                <div id="objects_edit"><a href="javascript:;" onclick="ui_objedit()" title="Edit an object"><img src="images/edit.png" border="0" /></a></div>
                <div id="objects_region"><a href="javascript:;" onclick="ui_objdefregion()" title="Define object by map region"><img src="images/frame.png" border="0" /></a></div>
            </div>
        </div>
	</div>
	<div class="border">
		<div class="lt"></div><div class="rt"></div><div class="lb"></div><div class="rb"></div>
		<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
		<div class="header">Collision Grid</div>
        <div class="sidemenu">
            <div class="rt"></div><div class="rb"></div><div class="t"></div><div class="b"></div><div class="r"></div>
            <div class="content" style="height: 40px;">
                <div id="cgrid_clear"><a href="javascript:;" onclick="ui_cgrid_erase()"><img src="images/edit_remove.png" border="0" /></a></div>
                <div id="cgrid_put"><a href="javascript:;" onclick="ui_cgrid_put()"><img src="images/edit_add.png" border="0" /></a></div>
            </div>
        </div>
		<div>
        	Difficulty to enter the tile:<br />
        	<div class="slider">
            	<a href="javascript:;" onclick="cgrid_setatt(0,this);" style="background-color:#D5FFD6;">No</a>
            	<a href="javascript:;" onclick="cgrid_setatt(5,this);" style="background-color:#E9FFD5;">5%</a>
            	<a href="javascript:;" onclick="cgrid_setatt(10,this);" style="background-color:#F2FFD5;">10%</a>
            	<a href="javascript:;" onclick="cgrid_setatt(20,this);" style="background-color:#FFFED5;">20%</a>
            	<a href="javascript:;" onclick="cgrid_setatt(50,this);" style="background-color:#FFEED5;">50%</a>
            	<a href="javascript:;" onclick="cgrid_setatt(70,this);" style="background-color:#FFE4D5;">70%</a>
            	<a href="javascript:;" onclick="cgrid_setatt(80,this);" style="background-color:#FFD5D5;">80%</a>
            	<a href="javascript:;" onclick="cgrid_setatt(100,this);" style="background-color:#E9E9E9;">Full</a>
            </div>
        </div>
	</div>
	</td>
	</tr>
</table>
<div class="border" id="ui_defobj" style="position: absolute; left: 110px; top: 60px; width: 522px; height: 440px; background-color:#FFFFFF; visibility: hidden; z-index:10001">
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
<div class="border" id="ui_objinfo" style="position: absolute; left: 250px; top: 100px; width: 340px; height: 250px; background-color:#FFFFFF; visibility: hidden; z-index:10002">
	<div class="lt"></div><div class="rt"></div><div class="lb"></div><div class="rb"></div>
	<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
	<div class="header">Edit Parameters</div>
	<div class="content" style="height: 32px;">
		<div class="topmenu">
			<a href="javascript:;" onclick="win_editobj_addparm();"><img src="images/edit_add32.png" border="0" align="absmiddle" /> <u>A</u>dd Parameter</a>
			<a href="javascript:;" onclick="win_editobj_save();"><img src="images/filesave.png" border="0" align="absmiddle" /> <u>S</u>ave</a>
			<a href="javascript:;" onclick="win_editobj_cancel();"><img src="images/button_cancel.png" border="0" align="absmiddle" /> <u>C</u>ancel</a>
		</div>			
	</div>
	<div id="ui_objinfo_data" class="dynamic_input">
			
	</div>
</div>
<div id="msg"></div>
</body>
</html>
