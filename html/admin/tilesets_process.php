<?php ob_implicit_flush(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>GloryLands Tilesets management</title>
</head>

<body>
<pre>
<?php

$bp = 'C:/Users/Γιάννης/Downloads/== Glory Lands ==/2D Tilesets/old';
$fn = $_REQUEST['f'];
if (!isset($fn)) {
	die("Invalid file specified");
}
$f = "$bp/$fn";
$fn = substr($fn,0,-4);

// Load image
$im = imagecreatefrompng($f);

// Get and set the transparent color
$tX = $_REQUEST['tx'];
if (!isset($tX)) $tX=0;
$tY = $_REQUEST['ty'];
if (!isset($tY)) $tY=0;
$tId = imagecolorat($im,$tX*32,$tY*32);
imagecolortransparent($im,$tId);
imagegif($im, 'cache/out.gif');
imagedestroy($im);
$im = imagecreatefromgif('cache/out.gif');
imagealphablending($im,true);

// Calculate tileset size
$w = imagesx($im)/32;
$h = imagesy($im)/32;
echo "Tileset size: $w&times;$h";

// Start image copy
echo "<table border=\"1\" style=\"background-image:url(images/transparent.gif)\" cellpadding=\"0\" cellspacing=\"0\">";
for ($y = 0; $y < $h; $y++) {
	echo "<tr>\n";
	for ($x = 0; $x < $w; $x++) {
		$tile = imagecreatetruecolor(32,32);
		imagecopy($tile, $im, 0, 0, $x * 32 ,$y * 32, 32, 32);
		imagealphablending($tile,true);
		imagecolortransparent($tile,0);
		imagegif($tile, "cache/{$fn}-{$x}-{$y}.gif");
		imagedestroy($tile);
		echo "<td><a href=\"?f=".$_REQUEST['f']."&tx={$x}&ty={$y}\"><img border=\"0\" src=\"cache/{$fn}-{$x}-{$y}.gif\"></a></td>\n";
	}
	echo "</tr>\n";
}
echo "</table>\n";

echo "<a href=\"tilesets_install.php?f={$fn}&w={$w}&h={$h}&a=c\">Install tileset</a>\n";
echo "<a href=\"tilesets_install.php?f={$fn}&w={$w}&h={$h}&a=d\">Delete cache</a>\n";
echo "<a href=\"tilesets.php\">Back to tilesets</a>\n";

// Save image
//imagegif($im,'out.gif');

// Cleanup
imagedestroy($im);
?>
</pre>
</body>
</html>
