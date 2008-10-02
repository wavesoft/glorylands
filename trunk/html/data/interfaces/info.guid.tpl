<table class="book" width="100%" height="110%" cellspacing="0" cellpadding="0" style="position: relative;">
<tr>
	<td colspan="3" style="background-image:url(images/UI/top-bg2.gif); height: 24px; background-position: bottom; background-repeat: repeat-x"></td>
</tr>
<tr>
	<td class="br2_lt"></td>
	<td class="br2_t"></td>
	<td class="br2_rt"></td>
</tr>
<tr>
	<td class="br2_l"></td>
	<td align="left" valign="top">
	<table width="100%">
	<tr>
		<td width="{$_my.icon_width}" height="-1" align="center" valign="middle" class="frame128"><img onload="qb_makedraggable(this,{$_my.guid},false);" src="images/{$_my.icon}"/></td>
		<td rowspan="2" valign="top">
		<h1>{$_my.name}</h1>
		<p>{$_my.desc}</p>
        <table width="100%">
        {section name=id loop=$_my.info}
        	<tr>
            	{if $_my.info[id].value == ''} 
            	<td colspan="2" align="center"><i>{$_my.info[id].name}</i></td>
                {else}
            	<td valign="top"><b>{$_my.info[id].name}:</b></td>
            	<td style="width: 100%;">{$_my.info[id].value}</td>
                {/if}            
            </tr>
		{/section}
        </table>		
        </td>
	</tr>
	<tr>
	  <td height="0" align="center" valign="middle">&nbsp;</td>
	  </tr>
	</table>
	</td>
	<td class="br2_r"></td>
</tr>
<tr>
	<td class="br2_lb"></td>
	<td class="br2_b"></td>
	<td class="br2_rb"></td>
</tr>
</table>