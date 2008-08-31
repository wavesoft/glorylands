<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Untitled Document</title>
</head>

<body>
<?php
$grid = unserialize(stripslashes($_REQUEST['data']));
$name = $_REQUEST['objname'];

$f=fopen('../../data/models/b_'.$name.'.o','w');
$maxx=0; $maxy=0; $vx=0; $vy=0;
$dgrid='';
foreach ($grid as $y => $frow) {
	if ($dgrid!='') $dgrid.="\n";
	$row='';
	$vx=0;
	foreach ($frow as $x => $img) {
		if ($row!='') $row.=',';
		$row.=$img;
		$vx++;
		if ($vx>$maxx) $maxx=$vx;
	}
	$vy++;
	if ($vy>$maxy) $maxy=$vy;
	$dgrid.=$row;
}
//echo "$maxx,$maxy";
fwrite($f,(ceil($maxx/2)-1).",".($maxy-1)."\n");
fwrite($f, $dgrid."\n");
fclose($f);

?>
Completed!
<p><a href="render.php">Add New</a></p>
</body>
</html>
