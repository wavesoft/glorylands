{assign var="title" value="Welcome"}
{include file="interface.header.tpl"}
  <tr>
    <td class="textframe" align="center"><img src="images/UI/gl_chaos.png" width="226" height="226" /></td>
    <td width="215" rowspan="2" align="center" valign="top" class="panelframe">
	    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
	    <tr>
			  <td width="17" height="44" background="images/UI/head_l.gif">&nbsp;</td>
		  	  <td width="169" background="images/UI/head_rep.gif" class="sideTbl">{#SERVER_STATUS#}</td>
			  <td width="18" background="images/UI/head_r.gif">&nbsp;</td>
		  </tr>
			<tr>
			  <td align="center" colspan="3" class="bluebk"><p>{#GAME_UNDER_CONSTRUCTION#}</p>
			  <p><div style="background-image:url(images/UI/load_{$server_load_img}.gif); background-repeat:no-repeat; width:150px; height:15px; text-align: center; font-size:9px; color: #FFFFFF; font-weight: bold; padding-top:2px; border: none;">Server load: {$server_load_perc}%</div></p></td>
			</tr>
	  </table>	    
	<br />
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
			<tr>
			  <td width="17" height="44" background="images/UI/head_l.gif">&nbsp;</td>
			  <td width="169" background="images/UI/head_rep.gif" class="sideTbl">{#LOGIN#}</td>
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
              <td>{#USERNAME#} :</td>
              <td><input name="name" type="text" class="login" size="10"></td>
            </tr>
            <tr>
              <td>{#PASSWORD#} : </td>
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
              <td align="center">{#WELCOME#} <b>{$player.profile.name}</b>!</td>
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
		  <td width="17" height="44" background="images/UI/head_l.gif">&nbsp;</td>
		  <td width="169" background="images/UI/head_rep.gif" class="sideTbl">{#PLAYER_INFO#}</td>
		  <td width="18" background="images/UI/head_r.gif">&nbsp;</td>
		</tr>
		<tr>
		  <td align="center" colspan="3" class="bluebk">
		  	<p>{#LOGIN_OK#}</p>
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
{/if}    </td>
  </tr>
  <tr>
    <td align="left" class="textframe"><h3>Welcome to GloryLands MMORPG!</h3>
      <p>GloryLands is an open-source, massive multiplayer online game that is based on a continiously extensible world and innumerable items, quests and monsters. You can enter this world just using your web browser. No extra software or knowledge is required.</p>
      <p>Keep in mind that this is only a demonstration of the latest stable release of the game. You can find more information on SourceForge: <a href="https://sourceforge.net/projects/glorylandsweb-b">https://sourceforge.net/projects/glorylandsweb-b</a></p>
      <h4>Disclamer</h4>
      <p>I want to inform the users of this website that most of the graphics of this game are not my material. If you are fan of the most known RPG games, you'll probably find many simillarities. I tried to use only free material found on the internet, buf if I have violated any copyright law please let me know. I'll try to remove it as soon as possible. I hope that through the open-data system many new copyleft or copyrightless graphics will be added and thus, I'll be able to remove all the possibly copyrigthed material. </p>
      <p>On the other hand, the game engine and the overall game design is completely my work and written from scratch.</p>
    <p><img src="images/UI/ffox.png" width="24" height="24" align="absmiddle" /> <img src="images/UI/chrome.png" width="24" height="24" align="absmiddle" /> Best viewed on Chrome or Firefox </p>
	<p><a href="http://sourceforge.net"><img align="absmiddle" src="http://sflogo.sourceforge.net/sflogo.php?group_id=230518&amp;type=3" alt="SourceForge.net Logo" width="125" border="0" height="37"></a> This is an open-source project</p></td>
  </tr>
{include file="interface.footer.tpl"}
