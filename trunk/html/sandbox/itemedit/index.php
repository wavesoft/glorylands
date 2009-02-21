<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>GloryLands Engine :: Items Editor</title>
<script language="javascript" src="../../includes/mootools-release-1.11.js"></script>
<script language="javascript" src="itemsapi-1.1-src.js"></script>
<link rel="stylesheet" type="text/css" href="edit.css">
</head>
<body>
<table width="1100">
	<tr>
		<td width="138">
			<div class="border">
				<div class="lt"></div><div class="rt"></div><div class="lb"></div><div class="rb"></div>
				<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
				<div class="header">Image</div>
				<div style="height: 128px; width: 128px; text-align: center;">
					<img align="absmiddle" src="../../images/inventory/Clipping-Picture-128x128.png" />
				</div>
				<a href="#" class="browse_img"><img src="images/fileopen.png" border="0" /></a>
			</div>
		</td>
		<td>
			<div class="border">
				<div class="lt"></div><div class="rt"></div><div class="lb"></div><div class="rb"></div>
				<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
				<div class="header">Item information</div>
				<div style="height: 128px;">
					<table>
						<tr>
							<td>Name:</td>
							<td><input name="value" type="text" id="item_name" size="30" /></td>
							<td>Keywords:</td>
							<td><input name="value" type="text" id="item_keyword" size="30" /></td>
						</tr>
						<tr>
							<td valign="top">Description:</td>
							<td colspan="3"><textarea name="value" cols="80" rows="4" id="item_desc"></textarea></td>
						</tr>
					</table>
				</div>
			</div>
		</td>
		<td width="138">
			<div class="border">
				<div class="lt"></div><div class="rt"></div><div class="lb"></div><div class="rb"></div>
				<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
				<div class="header">Menu</div>
				<div class="navmenu" style="height: 128px;">
					<a href="javascript:;" onclick="iedit_reset();" accesskey="n"><img src="images/filenew.png" border="0" /><br /><u>N</u>ew</a>
					<a href="javascript:;" onclick="ui_save();" accesskey="s"><img src="images/filesave.png" border="0" /><br /><u>S</u>ave</a>
					<a href="javascript:;" onclick="ui_load();" accesskey="o"><img src="images/fileopen.png" border="0" /><br /><u>O</u>pen</a>
					<a href="javascript:;" onclick="ui_compile();" accesskey="c"><img src="images/packet.png" border="0" /><br /><u>C</u>ompile</a>
				</div>
				<div style="clear: both"></div>
				<div class="message" id="json_output">&nbsp;</div>
			</div>
		</td>
	</tr>
	<tr>
		<td valign="top" colspan="3">
		<div class="border">
			<div class="lt"></div><div class="rt"></div><div class="lb"></div><div class="rb"></div>
			<div class="t"></div><div class="b"></div><div class="l"></div><div class="r"></div>
			<div class="header">Item attributes</div>
			<div class="itemlist_host">
				<div id="attrib_list">
					<div class="header">
						<span style="width: 240px;" title="What kind of attribute you want to define">Attribute Type</span>
						<span style="width: 220px;" title="The attribute modifier">Modifier</span>
						<span style="width: 100px;" title="The attribute or attribute modifier value">Value</span>
						<span style="width: 78px;" title="The maximum range within to perform random value decresion">Variation</span>
						<span style="width: 78px;" title="The chance this parameter has to be used">Use Chance</span>
						<span style="width: 50px; background-color: #BCD9AE;" title="(Inheritance) How important is this attribute">Gravity</span>
						<span style="width: 80px; background-color: #BCD9AE;" title="(Inheritance) The chance this attribute has to be rejected when merged with another item">Drop Chance</span>
						<span style="width: 80px; background-color: #BCD9AE;" title="(Inheritance) The attenuation the attribute value will receive when merged with another item">Attennuation</span>
						<span style="width: 80px; background-color: #BCD9AE;" title="(Inheritance) The attenuation direction">Att. Direction</span>
					</div>
				</div>
			</div>
			<div class="topmenu" style="bottom: 0px;">
				<a href="javascript:;" onclick="attrib_new();"><img src="images/edit_add32.png" border="0" align="absmiddle" /> <u>A</u>dd Attribute</a>
				<a href="javascript:;" onclick="attrib_delete();"><img src="images/button_cancel.png" border="0" align="absmiddle" /> <u>R</u>emove selected attributes</a>
			</div>
			<div style="clear: both"></div>
		</div>
		</td>
	</tr>
</table>
</body>
</html>
