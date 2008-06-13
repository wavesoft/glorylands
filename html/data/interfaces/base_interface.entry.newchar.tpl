<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>GloryLands :: Chaos Milestone</title>
{literal}
<style type="text/css">
<!--
body {
	background-image: url(images/UI/login_back.jpg);
	background-color: #000000;
	background-repeat: no-repeat;
	background-position: top center;
	background-attachment: fixed;
	margin: 0px;
}
.textframe {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #CCCCCC;
	background-image: url(images/UI/backblack.png);
	padding: 5px;
}
.panelframe {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #CCCCCC;
	background-color: #000000;
	padding: 5px;
}
.sideTbl {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #FF9900;
	text-align: center;
	vertical-align: middle;
	font-weight: bold;
}
.bluebk {
	background-image: url(images/UI/backblack.png);
	font-size: 10px;
	border-right-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-right-style: solid;
	border-bottom-style: solid;
	border-left-style: solid;
	border-right-color: #333333;
	border-bottom-color: #333333;
	border-left-color: #333333;
	margin: 2px;
	padding: 2px;
}
input.login {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #999999;
	background-color: #1D1D1D;
	border: 1px solid #666666;
}
button.login {
	font-family: Arial, Helvetica, sans-serif;
	background-image: url(images/UI/login.gif);
	height: 20px;
	width: 80px;
	border: 1px solid #000000;
}
button.logout {
	font-family: Arial, Helvetica, sans-serif;
	background-image: url(images/UI/logout.gif);
	height: 20px;
	width: 80px;
	border: 1px solid #000000;
}
button.create {
	font-family: Arial, Helvetica, sans-serif;
	background-image: url(images/UI/create.gif);
	height: 20px;
	width: 80px;
	border: 1px solid #000000;
}
select.enter {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #66CCFF;
	background-color: #000066;
	border: 1px solid #0066FF;
}
button.blank {
	font-family: Arial, Helvetica, sans-serif;
	background-image: url(images/UI/ok.gif);
	height: 20px;
	width: 80px;
	border: 1px solid #000000;
}
-->
</style>
{/literal}
</head>

<body bgcolor="#000000">
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td class="textframe" align="center"><img src="images/UI/gl_chaos.png" width="226" height="226" /></td>
  </tr>
{if $error}
  <tr>
    <td class="textframe" align="center"><font color="#FF0000">{$error}</font></td>
  </tr>
{/if}
  <tr>
    <td align="left" class="textframe"><h3>Create a new Character </h3>
	  <form action="?a=interface.entry.newchar" method="post">
	  <input type="hidden" name="action" value="create" />
      <p>Please select one of the follow templates to use for your character: 
	  <select name="template">
{section name=id loop=$templates}
		<option value="{$templates[id].template}">{$templates[id].race}</option>
{/section}
	  </select>
	  </p>
	  <p>Choose a name:	<input type="text" name="name" /></p>
	  <p><input type="submit" /></p>
	  </form>
      <p>&nbsp;</p></td>
  </tr>
  <tr>
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
</table>
</body>
</html>
