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
			<td><input class="text" name="config[BASE]" type="text" value="<?php _echo($_SESSION['config']['GAME']['BASE'], $root_path); ?>" size="50" /></td>
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
				<option <?php _selected($_SESSION['config']['GAME']['LANG'],'en',true); ?> value="en">English</option>
				<option <?php _selected($_SESSION['config']['GAME']['LANG'],'el',false); ?> value="el">Ελληνικά</option>
			</select></td>
		</tr>
	</table>
</p>
<div class="separator">Memory Caching (Beta)</div>
<?php
$memcache = extension_loaded('memcache');
$dis='';
if (!$memcache) {
	$dis='disabled="disabled"';
	echo "<p align=\"center\"><em>Memcache PHP extension is not installed/enabled</em></p>";
}
?>
<p>
	<table>
		<tr>
			<td colspan="2"><input <?=$dis?> type="checkbox" name="config[MC_ENABLE]" value="true" id="mc_en" /><label for="mc_en"> Enable distributed memory caching on MemCached server</label></td>
		</tr>
		<tr>
			<td width="120">Server Host:</td>
			<td><input <?=$dis?> class="text" name="config[MC_HOST]" type="text" value="<?php _echo($_SESSION['config']['GAME']['MC_HOST'],'localhost'); ?>" size="50" /></td>
		</tr>
		<tr>
			<td>Service Port:</td>
			<td><input <?=$dis?> class="text" name="config[MC_PORT]" type="text" value="<?php _echo($_SESSION['config']['GAME']['MC_PORT'],'11211'); ?>" size="50" /></td>
		</tr>
	</table>
</p>
<p><input type="submit" class="button" value="Next >>" /></p>
</form>