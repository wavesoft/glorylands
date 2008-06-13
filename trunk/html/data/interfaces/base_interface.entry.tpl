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
    <td rowspan="2" align="center" valign="top" class="panelframe">
	    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
			<tr>
			  <td width="18" height="44" background="images/UI/head_l.gif">&nbsp;</td>
			  <td background="images/UI/head_rep.gif" class="sideTbl">Server Status </td>
			  <td width="18" background="images/UI/head_r.gif">&nbsp;</td>
			</tr>
			<tr>
			  <td align="center" colspan="3" class="bluebk"><p>Το παιχνίδι βρίσκεται υπο ανάπτυξη. Ο server λειτουργεί δοκιμαστικά και όχι για το κοινό ακόμα. </p>
			  <p><div style="background-image:url(images/UI/load_{$server_load_img}.gif); background-repeat:no-repeat; width:150px; height:15px; text-align: center; font-size:9px; color: #FFFFFF; font-weight: bold; padding-top:2px; border: none;">Server load: {$server_load_perc}%</div></p></td>
			</tr>
		  </table>	    
		  <br />
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
			<tr>
			  <td width="18" height="44" background="images/UI/head_l.gif">&nbsp;</td>
			  <td background="images/UI/head_rep.gif" class="sideTbl">Login</td>
			  <td width="18" background="images/UI/head_r.gif">&nbsp;</td>
			</tr>
			<tr>
			  <td align="center" colspan="3" class="bluebk">
{if !$player}
		<script language="javascript" src="includes/md5.js"></script>
		<script language="JavaScript">
		{literal}
		<!--
			function doLogin(frm) {
			
			if (frm.elements[1].value == "") {
			   window.alert("Please enter your e-mail!");
			   frm.elements[1].focus();
			   return false;
			}
			if (frm.elements[2].value == "") {
			   window.alert("Please enter your password!");
			   frm.elements[2].focus();
			   return false;
			}
			
			frm.elements[2].value = hexMD5(frm.elements[2].value);

			}
		//-->
		</script>
		{/literal}
		  <form name="frmlogin" method="post" action="?a=interface.entry" onSubmit="doLogin(this)" id="frmlogin">
		  <input type="hidden" name="action" value="login">
          <table width="100%"  border="0" cellspacing="0" cellpadding="1">
{if $loginmsg}	
            <tr>
              <td colspan="2">
			  <font color="#FFCC00">{$loginmsg}</font>
			  </td>
            </tr>
{/if}	  
            <tr>
              <td>Username  :</td>
              <td><input name="name" type="text" class="login" size="10"></td>
            </tr>
            <tr>
              <td>Password : </td>
              <td><input name="password" type="password" class="login" size="10"></td>
            </tr>
            <tr>
              <td colspan="2" align="center"><button class="login" type="submit">&nbsp;</button> <button class="create" type="button" onclick="window.location='?a=interface.entry.register'">&nbsp;</button></td>
            </tr>
            <tr>
              <td colspan="2" align="center"><font size="1" color="#666666"><?php echo $loginmsg; ?></font></button></td>
            </tr>
          </table>	
          </form>        
{else}
		  <form name="frmlogout" method="post" action="?a=interface.entry">
		  <input type="hidden" name="action" value="logout">
          <table width="100%"  border="0" cellspacing="0" cellpadding="1">
            <tr>
              <td align="center">Welcome <b>{$player.profile.name}</b>!</td>
            </tr>
            <tr>
              <td align="center"><button class="logout" type="submit">&nbsp;</button> </td>
            </tr>
          </table>	
          </form>        
{/if}
				</td>
			</tr>
    </table>
    <br />
{if $player}
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
		<tr>
		  <td width="18" height="44" background="images/UI/head_l.gif">&nbsp;</td>
		  <td background="images/UI/head_rep.gif" class="sideTbl">Player Info</td>
		  <td width="18" background="images/UI/head_r.gif">&nbsp;</td>
		</tr>
		<tr>
		  <td align="center" colspan="3" class="bluebk">
		  	<p>You are successfully loged into the game system. Please select one of your charachters below to start the game with:</p>
{if $chars}
			<p align="center">
			<form action="?a=interface.entry" method="post">
			<input type="hidden" name="action" value="choose" />
			<select class="enter" name="char">
{section name=id loop=$chars}
				<option value="{$chars[id].guid}">{$chars[id].name}</option>
{/section}
			</select>
			<button class="arrow" type="submit"></button>
			</form>
			</p>
{/if}
			<p align="center"><button class="create" type="button" onclick="window.location='?a=interface.entry.newchar'"></button>
		   </td>
		</tr>
	  </table>	    
{/if}
    </td>
  </tr>
  <tr>
    <td align="left" class="textframe"><h3>Welcome to GloryLands MMORPG!</h3>
      <p>GloryLands is an open-data, massive multiplayer online game that is based on a continiously extensible world and innumerable items, quests and monsters. You can enter this world just using your web browser. No extra software or knowledge is required.</p>
      <h4>Disclamer</h4>
      <p>I want to inform the users of this website that most of the graphics of this game are not my material. If you are fan of the most known RPG games, you'll probably find many simillarities. I tried to use only free material found on the internet, buf if I have violated any copyright law please let me know. I'll try to remove it as soon as possible. I hope that through the open-data system many new copyleft or copyrightless graphics will be added and thus, I'll be able to remove all the possibly copyrigthed material. The source of the graphics are listed <a href="disclaimer.html" target="_blank">here</a> </p>
      <p>On the other hand, the game engine and the overall game design is completely my work.</p>
    <p><img src="images/ui/ffox.png" width="48" height="48" align="absmiddle" />Optimized for mozilla Firefox </p></td>
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
