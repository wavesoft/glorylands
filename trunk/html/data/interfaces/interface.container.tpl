<div class="bag_container">
{$_my.header}
{if is_array($_my.objects) && count($_my.objects) > 0 }
<ul>
{section name=id loop=$_my.objects}
<li oncontextmenu="var e=new Event(event); piemenu_dispose(); piemenu_init(e.event.clientX,e.event.clientY,{$_my.objects[id].guid},'CONTAINER', {$_my.parent});e.stop();">
	<img onclick="javascript:gloryIO('?a={$_my.objects[id].handler}&guid={$_my.objects[id].guid}');" onload="qb_makedraggable(this,{$_my.objects[id].guid},false,{$_my.parent},'interface.container');" src="images/{$_my.objects[id].image}" />
	<a href="javascript:gloryIO('?a={$_my.objects[id].handler}&guid={$_my.objects[id].guid}');">{$_my.objects[id].name}</a>
	<span>{$_my.objects[id].desc}</span>
	<p>{$_my.objects[id].cost}</p>
</li>
{/section}
</ul>
{else}
<img class="web" src="images/UI/web.gif" />
<p class="empty" align="center">There are no items here</p>
{/if}
</div>
