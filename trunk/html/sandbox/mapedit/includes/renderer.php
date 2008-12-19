<?php

function render_grid($grid, $filename) {

}

function render_object($grid, $filename) {	
	
	// Load the grid
	$maxx=0;
	$maxy=0;
	foreach ($grid as $y => $xgrid) {
		foreach ($xgrid as $x => $element) {
			if ($x>$maxx) $maxx=$x;
		}
		if ($y>$maxy) $maxy=$y;
	}
		
	// Create map base
	$im = imagecreatetruecolor(($maxx+1)*32,($maxy+1)*32);
	$c = imagecolorallocatealpha($im, 255,255,255,127);
	imagecolortransparent($im, $c);
	imagefill($im, 0, 0, $c);
	
	// Render map
	foreach ($grid as $y => $grid_y) {
		foreach ($grid_y as $x => $image) {
			$r_im=false;
			$file_gif = substr($image,0,-3)."gif";
			$file_png = substr($image,0,-3)."png";
	
			if (file_exists("../../images/tiles/".$file_gif)) $r_im = imagecreatefromgif("../../images/tiles/".$file_gif);
			if (file_exists("../../images/tiles/".$file_png)) $r_im = imagecreatefrompng("../../images/tiles/".$file_png);
			if ($r_im) {
				$w = imagesx($r_im);
				$h = imagesy($r_im);
				imagecopyresized($im, $r_im, $x*32, $y*32, 0, 0, 32, 32, $w, $h);
				//echo "imagecopyresized($im, $r_im, ".($x*TILESIZE).", ".($y*TILESIZE).", 0, 0, ".TILESIZE.", ".TILESIZE.", $w, $h)\n";
				imagedestroy($r_im);
			}
		}
	}
	
	// Echo result
	imagepng($im, $filename);

}

?>