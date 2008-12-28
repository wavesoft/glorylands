<table class="book" width="100%" height="100%" cellspacing="0" cellpadding="0">
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
		<td width="120" height="120" align="center" valign="top" class="frame128"><img src="images/inventory/Silver-Fern-128x128.png"/></td>
		<td rowspan="2" valign="top"><div class="merchant_sell">
		{section name=id loop=$_my.objects}
		<div>
			<img src="images/UI/merchant-border.gif" />
			<a href="#">
				<img src="images/{$_my.objects[id].icon}" width="32" height="32" border="0" />
				<h4>{$_my.objects[id].name}</h4>
				<div>{$_my.objects[id].desc}<br /><span class="money">{$_my.objects[id].cost}</span></div>				
			</a>
		</div>
		{/section}
		</div></td>
	</tr>
	<tr>
	  <td height="120" align="center" valign="top" class="book_mini">
	  	<table>
		 <tr><td><b>Merchant:</b></td><td>{$_my.merchant}</td></tr>
		 <tr><td><b>Gold:</b></td><td>{$_my.gold} <img src="images/UI/gold.gif" align="absmiddle" /></td></tr>
		 <tr><td colspan="2" align="center" valign="bottom" height="24"><a href="javascript:ddwin_dispose();"><img border="0" src="images/UI/return.gif" /></a></td></tr>
	  	</table>
	  </td>
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