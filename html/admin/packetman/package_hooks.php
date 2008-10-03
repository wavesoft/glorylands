<?php 
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Manage package hooks</title>
<link rel="stylesheet" type="text/css" href="res/stylemain.css">
</head>
<body>
<?php

//echo "<pre>".print_r($_REQUEST,true)."</pre>";

$guid = $_REQUEST['guid'];
$ans = $sql->query("SELECT * FROM `system_packages` WHERE `guid` = '{$guid}'");
if (!$ans) die($sql->getError());
if ($sql->emptyResults) die("Cannot find package with guid {$guid}");

$row = $sql->fetch_array();
$pid = $row['index'];

if ($_REQUEST['a'] == 'add_data') {
	
	// Check if we should add or update the entry
	$addrow = true;
	if (isset($_REQUEST['h_index'])) {
		if ($_REQUEST['h_index']!='') {
			$index = (int) $_REQUEST['h_index']; /* Exploit protection */
			$package = $sql->query_and_get_value("SELECT `package` FROM `system_hooks` WHERE `index` = $index");
			$addrow = ($package != 0);
		}
	}

	// Insert new or update value
	if ($addrow) {
		$sql->addRow('system_hooks', array(
			'hook' => $_REQUEST['h_hook'],
			'filename' => $_REQUEST['h_file'],
			'function' => $_REQUEST['h_function'],
			'active' => 'YES',
			'package' => $pid
		));
	} else {
		$sql->editRow('system_hooks', '`index` = '.$index, array(
			'hook' => $_REQUEST['h_hook'],
			'filename' => $_REQUEST['h_file'],
			'function' => $_REQUEST['h_function'],
			'active' => 'YES',
			'package' => $pid
		));
	}

	?>
	<center>
	<div class="centerblock" align="center">
	<p>A new hook that handles named <b><?php echo $_REQUEST['h_hook']; ?></b> established on the package <b><?php echo $row['name']; ?></b></p>
	<p><a href="packagemanifest.php?guid=<?php echo $guid; ?>">Click here to go back to the manifest management</a></p>
	</div>
	</center>
	<?php
	
}else if ($_REQUEST['a'] == 'delete') {

	?>
	<center>
	<div class="centerblock" align="center">
	<p>The following hooks are removed from the package:<ul>
	<?php
		foreach ($_REQUEST['hook'] as $index => $ack) {
			$sql->query("SELECT * FROM `system_hooks` WHERE `index` = $index");
			$info = $sql->fetch_array();
			$sql->query("DELETE FROM `system_hooks` WHERE `index` = $index");
			echo "<li><b>{$info['hook']}</b> handled by <em>{$info['function']}()</em> on <em>{$info['filename']}</em></li>";
		}
	?>
	</ul></p>
	<p><a href="packagemanifest.php?guid=<?php echo $guid; ?>">Click here to go back to the manifest management</a></p>
	</div>
	</center>
	<?php

} elseif ($_REQUEST['a'] == 'add') {

	// Search for currently declared system events
	if (isset($_SESSION[TEMP]['event_cache']) && !isset($_REQUEST['flushevents'])) {
		$events = $_SESSION[TEMP]['event_cache'];
	} else {
		$events = array();
		function search_events($basedir) {
			global $events;
			$d = dir($basedir);
			while (false !== ($entry = $d->read())) {
				if (substr($entry,0,1)!='.') {
					$f = $basedir.'/'.$entry;
					if (is_dir($f)) {
						search_events($f);
					} else {
						if (substr($f,-4)=='.php') {
							$buf = file_get_contents($f);
							preg_match_all("/callEvent\(\'([\w.]+)\'/", $buf, $matches, PREG_SET_ORDER);
							foreach ($matches as $val) {
								array_push($events, strtolower(trim($val[1])));
							}
						}
					}
				}
			}
			$d->close();
		}
		search_events($_CONFIG[GAME][BASE]);
		array_unique($events);
		sort($events);
		
		$_SESSION[TEMP]['event_cache'] = $events;
	}

?>
<table width="100%">
<tr>
	<td width="35" rowspan="2" valign="top"><img src="../images/hook32.gif" align="absmiddle" /></td>
	<td align="left"><font size="+2"><b> <?php echo $row['name']; ?></b></font></td>
</tr>
<tr>
	<td align="left">Create new hook for this package</td>
</tr>
<tr>
	<td colspan="2">
	
	<p>
	<fieldset><legend>Create a custom hook</legend>
	<form action="" method="post">
	<input type="hidden" name="a" value="add_data" />
	<table>
		<tr>
			<td><b>Hook Function:</b></td>
			<td><input size="32" type="text" name="h_function" /></td>
		</tr>
		<tr>
			<td><b>Hook Filename:</b></td>
			<td>
			<select name="h_file">
			<?php
			$d = dir(DIROF('DATA.HOOK',true));
			while (false !== ($entry = $d->read())) {
				if (substr($entry,0,1)!='.') echo "<option name=\"$entry\">$entry</option>\n";
			}
			$d->close();
 			?>
			</select>
			</td>
		</tr>
		<tr>
			<td><b>Handles system event:</b></td>
			<td>
			<select name="h_hook">
			<?php 
			foreach ($events as $event) {
				echo "<option value=\"{$event}\">{$event}</option>\n";
			}
			?>
			</select>
			</td>
		</tr>
	</table>
	<input type="submit" value="Add Hook" /> <input type="button" value="Update event list" onclick="window.location='?a=add&guid=<?php echo $guid; ?>&flushevents=1';" />
	</form>
	</fieldset>
	</p>

	<p>
	<fieldset><legend>Select one of the pre existing handled hooks</legend>
	<table width="100%" class="filetable" cellpadding="1" cellspacing="1">
	<?php
	foreach ($EventChain as $event => $handlers) {
?>
<tr class="folder">
	<td colspan="2"><?php echo $event; ?></td>
</tr>
<?
		foreach ($handlers as $handler) {
					
			// Get event index from SQL
			$index = $sql->query_and_get_value("SELECT `index` FROM `system_hooks` WHERE 
			  `hook` = '{$event}' AND `filename` = '{$handler[0]}' AND `function` = '{$handler[1]}'
			");
?>
<tr>
	<td width="16"><img src="../images/file.gif" /></td>
	<td><a href="?a=add_data&guid=<?php echo $guid; ?>&h_file=<?php echo urlencode($handler[0]); ?>&h_function=<?php echo urlencode($handler[1]); ?>&h_hook=<?php echo urlencode($event); ?>&h_index=<?php echo urlencode($index); ?>"><em><b><?php echo $handler[1]; ?>()</b></em> on <em><?php echo $handler[0]; ?></em></a></td>
</tr>
<?			
		}
	}
	?>
	</table>
	</fieldset>
	</p>
		
	</td>
</tr>
</table>
<br />
<input type="button" value="&lt;&lt; Back" onclick="window.location='packagemanifest.php?guid=<?php echo $guid; ?>';" />
<?php
}else if ($_REQUEST['a'] == 'edit') {
?>
<form action="" method="post">
<input type="hidden" name="a" value="edit_data" />
<input type="hidden" name="guid" value="<?php echo $guid; ?>" />
<table>
<tr>
	<td width="35" rowspan="2" valign="top"><img src="../images/hook32.gif" align="absmiddle" /></td>
	<td align="left"><font size="+2"><b> <?php echo $row['name']; ?></b></font></td>
</tr>
<tr>
	<td align="left">Create new hook for this package</td>
</tr>
<tr>
	<td colspan="2">	
<?php
	foreach ($_REQUEST['hook'] as $index => $value) {
		$sql->query("SELECT * FROM `system_hooks` WHERE `index` = $index");
		$info = $sql->fetch_array();
?>
	<p>
	<fieldset><legend>Edit hook <b><?php echo $info['hook']; ?></b></legend>
	<table>
		<tr>
			<td><b>Hook Function:</b></td>
			<td><input size="32" type="text" name="h_function[<?php echo $index; ?>]" value="<?php echo $info['function']; ?>" /></td>
		</tr>
		<tr>
			<td><b>Hook Filename:</b></td>
			<td><input size="32" type="text" name="h_file[<?php echo $index; ?>]" value="<?php echo $info['filename']; ?>" /></td>
		</tr>
		<tr>
			<td><b>Handles system event:</b></td>
			<td><input size="32" type="text" name="h_hook[<?php echo $index; ?>]" value="<?php echo $info['hook']; ?>" /></td>
		</tr>
	</table>
	</fieldset>
	</p>
<?php
	}
?>
	</td>
</tr>
</table>
<input type="button" value="&lt;&lt; Back" onclick="window.location='packagemanifest.php?guid=<?php echo $guid; ?>';" /> <input type="submit" value="Update Hook" />
</form>
<?php
}else if ($_REQUEST['a'] == 'edit_data') {
	$changed = 0;
	foreach ($_POST['h_function'] as $index => $function) {
		$function = mysql_escape_string($function);
		$file = mysql_escape_string($_POST['h_file'][$index]);
		$hook = mysql_escape_string($_POST['h_hook'][$index]);
		$ans=$sql->query("UPDATE `system_hooks` SET `function` = '{$function}', `filename` = '{$file}', `hook` = '{$hook}' WHERE `index` = $index");
		if (!$ans) die($sql->getError());
		$changed+=$sql->affectedRows;
	}
	?>
	<center>
	<div class="centerblock" align="center">
	<p><b><?php echo $changed; ?></b> hooks are updated on the plugin <b><?php echo $row['name']; ?></b></p>
	<p><a href="packagemanifest.php?guid=<?php echo $guid; ?>">Click here to go back to the manifest management</a></p>
	</div>
	</center>
<?php
} 
?>
</body>
</html>
