<h1>Installation is now completed</h1>
<p>You can now start using the game!</p>
<p>Log into the game from the <a href="..">Main Page</a> using the username <strong>admin</strong> and password <strong>admin</strong>, or create a new user account!</p>
<div class="separator">Additional Operations</div>
<p>
<ul>
<li>Now you should remove the <strong>/install</strong> directory to prevent any accidental re-installation of the game, that will cause data loss.</li>
<li>You should also make sure the MySQL user has only SELECT, INSERT, UPDATE and DELETE permissinos on the database.</li>
</ul>
</p>
<p>If you want, you can let the installer attempt to perofrm those steps, by clicking the following button:</p>
<form action="" method="post">
<ul>
<input type="hidden" name="step" value="6" />
<input type="hidden" name="prev_step" value="6" />
<li><input checked="checked" type="checkbox" name="finalize[sql]" value="true" id="fin_sql" /><label for="fin_sql"> Change the MySQL user permissions to SELECT, INSERT, UPDATE, DELETE only</label></li>
<li><input checked="checked" type="checkbox" name="finalize[files]" value="true" id="fin_files" /><label for="fin_files"> Make the configuration file non-writable</label></li>
</ul>
<p><input type="submit" class="button" value="Perform Finalizations" /></p>
</form>
