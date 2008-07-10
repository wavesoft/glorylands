{literal}
<script language="javascript">
function frmAddobjSubmit() {
	var frm = $('frm_admin_addobj');
	var o_model = frm.model.value;
	var o_template = frm.template.value;
	var o_name = frm.objname.value;
	var o_z = frm.zindex.value;
	var o_vars = frm.vars.value;
	var data = {'model': o_model, 'template': o_template, 'name': o_name, 'vars': o_vars, 'z': o_z};
	gloryIO('?a=admin.addobj&action=rect',data);
	return false;
}
</script>
{/literal}
<form id="frm_admin_addobj">
<table>
    <tr>
		<td>Object Model:</td>
        <td>
        <select id="model">
        {section name=id loop=$_my.objects}
        <option value="{$_my.objects[id]}">{$_my.objects[id]}</option>
        {/section}
        </select>        </td>
    </tr>
    <tr>
		<td>GO Template:</td>
        <td>
        <select id="template">
        {section name=id loop=$_my.templates}
        <option value="{$_my.templates[id].index}">{$_my.templates[id].name}</option>
        {/section}
        </select>        </td>
    </tr>
    <tr>
		<td>Object Name:</td>
        <td>
        <input type="text" id="objname" value="Object" />        </td>
    </tr>
    <tr>
		<td valign="top">Z Index:</td>
        <td>
		<input type="text" id="zindex" value="0" />
        </td>
    </tr>
    <tr>
		<td valign="top">Variables:</td>
        <td>
		<input type="text" id="vars" /><br />
      <small>Format: <b>variable</b>=<b>value</b>; variable=value...</small>      
        </td>
    </tr>
    <tr>
      <td valign="top">&nbsp;</td>
      <td align="right"><label for="button"></label>
      <input type="button" onClick="frmAddobjSubmit()" value="Create Object">
      </td>
    </tr>
</table>
</form>