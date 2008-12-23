<?php

function slice_grid($base, $width, $height) {
	$im = imagecreatefrompng($base.'-0-0.png');
	
	imagedestroy($im);
}

function render_grid($data, $filename, $background, $width, $height) {
	
	$im = imagecreatetruecolor(($width+1)*32,($height+1)*32);
	$c = imagecolorallocatealpha($im, 255,255,255,0);
	imagefill($im, 0, 0, $c);
	
	// Get background
	$bim = imagecreatefrompng('../../images/tiles/'.$background);
	
	// Render the background
	for ($y=0; $y<=$height; $y++) {
		for ($x=0; $x<=$width; $x++) {
			imagecopy($im, $bim, $x*32, $y*32, 0, 0, 32, 32);
		}
	}
	
	// We are done with background
	imagedestroy($bim);
		
	// Render the image
	$cache = array();
	foreach ($data as $gid => $grid) {
		foreach ($grid as $eid => $element) {	

			$image = $element['s'];
			$x = $element['x'];
			$y = $element['y'];
		
			if (isset($cache[$image])) {
				$cim = $cache[$image];
			} else {
				$cim = imagecreatefrompng('../../images/tiles/'.$image);
				$cache[$image] = $cim;
			}
						
			imagecopy($im, $cim, $x*32, $y*32, 0, 0, 32, 32);
		}
	}

	// Destroy cache
	foreach ($cache as $cim) {
		imagedestroy($cim);
	}
	
	// Save image
	imagepng($im, $filename);
	
	// Destroy the image
	imagedestroy($im);
	
	// What? You need more? :P
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

function render_fegion_object($grid, $filename) {	
	
	// Load the grid
	$maxx=0;
	$maxy=0;
	$minx=false;
	$miny=false;
	foreach ($grid as $l => $xygrid) {
		foreach ($xygrid as $y => $xgrid) {
			foreach ($xgrid as $x => $element) {
				if ($x>$maxx) $maxx=$x;
				if ($element) {
					if (($minx===false) || ($x<$minx)) 	$minx = $x;					
					if (($miny===false) || ($y<$miny)) 	$miny = $y;
				}
			}
			if ($y>$maxy) $maxy=$y;
		}
	}
		
	// Create map base
	$im = imagecreatetruecolor(($maxx-$minx+1)*32,($maxy-$miny+1)*32);
	$c = imagecolorallocatealpha($im, 255,255,255,127);
	imagecolortransparent($im, $c);
	imagefill($im, 0, 0, $c);
	
	// Render map
	foreach ($grid as $l => $xygrid) {
		if (is_array($xygrid))
		foreach ($xygrid as $y => $grid_y) {
			if (is_array($grid_y))
			foreach ($grid_y as $x => $image) {	
				if ($image && ($x>=$minx) && ($y>=$miny)) {
					$r_im=false;
					$image = basename($image);
					$file_gif = substr($image,0,-3)."gif";
					$file_png = substr($image,0,-3)."png";
			
					if (file_exists("../../images/tiles/".$file_gif)) $r_im = imagecreatefromgif("../../images/tiles/".$file_gif);
					if (file_exists("../../images/tiles/".$file_png)) $r_im = imagecreatefrompng("../../images/tiles/".$file_png);
					if ($r_im) {
						$w = imagesx($r_im);
						$h = imagesy($r_im);
						imagecopyresized($im, $r_im, ($x-$minx)*32, ($y-$miny)*32, 0, 0, 32, 32, $w, $h);
						//echo "imagecopyresized($im, $r_im, ".($x*TILESIZE).", ".($y*TILESIZE).", 0, 0, ".TILESIZE.", ".TILESIZE.", $w, $h)\n";
						imagedestroy($r_im);
					}
				}
			}
		}
	}
	
	// Echo result
	imagepng($im, $filename);

}

?>