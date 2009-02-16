<h2>Install extra packages</h2>
<p>This installation is shipped with some additional game packages. From here, you can select the packages you want to be installed:</p>
<div class="separator">Additional Packages</div>
<form action="" method="post">
<input type="hidden" name="step" value="6" />
<input type="hidden" name="prev_step" value="5" />
<ul>
<?php
$i=0;
$d = dir("data/packages");
while (false !== ($entry = $d->read())) {
	if (substr($entry,0,1)!='.') {
		$fname = substr($entry,0,-4);
?>
	<li><input type="checkbox" name="package[<?php echo $i; ?>]" value="<?php echo $entry; ?>" id="p<?php echo $i; ?>" /><label for="p<?php echo $i; ?>"><?php echo $fname; ?></label></li>
<?php		
	}
	$i++;
}
$d->close();
?>
</ul>
<p><input type="submit" class="button" value="Complete Installation" /></p>
</form>