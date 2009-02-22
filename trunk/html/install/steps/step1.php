<?php

// Reset session and try to load the last config
session_unset();
if (is_file("../config/config.php")) {
	include "../config/config.php";
	$_SESSION['config'] = $_CONFIG;
	$_SESSION['dbmode'] = 'patch';
}

$has_error = false;
$issues = array();
$root_path = dirname(dirname(dirname(__FILE__)));
?>
<h2>Pre-installation check</h2>
<p>This wizzard will help you install and configure the game for the first use. In this step, the installer checks for all the system requirements in order to be able to run this game.</p>
<div class="separator">PHP Compatibility</div>
<p>
	<table class="checks" width="400">
		<tr>
			<th width="140">PHP Version</th>
			<td>
				<?php 
					$ver = phpversion();
					$check = version_compare($ver, "5.2.1", ">=");
					if ($check) {
						echo "<span class=\"ok\">$ver</span>";
					} else {
						echo "<span class=\"error\">&lt; 5.2.1</span>";
						$issues[]='PHP Version 5.2.1 or later is required';
						$has_error = true;
					}
				?>
			</td>
		</tr>
		<tr>
			<th>SAPI Type</th>
			<td>
				<?php 
					$sapi_type = php_sapi_name();
					if (substr($sapi_type, 0, 3) == 'cgi') {
						echo "<span class=\"error\">{$sapi_type}</span>";
						$issues[]='It seems PHP is running as CGI Binary, probably for security reasons. This mode is not supported. Please contact your hosting provider to fix this issue.';
						$has_error = true;
					} else {
						echo "<span class=\"ok\">{$sapi_type}</span>";
						$ok = function_exists('apache_request_headers'); 
						if (!$ok) {
							echo "<span class=\"error\"> (Erroreus)</span>";
							$issues[]='PHP Is running as a module, but not all the functionality is provided! In detail, the function apache_request_headers() was not found declared.';
							$has_error = true;
						}
					}
				?>
			</td>
		</tr>
		<tr>
			<th>PHP Accelerator</th>
			<td>
				<?php 
					$acc='';
					$ok = extension_loaded('eAccelerator'); 
					if ($ok) $acc='eAccelerator';
					if (!$ok) {
						$ok = $ok || extension_loaded('APC'); 
						if ($ok) $acc='APC';
					}
					if (!$ok) {
						$ok = $ok || extension_loaded('XCache'); 
						if ($ok) $acc='XCache';
					}
					if ($ok) {
						echo "<span class=\"ok\">$acc</span>";
					} else {
						echo "<span class=\"warn\">Not detected</span>";
						$issues[]='No known PHP Accelerators werre detected. A PHP Accelerator is highly recommended, because it can grately improve the game performance.';
					}
				?>
			</td>
		</tr>
		<tr>
			<th>Extension - zLib</th>
			<td>
				<?php 
					$ok = extension_loaded('zlib'); 
					if ($ok) {
						echo "<span class=\"ok\">Exists</span>";
					} else {
						echo "<span class=\"error\">Missing</span>";
						$issues[]='zLib extension is missing. This extension must be installed in order to continue.';
						$has_error = true;
					}
				?>
			</td>
		</tr>
		<tr>
			<th>Extension - MBstring </th>
			<td>
				<?php 
					$ok = extension_loaded('mbstring'); 
					if ($ok) {
						echo "<span class=\"ok\">Exists</span>";
					} else {
						echo "<span class=\"error\">Missing</span>";
						$issues[]='Multibyte String extension is mission. This extension provies UNICODE support for the game. It must be installed in order to continue.';
						$has_error = true;
					}
				?>
			</td>
		</tr>
		<tr>
			<th>Extension - BZip2</th>
			<td>
				<?php 
					$ok = function_exists('bzdecompress');
					if ($ok) {
						echo "<span class=\"ok\">Exists</span>";
					} else {
						echo "<span class=\"warn\">Missing</span>";
						$issues[]='BZip2 extension is missing. This extension should be installed if you want to use .bz2 compressed game package archives. However you can continue the installation of the game.';
					}
				?>
			</td>
		</tr>
		<tr>
			<th>Extension - GDLib</th>
			<td>
				<?php 
					$ok = extension_loaded('gd'); 
					if ($ok) {
						echo "<span class=\"ok\">Exists</span>";
					} else {
						echo "<span class=\"error\">Missing</span>";
						$issues[]='GD library extension is missing. This extension must be installed in order to continue.';
						$has_error = true;
					}
				?>
			</td>
		</tr>
		<tr>
			<th>Extension - MySQL</th>
			<td>
				<?php 
					$ok = extension_loaded('mysql'); 
					if ($ok) {
						echo "<span class=\"ok\">Exists</span>";
					} else {
						echo "<span class=\"error\">Missing</span>";
						$issues[]='MySQL Extension is missing. This extension must be installed in order to continue.';
						$has_error = true;
					}
				?>
			</td>
		</tr>
		<tr>
			<th>Extension - XML</th>
			<td>
				<?php 
					$ok = function_exists('xml_parse_into_struct'); 
					if ($ok) {
						echo "<span class=\"ok\">Exists</span>";
					} else {
						echo "<span class=\"error\">Missing</span>";
						$issues[]='XML Parser Extension is missing. This extension must be installed in order to continue.';
						$has_error = true;
					}
				?>
			</td>
		</tr>
		<tr>
			<th>Extension - Memcache</th>
			<td>
				<?php 
					$ok = extension_loaded('memcache'); 
					if ($ok) {
						echo "<span class=\"ok\">Exists</span>";
					} else {
						echo "<span class=\"warn\">Missing</span>";
						$issues[]='MemCache extension not found. Memory caching will be disabled.';
					}
				?>
			</td>
		</tr>
	</table>
</p>
<div class="separator">Filesystem permissions</div>
<p>
	<table class="checks" width="400">
		<tr>
			<th width="270">Game Root</th>
			<td>
				<?php 						
					if (is_writable($root_path)) {
						echo "<span class=\"ok\">Writable</span>";
					} else {
						echo "<span class=\"error\">Not writable</span>";
						$issues[]='Directory $root_path must be writable in order to continue this setup.';
						$has_error = true;
					}
				?>
			</td>
		</tr>
		<tr>
			<th width="270">config/config.php</th>
			<td>
				<?php 						
					if (!file_exists($root_path.'/config/config.php')) {
						echo "<span class=\"ok\">Missing</span>";
					} elseif (is_writable($root_path.'/config/config.php')) {
						echo "<span class=\"ok\">Writable</span>";
					} else {
						echo "<span class=\"error\">Not writable</span>";
						$issues[]='File /config/config.php must be writable in order to continue this setup.';
						$has_error = true;
					}
				?>
			</td>
		</tr>
		<tr>
			<th width="120">admin/cache</th>
			<td>
				<?php 						
					if (is_writable($root_path.'/admin/cache')) {
						echo "<span class=\"ok\">Writable</span>";
					} else {
						echo "<span class=\"error\">Not writable</span>";
						$issues[]='Folder /admin/cache must be writable in order to continue this setup.';
						$has_error = true;
					}
				?>
			</td>
		</tr>
		<tr>
			<th width="120">admin/packages</th>
			<td>
				<?php 						
					if (is_writable($root_path.'/admin/packages')) {
						echo "<span class=\"ok\">Writable</span>";
					} else {
						echo "<span class=\"error\">Not writable</span>";
						$issues[]='Folder /admin/packages must be writable in order to continue this setup.';
						$has_error = true;
					}
				?>
			</td>
		</tr>
		<tr>
			<th width="120">admin/scripts</th>
			<td>
				<?php 						
					if (is_writable($root_path.'/admin/scripts')) {
						echo "<span class=\"ok\">Writable</span>";
					} else {
						echo "<span class=\"error\">Not writable</span>";
						$issues[]='Folder /admin/scripts must be writable in order to continue this setup.';
						$has_error = true;
					}
				?>
			</td>
		</tr>
		<tr>
			<th width="120">engine/outputprocessors/interfaces/cache</th>
			<td>
				<?php 						
					if (is_writable($root_path.'/engine/outputprocessors/interfaces/cache')) {
						echo "<span class=\"ok\">Writable</span>";
					} else {
						echo "<span class=\"error\">Not writable</span>";
						$issues[]='Folder /engine/outputprocessors/interfaces/cache must be writable in order to continue this setup.';
						$has_error = true;
					}
				?>
			</td>
		</tr>
		<tr>
			<th width="120">images</th>
			<td>
				<?php 						
					if (is_writable($root_path.'/images')) {
						echo "<span class=\"ok\">Writable</span>";
					} else {
						echo "<span class=\"error\">Not writable</span>";
						$issues[]='Folder /images must be writable in order to continue this setup.';
						$has_error = true;
					}
				?>
			</td>
		</tr>
		<tr>
			<th width="120">engine</th>
			<td>
				<?php 						
					if (is_writable($root_path.'/engine')) {
						echo "<span class=\"ok\">Writable</span>";
					} else {
						echo "<span class=\"error\">Not writable</span>";
						$issues[]='Folder /images/tiles must be writable in order to continue this setup.';
						$has_error = true;
					}
				?>
			</td>
		</tr>
		<tr>
			<th width="120">data</th>
			<td>
				<?php 						
					if (is_writable($root_path.'/data')) {
						echo "<span class=\"ok\">Writable</span>";
					} else {
						echo "<span class=\"error\">Not writable</span>";
						$issues[]='Folder /data must be writable in order to continue this setup.';
						$has_error = true;
					}
				?>
			</td>
		</tr>
	</table>
</p>
<div class="separator">Check results</div>
<?php
if (sizeof($issues)>0) {
	echo "<ul>\n";
	foreach ($issues as $issue) {
		echo "<li>$issue</li>\n";
	}
	echo "</ul>\n";
}
if ($has_error) {
	echo '<p>Errors where detected in the pre-installation check. Make sure to correct them before continuing</p><p><a href="" class="button">Repeat Checks</a></p>';
} else {
	echo '<p>Checks indicated that your system supports the game platform. You can proceed with the installation</p><p><a href="?step=2" class="button">Next >></a></p>';	
}
?>