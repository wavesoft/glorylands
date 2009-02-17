{if $_my.slots == 0}
<p align="center">No items</p>
{else}
<div class="bag_slotted" style="width: 100%;">
{section name=slot start=1 loop=$_my.slots+1 step=1}
<div class="drag_host {$smarty.section.slot.index} {$_my.parent}">{if $_my.objects[slot]}<span {if $_my.objects[slot].count > 1}count="{$_my.objects[slot].count}"{/if} tip="{$_my.objects[slot].tip}" class="drag_able {$_my.objects[slot].guid}"><img src="images/{$_my.objects[slot].image}" width="38" height="38" />{if $_my.objects[slot].count > 1}<b>{$_my.objects[slot].count}</b>{/if}</span>{/if}</div>
{/section}
</div>
<div class="clear"></div>
{/if}