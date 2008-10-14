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
button.arrow {
	font-family: Arial, Helvetica, sans-serif;
	background-image: url(images/UI/navbtn_ok.gif);
	height: 20px;
	width: 23px;
	border: 1px solid #000000;
}
select.enter {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #66CCFF;
	background-color: #000066;
	border: 1px solid #0066FF;
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
	font-size: 11px;
	color: #999999;
	background-color: #000000;
	border: 1px solid #333333;
	height: 20px;
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
{if $error}
    <td width="215" rowspan="3" align="center" valign="top" class="panelframe">
{else}
    <td width="215" rowspan="2" align="center" valign="top" class="panelframe">
{/if}
	    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
	    <tr>
			  <td width="17" height="44" background="images/UI/head_l.gif">&nbsp;</td>
		  	  <td width="169" background="images/UI/head_rep.gif" class="sideTbl">Server Status </td>
			  <td width="18" background="images/UI/head_r.gif">&nbsp;</td>
		  </tr>
			<tr>
			  <td align="center" colspan="3" class="bluebk"><p>This game is under construction and the server is not open for the public yet!</p>
			  <p><div style="background-image:url(images/UI/load_{$server_load_img}.gif); background-repeat:no-repeat; width:150px; height:15px; text-align: center; font-size:9px; color: #FFFFFF; font-weight: bold; padding-top:2px; border: none;">Server load: {$server_load_perc}%</div></p></td>
			</tr>
	  </table>	    
	<br /></td>
  </tr>
{if $error}
  <tr>
    <td align="center" class="textframe"><font color="#FF0000">{$error}</font></td>
  </tr>
{/if}
  <tr>
    <td align="left" class="textframe"><h3>{$question.title}</h3>
      <p>{$question.question}</p>
      <form  method="post" action="">
	  <input type="hidden" name="question" value="{$question.index}" />
	  <table>
{section name=id loop=$answers}
	  <tr>
	  	<td width="20"><input type="radio" id="e{$answers[id].index}" name="answer" value="{$answers[id].index}" /></td>
		<td><label for="e{$answers[id].index}">{$answers[id].answer}</label></td>
	  </tr>
{/section}
	  <tr>
	  	<td colspan="2">
		<input type="submit" value="Next Step >>" />
		</td>
	  </tr>
	  </table>	  
      </form>      	  
    </td>
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
