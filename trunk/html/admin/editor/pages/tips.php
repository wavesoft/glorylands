<?php access_check(ACCESS_EDITOR); ?>
<?php 

// Perform any actions, if told so
if (isset($_REQUEST['action'])) {

	// -- EDIT --
	if ($_REQUEST['action'] == 'edit') {
		$ans=$sql->editRow('data_tips', '`index` = '.$_REQUEST['index'], array(
			'title' => $_POST['title'],
			'importance' => $_POST['importance'],
			'tip' => $_POST['tip'],
			'trigger_action' => $_POST['trigger_action'],
			'trigger_request' => $_POST['trigger_request']
		));
		if (!$ans) {
			?>
			<div class="msg_error">Error while updating tip entry! Changes are not saved!</div>
			<?php
		} elseif ($sql->emptyResults) {
			?>
			<div class="msg_warn">No changes were made!</div>
			<?php
		} else {
			?>
			<div class="msg_ok">Tip updated successfully.</div>
			<?php
		}
		
	// -- ADD --
	} elseif ($_REQUEST['action'] == 'add') {
		$ans=$sql->addRow('data_tips', array(
			'title' => $_POST['title'],
			'importance' => $_POST['importance'],
			'tip' => $_POST['tip'],
			'trigger_action' => $_POST['trigger_action'],
			'trigger_request' => $_POST['trigger_request'],
			'contributor' => $_SESSION[PLAYER][PROFILE]['index']
		));
		if (!$ans) {
			?>
			<div class="msg_error">Error while adding tip entry! Changes are not saved!</div>
			<?php
		} elseif ($sql->emptyResults) {
			?>
			<div class="msg_warn">No changes were made!</div>
			<?php
		} else {
			?>
			<div class="msg_ok">Tip added successfully.</div>
			<?php
		}
	}

}
?>
<h1>Introduction Tips</h1>
<p>The introduction tips are pop-up messages that are displaied when the user tries to do something for the fisrt time. All the tips are disposed after they are shown.</p>
<table class="general" cellpadding="2" cellspacing="0" width="100%">
<tr class="header">
	<td width="16" align="center">#</td>
	<td width="60" align="center">Editor</td>
	<td width="90" align="center">Action</td>
	<td>Title</td>
	<td width="60" align="center">Actions</td>
</tr>
<?php
$sql->query("SELECT * FROM `data_tips`");
while ($row = $sql->fetch_array(MYSQL_ASSOC)) {
?>
<tr>
	<td align="center"><?php echo $row['index']; ?></td>
	<?php
		if ($row['contributor'] == 0) {
	?>		
	<td align="center" class="access_admin">System</td>
	<?php
		} elseif ($row['contributor'] == $_SESSION[PLAYER][PROFILE]['index']) {
	?>		
	<td align="center" class="access_you">You</td>
	<?php
		} else {
			$user='Other';
			if (($_SESSION[PLAYER][PROFILE]['level'] == 'ADMIN') || ($_SESSION[PLAYER][PROFILE]['level'] == 'MODERATOR')) {
				$user='['.$sql->query_and_get_value('SELECT `name` FROM `users_accounts` WHERE `index` = '.$row['contributor']).']';
			}
	?>		
	<td align="center" class="access_other"><?php echo $user; ?></td>
	<?php
		}
	?>
	<td width="60" align="center"><em><?php echo $row['trigger_action']; ?></em></td>
	<td><?php echo $row['title']; ?></td>
	<td align="center">
	<?php 
		// Calculate access
		$access = false;
		$access = $access || ($row['contributor'] == $_SESSION[PLAYER][PROFILE]['index']); /* Owner */
		$access = $access || ($_SESSION[PLAYER][PROFILE]['level'] == 'ADMIN'); /* Admin */
		$access = $access || ($_SESSION[PLAYER][PROFILE]['level'] == 'MODERATOR'); /* Moderator */
		if ($access) {
		?>
		<a title="Edit this entry" href="?page=tips_edit&index=<?php echo $row['index']; ?>"><img src="theme/icons/edit.png" border="0" /></a>
		<?php
		} else {
		?>
		<img src="theme/icons/edit_blur.png" border="0" />
		<?php
		}
	?>
	<a title="View this entry" href="?page=tips_view&index=<?php echo $row['index']; ?>"><img src="theme/icons/14_layer_novisible.gif" border="0" /></a>
	</td>
</tr>
<?php
}
?>
</table>