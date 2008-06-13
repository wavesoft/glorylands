<?php

$name = 'roshan';

$src = imagecreatefrompng("cache/{$name}.png");
if (!$src) die('Cannot load image!');

imagesavealpha($src,true);
imagealphablending($src,true);

function alpha_blending ($dest, $source, $dest_x, $dest_y) {

	## lets blend source pixels with source alpha into destination =)
	for ($y = 0; $y < imagesy($source); $y++) {
		for ($x = 0; $x < imagesx($source); $x++) {
					   
			$argb_s = imagecolorat ($source    ,$x            ,$y);
			$argb_d = imagecolorat ($dest    ,$x+$dest_x    ,$y+$dest_y);
					   
			$a_s    = ($argb_s >> 24) << 1; ## 7 to 8 bits.
			$r_s    =  $argb_s >> 16     & 0xFF;
			$g_s    =  $argb_s >>  8    & 0xFF;
			$b_s    =  $argb_s            & 0xFF;
						   
			$r_d    =  $argb_d >> 16    & 0xFF;
			$g_d    =  $argb_d >>  8    & 0xFF;
			$b_d    =  $argb_d            & 0xFF;
						   
			## source pixel 100% opaque (alpha == 0)
			if ($a_s == 0) {
				$r_d = $r_s; $g_d = $g_s; $b_d = $b_s;
			}
			## source pixel 100% transparent (alpha == 255)
			else if ($a_s > 253) {
			## using source alpha only, we have to mix (100-"some") percent
			## of source with "some" percent of destination.
			} else {
				$r_d = (($r_s * (0xFF-$a_s)) >> 8) + (($r_d * $a_s) >> 8);
				$g_d = (($g_s * (0xFF-$a_s)) >> 8) + (($g_d * $a_s) >> 8);
				$b_d = (($b_s * (0xFF-$a_s)) >> 8) + (($b_d * $a_s) >> 8);
			}
						   
			$rgb_d = imagecolorallocatealpha ($dest, $r_d, $g_d, $b_d, 0);
			imagesetpixel ($dest, $x, $y, $rgb_d);
		}
	}
}

function saveFrame($image, $frame, $filename) {
	$cell_w = imagesx($image)/4;
	$cell_h = imagesy($image)/4;

	$im = imagecreatetruecolor($cell_w,$cell_h);
	imagesavealpha($im,true);
	imagealphablending($im, true);

	$c = imagecolorallocatealpha($im,0,0,0,127);
	imagecolortransparent($im, $c);
	imagefill($im, 0,0, $c);

	imagecopy($im, $image, 0, 0, 0, $frame*$cell_h, $cell_w, $cell_h);
	imagegif($im, $filename);

	/*
	$c = imagecolorat($im, 0, 0);
	$p = imagecolorsforindex($im, $c);
	echo "<pre>Color is $c with parameters:\n".print_r($p,true)."</pre>";
	*/
	
	imagedestroy($im);
}

saveFrame($src, 0, "cache/char-{$name}-l.gif");
saveFrame($src, 1, "cache/char-{$name}-r.gif");
saveFrame($src, 2, "cache/char-{$name}-u.gif");
saveFrame($src, 3, "cache/char-{$name}-d.gif");

?>
<img src="<?php echo "cache/char-{$name}-l.gif"; ?>" />
<img src="<?php echo "cache/char-{$name}-r.gif"; ?>" />
<img src="<?php echo "cache/char-{$name}-u.gif"; ?>" />
<img src="<?php echo "cache/char-{$name}-d.gif"; ?>" />
