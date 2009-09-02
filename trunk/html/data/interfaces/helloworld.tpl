{assign var="title" value="Hello World"}
{include file="interface.header.tpl"}
<tr>
  <td colspan="2" class="textframe">
  <h1>{$text} {$name}!</h1>
  <p>Your char is <img src="{$images}/{$char.icon}" align="absmiddle"/>{$char.name}</p>
  <p>This is an example of data loop:</p>
  <table>
    {section name=id loop=$someitems}
    <tr>
      <td><img alt="{$someitems[id].tip}" src="images/{$someitems[id].icon}" /></td>
      <td><span title="{$someitems[id].tip}">{$someitems[id].text}</span></td>
    </tr>
    {/section}
  </table>
  </td>
</tr>
{include file="interface.footer.tpl"}
