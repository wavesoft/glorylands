<?php 
// Perform any requested actions
if (isset($_REQUEST['a'])) {
	$sql->addRow('data_regquiz_questions',array(
		'title' => stripslashes($_REQUEST['title']),
		'question' => stripslashes($_REQUEST['question'])
	));
	?>
	<div class="msg_ok">Added successfully</div>
	<form target="left" action="nav.php">
	<input type="hidden" name="page" value="regquiz" />
	<input type="hidden" name="quiz" value="<?php echo $_REQUEST['quiz']; ?>" />
	</form>
	<script language="javascript">document.forms[0].submit();</script>
	<?php	
}
?>
<h1>Add question</h1>
<form action="" method="post">
<input type="hidden" name="quiz" value="<?php echo $_REQUEST['quiz']; ?>" />
<input type="hidden" name="a" value="add" />
<table class="discreet">
<tr>
	<th align="right">Title:</th>
	<td><input name="title" type="text" value="<?php echo $row['title']; ?>" size="79" /></td>
</tr>
<tr>
	<th valign="top" align="right">Question:</th>
	<td><textarea cols="60" rows="7" name="question"><?php echo $row['question']; ?></textarea></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>
	<input type="submit" class="button" value="Complete Edit" />
	</td>
</tr>
</table>
</form>
