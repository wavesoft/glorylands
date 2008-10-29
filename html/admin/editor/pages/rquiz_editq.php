<?php access_check(ACCESS_EDITOR); ?>
<?php 
// Perform any requested actions
if (isset($_REQUEST['a'])) {
	$sql->editRow('data_regquiz_questions', '`index` = '.$_REQUEST['quiz'],array(
		'title' => stripslashes($_REQUEST['title']),
		'question' => stripslashes($_REQUEST['question'])
	));
	?>
	<div class="msg_ok">Updated successfully</div>
	<form target="left" action="nav.php">
	<input type="hidden" name="page" value="regquiz" />
	<input type="hidden" name="quiz" value="<?php echo $_REQUEST['quiz']; ?>" />
	</form>
	<script language="javascript">document.forms[0].submit();</script>
	<?php	
}
$ans=$sql->query("SELECT * FROM `data_regquiz_questions` WHERE `index` = ".$_REQUEST['quiz']);
if (!$ans) {
	echo "<div class=\"msg_error\">Cannot load question information: ".$sql->getError()."</div>\n";
}
$row = $sql->fetch_array();
?>
<h1>Edit question</h1>
<form action="" method="post">
<input type="hidden" name="quiz" value="<?php echo $_REQUEST['quiz']; ?>" />
<input type="hidden" name="a" value="edit" />
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
