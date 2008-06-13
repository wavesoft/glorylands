<?php
include "../config/config.php";
include "../engine/includes/base.php";

if (isset($_REQUEST['scale'])) {
	define('TILESIZE', $_REQUEST['scale']);
} else {
	define('TILESIZE', 32);
}

$map = $_REQUEST['map'];
if (!$map) die('Specify a map file!');

if (substr($map,0,1)=='_') {
	$map = $_CONFIG[GAME][BASE]."/admin/cache/".substr($map,1);
} else {
	$map = "../data/maps/{$map}.jmap";
}
if (!file_exists($map)) die("Filename {$map} does not exist!");

$grid = json_decode(file_get_contents($map),true);

// Load images
$images = array();
foreach ($grid['dic'] as $file => $entry) {
	$im = imagecreatefromgif("../images/tiles/{$file}");
	if ($im) {
		$images[$entry] = $im;
	}
}

// Get extends
$maxx = $grid['range']['x']['M'];
$maxy = $grid['range']['y']['M'];

// Create map base
$im = imagecreatetruecolor(($maxx+1)*TILESIZE,($maxy+1)*TILESIZE);
$c = imagecolorallocatealpha($im, 255,255,255,127);
imagecolortransparent($im, $c);
imagefill($im, 0, 0, $c);

// Render map
foreach ($grid['grid'] as $y => $grid_y) {
	foreach ($grid_y as $x => $grid_images) {
		foreach ($grid_images as $level => $image) {
			$w = imagesx($images[$image]);
			$h = imagesy($images[$image]);
			imagecopyresized($im, $images[$image], $x*TILESIZE, $y*TILESIZE, 0, 0, TILESIZE, TILESIZE, $w, $h);
		}
	}
}

// Echo result
header('Content-type: image/jpeg');
imagejpeg($im);

?>