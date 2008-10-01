<?php 
// (Disable GZip output)
define('NOZIP',true);
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
include "scripts/packetman.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Delete Package</title>
<link rel="stylesheet" type="text/css" href="res/stylemain.css">
</head>
<body>
<pre>
<?php

$guid = $_REQUEST['guid'];
$ans = $sql->query("SELECT * FROM `system_packages` WHERE `guid` = '{$guid}'");
if (!$ans) die($sql->getError());
if ($sql->emptyResults) die("Cannot find package with guid {$guid}");

$row = $sql->fetch_array();
$pid = $row['index'];

if (!isset($_REQUEST['ack'])) {
	
	$_SESSION[DATA][TEMP]['del_ack'] = md5(time().rand(0,1000));
	echo '<center style="background-color:#FFBBBB; border: solid 2px #FF0000; padding: 5px;">';
	echo "You are about to remove the package <b>".$row['name']."</b>. This action is undoable! Are you sure?\n";
	echo "<input type=\"button\" onclick=\"window.location='?ack=".$_SESSION[DATA][TEMP]['del_ack']."&guid=".$guid."';\" value=\"CONFIRM PACKAGE REMOVAL\" />";
	echo '</center>';

} else {

	// Find package directories
	$root = DIROF('SYSTEM.ADMIN').'/packages/'.$guid;
	if (!is_dir($root)) mkdir($root);
	$dest = $root.'/disabled';
	if (!is_dir($dest)) mkdir($dest);
	$scripts = $root.'/scripts';
	if (!is_dir($scripts)) mkdir($scripts);
	

	$ack = $_REQUEST['ack'];
	if ($ack == $_SESSION[DATA][TEMP]['del_ack']) {	
		unset($_SESSION[DATA][TEMP]['del_ack']);
		
		echo "Starting uninstallation of packet <b>".$row['name']."</b>...\n\n";

		echo "Executing uninstallation scripts...";
		package_run_uninstall($pid, $scripts);
		echo "<font color=\"green\">ok</font>\n";
		
		echo "Removing files...";
		package_uninstall_files($pid);
		echo "<font color=\"green\">ok</font>\n";

		echo "Removing sql entries...";
		package_uninstall_db($pid);
		echo "<font color=\"green\">ok</font>\n";

		echo "Removing local cache...";
		package_clear_dir($root);
		rmdir($root);
		echo "<font color=\"green\">ok</font>\n";

		echo "\nUninstallation completed successfully!";		
		
		// Javascript: Update left bar
		?>
		<form action="navbar.php" target="left">
		<input type="hidden" name="rand" value="<?php echo md5(time().rand(0,100)); ?>" />
		<input type="hidden" name="guid" value="" />
		</form>
		<script language="javascript">
		document.forms[0].submit();
		</script>
		<?php		
	
	} else {
		unset($_SESSION[DATA][TEMP]['del_ack']);
		echo "Unacknowledged uninstallation of packet <b>".$row['name']."</b>\n<font color=\"red\">Uninstallation aborted</font>\n\n";
	}

}
?>
</pre>
<input type="button" value="&lt;&lt; Back" onclick="window.location='home.php'" />
</body>
</html>
