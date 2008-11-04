<?php access_check(ACCESS_EDITOR); ?>
<?php
// Get map information
if (isset($_REQUEST['index'])) {
	$sql->query("SELECT * FROM `data_tips` WHERE `index` = ".$_REQUEST['index']);
	$row = $sql->fetch_array();
} else {
	die("<div class=\"msg_error\">Tip not specified!</div>");
}

// Make sure we have the permissions to edit this file
$access = false;
$access = $access || ($row['contributor'] == $_SESSION[PLAYER][PROFILE]['index']); /* Owner */
$access = $access || ($_SESSION[PLAYER][PROFILE]['level'] == 'ADMIN'); /* Admin */
$access = $access || ($_SESSION[PLAYER][PROFILE]['level'] == 'MODERATOR'); /* Moderator */
if (!$access) {
	die('<div class="msg_warn">You have no permission to edit this file!<br />Only the creator and the administrator can edit this tip!</div>');
}

?>
<script type="text/javascript" src="tools/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	tinyMCE.init({
	mode : "exact",
	elements : "tip",
	theme : "simple"
	});
</script>
<h1>Edit Tip <em><?php echo $row['title']; ?></em></h1>
<p>From this page you can edit the tip parameters</p>
<form action="?page=tips" method="post">
<input type="hidden" name="action" value="edit" />
<input type="hidden" name="index" value="<?php echo $_REQUEST['index']; ?>" />
<table class="editor" cellpadding="2" cellspacing="0" width="100%">
<tr class="info">
	<td colspan="2">General information</td>
</tr>
<tr>
	<td width="180">Title:<br /><small>The tip title</small></td>
	<td><input name="title" type="text" value="<?php echo $row['title']; ?>" size="70" /></td>
</tr>
<tr>
	<td>Importance:<br /><small>How important the tip is</small></td>
	<td><select name="importance">
	<option value="HIGH" <?php if ($row['importance'] == 'HIGH') echo 'selected="selected"'; ?>>HIGH</option>
	<option value="NORMAL" <?php if ($row['importance'] == 'NORMAL') echo 'selected="selected"'; ?>>NORMAL</option>
	<option value="LOW" <?php if ($row['importance'] == 'LOW') echo 'selected="selected"'; ?>>LOW</option>
	</select>
	</td>
</tr>
<tr class="info">
	<td colspan="2">Tip text</td>
</tr>
<tr>
	<td colspan="2">
	<textarea id="tip" name="tip" rows="9" style="width: 100%;"><?php echo $row['tip']; ?></textarea>
	</td>
</tr>
<tr class="info">
	<td colspan="2">Trigger</td>
</tr>
<tr>
	<td>Action:<br /><small>The tip will be shown when this action is requested</small></td>
	<td><input name="trigger_action" type="text" value="<?php echo $row['trigger_action']; ?>" size="39" /></td>
</tr>
<tr>
	<td>Request:<br /><small>Additional required request or user variables. Syntax:<br />%varname% &gt;,&lt;,=,! value [and,or ...]<br />Ex: %INT% > 20 and %DEX% < 10</small></td>
	<td valign="middle"><textarea name="trigger_request" cols="30" rows="3"><?php echo $row['trigger_request']; ?></textarea>
	</td>
</tr>
<tr>
	<td colspan="2"><input type="submit" class="button" value="Sumit Changes" /></td>
</tr>
</table>
</form>