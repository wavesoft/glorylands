<?php 
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Insert table structure</title>
<link rel="stylesheet" type="text/css" href="res/stylemain.css">
</head>
<body>

<?php
if (isset($_REQUEST['table']) && isset($_REQUEST['key'])) {

	// Find the auto increment
	$sql->query("SHOW COLUMNS FROM `".$_REQUEST['table']."` WHERE `extra` = 'auto_increment'");
	if ($sql->emptyResults) {
		$indexkey = '';
	} else {
		$indexkey = $sql->fetch_array(MYSQL_NUM);
		$indexkey = $indexkey[0];
	}

	$struct = '';
	foreach ($_REQUEST['entry'] as $keyv => $ack) {

		if ($struct!='') $struct.=";\n";

		$ans = $sql->query("SELECT * FROM `".$_REQUEST['table']."` WHERE `".$_REQUEST['key']."` = '".$keyv."'");
		if (!$ans) die($sql->getError());
		$row = $sql->fetch_array_fromresults($ans, MYSQL_ASSOC);
		
		if ($_REQUEST['mode'] == 'delete') {
			$struct .= "DELETE FROM `".$_REQUEST['table']."` WHERE ";
			$where='';
			if (isset($_REQUEST['indexdel'])) {
				$struct.="`{$indexkey}` = '".$row[$indexkey]."'";
			} else {
				foreach ($row as $key => $value) {
					if ((($key != $indexkey) || !isset($_REQUEST['nokey'])) && !isset($_REQUEST['ignore'][$key])) {
						if ($where!='') $where.=' AND ';
						$where .= "`$key` = '".$value."'";
					}
				}
				$struct.=$where;
			}
		} else {
			$struct .= "INSERT INTO `".$_REQUEST['table']."` \n";
			$names = '';
			$values = '';
			foreach ($row as $key => $value) {
					if ((($key != $indexkey) || !isset($_REQUEST['nokey'])) && !isset($_REQUEST['ignore'][$key])) {
					if ($names!='') $names.=',';
					$names.='`'.$key.'`';
					if ($values!='') $values.=',';
					$values.="'".mysql_escape_string($value)."'";
				}
			}
			$struct .= "($names) VALUES ($values)";
		}
		
	}
	
?>
<center>
<div class="centerblock" align="center">
<p>The data of row <?php echo $_REQUEST['key']." = ".$_REQUEST['keyv'] ?> of table <b><?php echo $_REQUEST['table']; ?></b> is now entered on your script.</b></p>
<p><a href="javascript:window.close();">Click here to close the window</a></p>
</div>
</center>
<script language="javascript">
window.opener.document.forms[0].elements[7].value += '<?php echo str_replace("\n","\\n\\\n",addslashes($struct)); ?>;'+"\n";
window.close();
</script>
<?php
} elseif (isset($_REQUEST['table'])) {
?>
<p>Please select the row to import from table <b><?php echo $_REQUEST['table']; ?></b>:</p>
<form action="" method="post">
<input type="hidden" name="table" value="<?php echo $_REQUEST['table']; ?>" />
<input type="hidden" name="mode" value="<?php echo $_REQUEST['mode']; ?>" />
<label for="nokey"><input id="nokey" type="checkbox" name="nokey" checked="checked" /> Do not include primery key</label>
<?php
	if ($_REQUEST['mode'] == 'delete') {
?>
 <label for="indexdel"><input id="indexdel" type="checkbox" name="indexdel" /> Delete using index</label>
<?php	
	}
?>
<?php
	$sql->query("SHOW INDEX FROM `".$_REQUEST['table']."`");
	$indexkey = $sql->fetch_array(MYSQL_ASSOC);
	$indexkey = $indexkey['Column_name'];
	$i=0;
?>
<input type="hidden" name="key" value="<?php echo $indexkey; ?>" />
<table class="filetable" style="width: 100%;">
<?php	
	$first = true;
	$keyvalue = '';
	$sql->query("SELECT * FROM `".$_REQUEST['table']."`");
	while ($row = $sql->fetch_array(MYSQL_ASSOC)) {
		$i++;
	
		// In case this is the first entry, display the header
		// (because we now know the colspan)
		if ($first) {
			$first = false;
?>
	<tr class="head">
		<td colspan="<?php echo sizeof($row)+1; ?>">Available databases</td>
	</tr>
	<tr class="folder">
		<td>&nbsp;</td>
<?php
			foreach ($row as $field => $value) {
?>
		<td><input type="checkbox" title="Ignore this field" name="ignore[<?php echo $field; ?>]" /><?php echo $field; ?></td>
<?php
			}
?>
	</tr>
<?php
		}

	$keyvalue = $row[$indexkey];
?>
	<tr>
		<td width="16"><input type="checkbox" name="entry[<?php echo $keyvalue; ?>]" id="e<?php echo $i; ?>" /></td>
<?php
		foreach ($row as $field => $value) {
?>
		<td><label for="e<?php echo $i; ?>"><?php echo $value; ?></label></td>
<?php
	}
?>
	</tr>
<?php
	}
?>
</table>
<input type="submit" value="Use theese values" />
</form>
<?php
} else {
?>
<p>Please select the table to read the data from:</p>
<table class="filetable" style="width: 100%;">
	<tr class="head">
		<td colspan="2">Available databases</td>
	</tr>
<?php
	$sql->query("SHOW TABLES");
	while ($row = $sql->fetch_array(MYSQL_NUM)) {
		$table = $row[0];
?>
	<tr>
		<td width="16"><img src="../images/db.gif" /></td>
		<td><a href="?table=<?php echo $table; ?>&mode=<?php echo $_REQUEST['mode']; ?>"><?php echo $table; ?></a></td>
	</tr>
<?php
	}
?>
</table>
<?php
}
?>
</body>
</html>
