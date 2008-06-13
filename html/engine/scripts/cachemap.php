<?php
include "../../config/config.php";

function mapLoadFile($f) {
	$layers = array(
	
		// Layer1
		array(
			'castle-ext-0-0.gif', 'castle-ext-1-0.gif', 'castle-ext-2-0.gif', 'castle-ext-3-0.gif', 'castle-ext-4-0.gif', 'castle-ext-5-0.gif', 'castle-ext-6-0.gif', 'castle-ext-7-0.gif'
		),

		// Layer2
		array(
			'castle-ext-0-1.gif', 'castle-ext-1-1.gif', 'castle-ext-2-1.gif', 'castle-ext-3-1.gif', 'castle-ext-4-1.gif', 'castle-ext-5-1.gif', 'castle-ext-6-1.gif', 'castle-ext-7-1.gif'
		),

		// Layer3
		array(
			'castle-ext-0-2.gif', 'castle-ext-1-2.gif', 'castle-ext-2-2.gif', 'castle-ext-3-2.gif', 'castle-ext-4-2.gif', 'castle-ext-5-2.gif', 'castle-ext-6-2.gif', 'castle-ext-7-2.gif'
		)
	
	);
	return array("back"=>'castle-ext-0-0.gif', "layers"=>$layers, "width"=>4, "height"=>2);
}

$img = imagecreatetruecolor(512,512); // 512x512 image chunks
imagealphablending($img,true);

$map = mapLoadFile($_REQUEST['map']);
print_r($map);

$i_back = imagecreatefromgif(DIROF('IMAGE.TILE').$map['back']);

// Background
/*
for ($x=0; $x<$map['width']; $x++) {
	for ($y=0; $y<$map['height']; $y++) {
		imagecopy($img,$i_back,$x*32,$y*32,0,0,32,32);
	}
}
*/

// Layers
foreach ($map['layers'] as $layer) {

	for ($x=0; $x<$map['width']; $x++) {
		for ($y=0; $y<$map['height']; $y++) {
			$id = ($y*$map['width'])+$y;

			$tile = imagecreatefromgif(DIROF('IMAGE.TILE').$layer[$id]);
			imagecopy($img,$tile,$x*32,$y*32,0,0,32,32);
			imagedestroy($tile);
		}
	}
}

imagejpeg($img, DIROF('DATA.IMAGE')."mapcache/00000.jpg",80);
?>