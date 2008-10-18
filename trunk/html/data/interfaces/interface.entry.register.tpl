{assign var="title" value="Create Account"}
{include file="interface.header.tpl"}
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
    <td align="left" class="textframe"><h3>Create new account </h3>
      <p>Please complete the information required to create a new account. No activation is required, but your account might be deleted on future database update: </p>
      <form  method="post" action="">
	  <input type="hidden" name="action" value="create" />
	  <table>
	  <tr>
	  	<td>Username:</td>
		<td><input type="text" name="username" />
	  </tr>
	  <tr>
	  	<td>Password:</td>
		<td><input type="password" name="password" />
	  </tr>
	  <tr>
	  	<td>Confirm:</td>
		<td><input type="password" name="password2" />
	  </tr>
	  <tr>
	  	<td>E-mail:</td>
		<td><input type="email" name="email" />
	  </tr>
	  <tr>
	  	<td colspan="2">
		<input type="submit" value="Create account" />
		</td>
	  </tr>
	  </table>
      </form>      	  
    </td>
  </tr>
{include file="interface.footer.tpl"}