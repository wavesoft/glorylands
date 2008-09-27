<?php 
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
include "scripts/packetman.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Disable Package</title>
<link rel="stylesheet" type="text/css" href="res/stylemain.css">
</head>
<body>
<?php


$guid = $_REQUEST['guid'];
$ans = $sql->query("SELECT * FROM `system_packages` WHERE `guid` = '{$guid}'");
if (!$ans) die($sql->getError());
if ($sql->emptyResults) die("Cannot find package with guid {$guid}");

$row = $sql->fetch_array();
$pid = $row['index'];

packet_archive_files($pid, DIROF('SYSTEM.ADMIN').'cache', true, true);
packet_restore_files($pid, DIROF('SYSTEM.ADMIN').'cache', true);
?>
</body>
</html>
