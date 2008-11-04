<?php access_check(ACCESS_EDITOR); ?>
<script type="text/javascript" src="tools/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	tinyMCE.init({
	mode : "exact",
	elements : "tip",
	theme : "simple"
	});
</script>
<h1>Add New Tip <em><?php echo $row['title']; ?></em></h1>
<p>From this page you can create a new tip</p>
<form action="?page=tips" method="post">
<input type="hidden" name="action" value="add" />
<table class="editor" cellpadding="2" cellspacing="0" width="100%">
<tr class="info">
	<td colspan="2">General information</td>
</tr>
<tr>
	<td width="180">Title:<br /><small>The tip title</small></td>
	<td><input name="title" type="text" value="<?php echo $_REQUEST['title']; ?>" size="70" /></td>
</tr>
<tr>
	<td>Importance:<br /><small>How important the tip is</small></td>
	<td><select name="importance">
	<option value="HIGH">HIGH</option>
	<option value="NORMAL" selected="selected">NORMAL</option>
	<option value="LOW">LOW</option>
	</select>
	</td>
</tr>
<tr class="info">
	<td colspan="2">Tip text</td>
</tr>
<tr>
	<td colspan="2">
	<textarea id="tip" name="tip" rows="9" style="width: 100%;"><?php echo $_REQUEST['tip']; ?></textarea>
	</td>
</tr>
<tr class="info">
	<td colspan="2">Trigger</td>
</tr>
<tr>
	<td>Action:<br /><small>The tip will be shown when this action is requested</small></td>
	<td><input name="trigger_action" type="text" value="<?php echo $_REQUEST['trigger_action']; ?>" size="39" /></td>
</tr>
<tr>
	<td>Request:<br /><small>Additional required request or user variables. Syntax:<br />%varname% &gt;,&lt;,=,! value [and,or ...]<br />Ex: %INT% > 20 and %DEX% < 10</small></td>
	<td valign="middle"><textarea name="trigger_request" cols="30" rows="3"><?php echo $_REQUEST['trigger_request']; ?></textarea>
	</td>
</tr>
<tr>
	<td colspan="2"><input type="submit" class="button" value="Sumit Changes" /></td>
</tr>
</table>
</form>