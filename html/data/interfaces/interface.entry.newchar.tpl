{assign var="title" value="New Character"}
{include file="interface.header.tpl"}
  <tr>
    <td class="textframe" align="center"><img src="images/UI/gl_chaos.png" width="226" height="226" /></td>
    <td width="215" rowspan="2" align="center" valign="top" class="panelframe">
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
{include file="interface.footer.tpl"}
