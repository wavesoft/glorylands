<form target="left" action="nav.php">
<input type="hidden" name="page" value="regquiz" />
<input type="hidden" name="quiz" value="4" />
</form>
<script language="javascript">document.forms[0].submit();</script>
<pre>
<?php 
print_r($_REQUEST);
?>
</pre>