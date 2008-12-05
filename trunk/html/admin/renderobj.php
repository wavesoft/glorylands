<?php
include "../config/config.php";
include "../engine/includes/base.php";

if (isset($_REQUEST['scale'])) {
	define('TILESIZE', $_REQUEST['scale']);
} else {
	define('TILESIZE', 32);
}

$obj = $_REQUEST['obj'];
if (!$obj) die('Specify an object file!');

if (substr($map,0,1)=='_') {
	$obj = $_CONFIG[GAME][BASE]."/admin/cache/".substr($obj,1);
} else {
	$obj = "../data/models/{$obj}.o";
}
if (!file_exists($obj)) die("Filename {$obj} does not exist!");

// Load the grid
$maxx=1;
$maxy=0;
$f = fopen($obj,"r");
$center = fgets($f);
$grid=array();
while (!feof($f)) {
	$row = fgets($f);
	$row = str_replace("\n","",$row);
	$row = str_replace("\r","",$row);
	
	$line = explode(",",$row);
	if (sizeof($line)>$maxx) $maxx=sizeof($line);
	$grid[]=$line;
	$maxy++;
}
fclose($f);

// Create map base
$im = imagecreatetruecolor(($maxx+1)*TILESIZE,($maxy+1)*TILESIZE);
$c = imagecolorallocatealpha($im, 255,255,255,127);
imagecolortransparent($im, $c);
imagefill($im, 0, 0, $c);

// Render map
foreach ($grid as $y => $grid_y) {
	foreach ($grid_y as $x => $image) {
		$r_im=false;
		$file_gif = substr($image,0,-3)."gif";
		$file_png = substr($image,0,-3)."png";

		if (file_exists("../images/tiles/".$file_gif)) $r_im = imagecreatefromgif("../images/tiles/".$file_gif);
		if (file_exists("../images/tiles/".$file_png)) $r_im = imagecreatefrompng("../images/tiles/".$file_png);
		if ($r_im) {
			$w = imagesx($r_im);
			$h = imagesy($r_im);
			imagecopyresized($im, $r_im, $x*TILESIZE, $y*TILESIZE, 0, 0, TILESIZE, TILESIZE, $w, $h);
			//echo "imagecopyresized($im, $r_im, ".($x*TILESIZE).", ".($y*TILESIZE).", 0, 0, ".TILESIZE.", ".TILESIZE.", $w, $h)\n";
			imagedestroy($r_im);
		}
	}
}

// Echo result
header('Content-type: image/png');
imagepng($im);

?>