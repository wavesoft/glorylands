<?php
$has_error = false;
$issues = array();
$root_path = dirname(dirname(dirname(__FILE__)));

//echo "<pre>".print_r($_SERVER,true)."</pre>";

$root_url = 'http://'.$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['REQUEST_URI']));
$root_url = str_replace('\\','/', $root_url);
?>
<form action="" method="post">
<input type="hidden" name="step" value="3" />
<input type="hidden" name="prev_step" value="2" />
<h2>Basic configuration</h2>
<p>In this step you will set up the basic game configuration.</p>
<div class="separator">Filesystem and URL</div>
<p>
	<table>
		<tr>
			<td width="120">Installation folder: </td>
			<td><input class="text" name="config[BASE]" type="text" value="<?php _echo($_SESSION['config']['GAME']['BASE'], $root_path); ?>" size="50" /><br /><small><em>For windows installations, use / instead of \</em></small></td>
		</tr>
		<tr>
			<td>Game URL:</td>
			<td><input class="text" name="config[REF_URL]" type="text" value="<?php _echo($_SESSION['config']['GAME']['REF_URL'], $root_url); ?>" size="50" /><br /><small><em>You can also use relative URLs (ex. /game instead of http://yourdomain/game)</em></small></td>
		</tr>
	</table>
</p>
<div class="separator">Misc information</div>
<p>
	<table>
		<tr>
			<td width="120">Game Title: </td>
			<td><input class="text" name="config[TITLE]" type="text" value="<?php _echo($_SESSION['config']['GAME']['TITLE'],'Glory Lands'); ?>" size="50" /></td>
		</tr>
		<tr>
			<td>Default Language: </td>
			<td><select class="text" name="config[LANG]" >
				<option value="en">English</option>
			</select></td>
		</tr>
	</table>
</p>
<p><input type="submit" class="button" value="Next >>" /></p>
</form>