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

	// Update value in case of dynamic value
	$group = strtoupper($_REQUEST['d_group']);
	$value = $_REQUEST['d_value'];
	$mode = 'FIXED';
	if (isset($_REQUEST['d_dynamic'])) {
		$mode='DYNAMIC';
		$last = $sql->query_and_get_value("SELECT MAX(`value`) FROM `system_dictionaries` WHERE `group` = '{$group}'");
		if ($last=='') $last=0;
		$last++;
		$value = $last;
	}
	
	// Check if we should add or update the entry
	$addrow = true;
	if (isset($_REQUEST['d_index'])) {
		if ($_REQUEST['d_index']!='') {
			$index = (int) $_REQUEST['d_index']; /* Exploit protection */
			$package = $sql->query_and_get_value("SELECT `package` FROM `system_dictionaries` WHERE `index` = $index");
			$addrow = ($package != 0);
		}
	}
	
	// Perform insert or update
	if ($addrow) {
		$sql->addRow('system_dictionaries', array(
			'group' => $group,
			'name' => $_REQUEST['d_variable'],
			'value' => $value,
			'mode' => $mode,
			'package' => $pid
		));
	} else {
		$sql->editRow('system_dictionaries', '`index` = '.$index, array(
			'group' => $group,
			'name' => $_REQUEST['d_variable'],
			'value' => $value,
			'mode' => $mode,
			'package' => $pid
		));	
	}

	?>
	<center>
	<div class="centerblock" align="center">
	<p>The new dictionary entry <b><?php echo $_REQUEST['d_variable']; ?></b> = <em>'<?php echo $value; ?>'</em> on group named <b><?php echo $group; ?></b> is no included on the package <b><?php echo $row['name']; ?></b></p>
	<p><a href="packagemanifest.php?guid=<?php echo $guid; ?>">Click here to go back to the manifest management</a></p>
	</div>
	</center>
	<?php
	
}else if ($_REQUEST['a'] == 'delete') {

	?>
	<center>
	<div class="centerblock" align="center">
	<p>The following dictionary entries are removed from the package:<ul>
	<?php
		foreach ($_REQUEST['entry'] as $index => $ack) {
			$sql->query("SELECT * FROM `system_dictionaries` WHERE `index` = $index");
			$info = $sql->fetch_array();
			$sql->query("DELETE FROM `system_dictionaries` WHERE `index` = $index");
			echo "<li><b>{$info['name']}</b> = '<em>{$info['value']}</em>' on the group <em>{$info['group']}</em></li>\n";
		}
	?>
	</ul></p>
	<p><a href="packagemanifest.php?guid=<?php echo $guid; ?>">Click here to go back to the manifest management</a></p>
	</div>
	</center>
	<?php

} elseif ($_REQUEST['a'] == 'add') {

?>
<table width="100%">
<tr>
	<td width="35" rowspan="2" valign="top"><img src="../images/dictionary32.gif" align="absmiddle" /></td>
	<td align="left"><font size="+2"><b> <?php echo $row['name']; ?></b></font></td>
</tr>
<tr>
	<td align="left">Create new dictionary entry for this package</td>
</tr>
<tr>
	<td colspan="2">
	
	<p>
	<fieldset><legend>Create new entry</legend>
	<form action="" method="post">
	<input type="hidden" name="a" value="add_data" />
	<table>
		<tr>
			<td><b>Dictionary Group:</b></td>
			<td>
			<input type="text" name="d_group" id="d_group_text" /> &lt;&lt;
			</td>
			<td>
			<select onchange="this.form.d_group_text.value=this.value">
				<option value="" selected="selected">(Custom)</option>
				<option disabled="disabled">------------------------------</option>
				<option value="GUID">GUID Type Association</option>
			</select>
			</td>
		</tr>
		<tr>
			<td><b>Entry Name:</b></td>
			<td colspan="2">
			<input type="text" name="d_variable" />
			</td>
		</tr>
		<tr>
			<td><b>Entry Value:</b></td>
			<td><input type="text" name="d_value" /> <input type="checkbox" name="d_dynamic" id="d_dynamic" /></td>
			<td valign="middle">
			<label for="d_dynamic"> Allocate the first available value</label>
			</td>
		</tr>
	</table>
	<input type="submit" value="Add Dictionary Entry" />
	</form>
	</fieldset>
	</p>

	<p>
	<fieldset><legend>Select one of the pre existing dictionary entries</legend>
	<table width="100%" class="filetable" cellpadding="1" cellspacing="1">
	<?php
	
	$groups = array();
	$ans = $sql->query("SELECT * FROM `system_dictionaries`");
	while ($info = $sql->fetch_array(MYSQL_ASSOC)) {
		if (!isset($groups[$info['group']])) {
			$groups[$info['group']] = array($info);
		} else {
			array_push($groups[$info['group']], $info);
		}
	}
	
	foreach ($groups as $group => $items) {
?>
<tr class="folder">
	<td colspan="2"><?php echo $group; ?></td>
</tr>
<?
		foreach ($items as $info) {
			$dynamic = '';
			if ($info['mode'] == 'DYNAMIC') {
				$dynamic = '&d_dynamic=yes';
			}
?>
<tr>
	<td width="16"><img src="../images/comment.gif" /></td>
	<td><a href="?a=add_data&guid=<?php echo $guid; ?>&d_variable=<?php echo urlencode($info['name']); ?>&d_value=<?php echo urlencode($info['value']); ?>&d_group=<?php echo urlencode($info['group']); ?>&d_index=<?php echo urlencode($info['index']); ?><?php echo $dynamic ?>"><b><?php echo $info['name'] ?></b> = '<em><?php echo $info['value'] ?></em>'</a></td>
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
	<td width="35" rowspan="2" valign="top"><img src="../images/dictionary32.gif" align="absmiddle" /></td>
	<td align="left"><font size="+2"><b> <?php echo $row['name']; ?></b></font></td>
</tr>
<tr>
	<td align="left">Create new dictionary entry for this package</td>
</tr>
<tr>
	<td colspan="2">	
<?php
	foreach ($_REQUEST['entry'] as $index => $value) {
		$sql->query("SELECT * FROM `system_dictionaries` WHERE `index` = $index");
		$info = $sql->fetch_array();
?>
	<p>
	<fieldset><legend>Edit dictionary entry <b><?php echo $info['name']; ?></b> = '<em><?php echo $info['value']; ?></em>'</legend>
	<table>
		<tr>
			<td><b>Dictionary Group:</b></td>
			<td>
			<input type="text" name="d_group[<?php echo $info['index'] ?>]" id="d_group_text" value="<?php echo $info['group']; ?>" /> &lt;&lt;
			</td>
			<td>
			<select onchange="this.form.d_group_text.value=this.value">
				<option value="" selected="selected">(Custom)</option>
				<option disabled="disabled">------------------------------</option>
				<option value="GUID">GUID Type Association</option>
			</select>
			</td>
		</tr>
		<tr>
			<td><b>Entry Name:</b></td>
			<td colspan="2">
			<input value="<?php echo $info['name']; ?>" type="text" name="d_variable[<?php echo $info['index'] ?>]" />
			</td>
		</tr>
		<tr>
			<td><b>Entry Value:</b></td>
			<td><input value="<?php echo $info['value']; ?>"  type="text" name="d_value[<?php echo $info['index'] ?>]" /> <input type="checkbox" name="d_dynamic[<?php echo $info['index'] ?>]" id="d_dynamic"<?php if ($info['mode']=='DYNAMIC') echo " checked=\"checked\""; ?> /></td>
			<td valign="middle">
			<label for="d_dynamic"> Allocate the first available value</label>
			</td>
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
<input type="button" value="&lt;&lt; Back" onclick="window.location='packagemanifest.php?guid=<?php echo $guid; ?>';" /> <input type="submit" value="Update Dictionary" />
</form>
<?php
}else if ($_REQUEST['a'] == 'edit_data') {
	$changed = 0;
	foreach ($_POST['d_group'] as $index => $group) {
	
		// Update value in case of dynamic value
		$group = strtoupper($_REQUEST['d_group'][$index]);
		$value = $_REQUEST['d_value'][$index];
		$mode = 'FIXED';
		if (isset($_REQUEST['d_dynamic'][$index])) {
			$mode='DYNAMIC';
			
			// Do not update the value if the entry is already dynamic
			if ($sql->poll("SELECT `index` FROM `system_dictionaries` WHERE `index` = $index AND `mode` = 'DYNAMIC'")) {
				$value = $sql->query_and_get_value("SELECT `value` FROM `system_dictionaries` WHERE `index` = $index");
			} else {
				$last = $sql->query_and_get_value("SELECT MAX(`value`) FROM `system_dictionaries` WHERE `group` = '{$group}'");
				if ($last=='') $last=0;
				$last++;
				$value = $last;
			}
		}

		$ans=$sql->editRow('system_dictionaries', "`index` = $index", array(
			'group' => $group,
			'name' => $_REQUEST['d_variable'][$index],
			'value' => $value,
			'mode' => $mode,
			'package' => $pid
		));
		if (!$ans) die($sql->getError());
		$changed+=$sql->affectedRows;
	}
	?>
	<center>
	<div class="centerblock" align="center">
	<p><b><?php echo $changed; ?></b> dictionary entries are updated on the plugin <b><?php echo $row['name']; ?></b></p>
	<p><a href="packagemanifest.php?guid=<?php echo $guid; ?>">Click here to go back to the manifest management</a></p>
	</div>
	</center>
<?php
} 
?>
</body>
</html>
