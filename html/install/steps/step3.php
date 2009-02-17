<?php
$has_error = false;
$issues = array();
$root_path = dirname(dirname(__FILE__));

//echo "<pre>".print_r($_SERVER,true)."</pre>";

$root_url = 'http://'.$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['REQUEST_URI']));
?>
<form action="" method="post">
<input type="hidden" name="step" value="4" />
<input type="hidden" name="prev_step" value="3" />
<h2>Database connection</h2>
<p>In this step you will set up the database connection.</p>
<p>Make sure the MySQL user has CREATE, DROP, SHOW, 
<div class="separator">Connection Iformation</div>
<p>
	Theese fields holds the information that will be used by the game to connect with the MySQL Server
	<table>
		<tr>
			<td width="120">Server host: </td>
			<td><input class="text" name="config[HOST]" type="text" value="<?php _echo($_SESSION['config']['DB']['HOST'],'localhost'); ?>" size="20" /></td>
		</tr>
		<tr>
			<td width="120">Username: </td>
			<td><input class="text" name="config[USER]" type="text" value="<?php _echo($_SESSION['config']['DB']['USER'],''); ?>" size="20" /></td>
		</tr>
		<tr>
			<td width="120">Password: </td>
			<td><input class="text" name="config[PASSWORD]" type="password" value="<?php _echo($_SESSION['config']['DB']['PASSWORD'],''); ?>" size="20" /></td>
		</tr>
		<tr>
			<td width="120">Re-Type Password: </td>
			<td><input class="text" name="config[PASSWORD_CONFIRM]" type="password" value="<?php _echo($_SESSION['config']['DB']['PASSWORD'],''); ?>" size="20" /></td>
		</tr>		
	</table>
</p>
<div class="separator">MySQL Super-User Iformation</div>
<p>
	The following fields holds the MySQL super-user information that will be used by the installer to set up the databases automatically. The super-user must have create/drop database permissions on the server.<br />
	<em>(Leave those fields blank to use the same values as above)</em>
	<table>
		<tr>
			<td width="120">SU Username: </td>
			<td><input class="text" name="setupsql[USER]" type="text" value="<?php _echo($_SESSION['setupsql']['USER'],''); ?>" size="20" /></td>
		</tr>
		<tr>
			<td width="120">SU Password: </td>
			<td><input class="text" name="setupsql[PASSWORD]" type="password" value="<?php _echo($_SESSION['setupsql']['PASSWORD'],''); ?>" size="20" /></td>
		</tr>
		<tr>
			<td width="120">Re-Type Password: </td>
			<td><input class="text" name="setupsql[PASSWORD_CONFIRM]" type="password" value="<?php _echo($_SESSION['setupsql']['PASSWORD'],''); ?>" size="20" /></td>
		</tr>		
	</table>
</p>
<div class="separator">Database Information</div>
<p>
	<table>
		<tr>
			<td width="120">Database name: </td>
			<td><input class="text" name="config[DATABASE]" type="text" value="<?php _echo($_SESSION['config']['DB']['DATABASE'],'glorylands'); ?>" size="20" /></td>
		</tr>
	</table>
	<input checked="checked" id="inst_db" name="newdb" value="yes" type="checkbox"> <label for="inst_db">Create new database, or flush the previous one, if it already exists</label>
</p>
<p><input type="submit" class="button" value="Next >>" /></p>
</form>