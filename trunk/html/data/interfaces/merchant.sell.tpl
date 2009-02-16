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
		<td width="120" height="120" align="center" valign="top">
			<div class="frame128"><img src="images/inventory/Silver-Fern-128x128.png"/></div>
			<table>
			 <tr><td colspan="2" valign="top" height="80" align="left"><small>{$_my.welcome}</small></td></tr>
			 <tr><td><b>Gold:</b></td><td>{$_my.gold} <img src="images/UI/gold.gif" align="absmiddle" /></td></tr>
			 <tr><td><b>Slots:</b></td><td>{$_my.slots}</td></tr>
			 <tr><td colspan="2" align="center" valign="bottom" height="24" class="book_menu">
				 <br />
				 <ul>
					<li><a href="javascript:;" onclick="gloryIO('?a=merchant.buy&guid={$_my.guid}')"><img border="0" src="images/UI/btn_buy.gif" /></a></li>
					<li><a href="javascript:;" onclick="gloryIO('?a=merchant.sell&guid={$_my.guid}')"><img border="0" src="images/UI/btn_sell.gif" /></a></li>
					<li><a href="javascript:;" onclick="gloryIO('?a=map.grid.get')"><img border="0" src="images/UI/return.gif" /></a></li>
				</ul>
			</td></tr>
			</table>
		</td>
		<td valign="top">
		{if is_array($_my.objects) && count($_my.objects) > 0 }
		<div class="merchant_sell">
			{section name=id loop=$_my.objects}
			<div class="item">
				<img src="images/UI/merchant-border.gif" />
				{if $_my.objects[id].count>1 }
				<a href="javascript:;" onclick="mf_count_items(this,'?a=merchant.sell&guid={$_my.guid}&sell={$_my.objects[id].guid}',{$_my.objects[id].count})">
				{else}
				<a href="javascript:;" onclick="gloryIO('?a=merchant.sell&guid={$_my.guid}&sell={$_my.objects[id].guid}')">
				{/if}
					<img src="images/{$_my.objects[id].icon}" width="32" height="32" border="0" />
					<h4>{$_my.objects[id].name}</h4>
					<div>{$_my.objects[id].desc}<br /><span class="money">{$_my.objects[id].cost}</span></div>				
					{if $_my.objects[id].count>1 }
					<small>{$_my.objects[id].count}</small>
					{/if}
				</a>
			</div>
			{/section}

			<div class="count" id="mf_counter_host" style="visibility: hidden;">
				<div>How many items to sell?</div>
				{section name=c start=1 loop=21 step=1}<a href="javascript:;" id="fm_count_{$smarty.section.c.index}" onmouseover="mf_count_move({$smarty.section.c.index});" onclick="mf_count_set({$smarty.section.c.index});">&nbsp;</a>{/section} <span id="mf_counter">0</span>
			</div>
		</div>
		{else}
		You have nothing that can be sold!
		{/if}
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