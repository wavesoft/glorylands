<?php ob_implicit_flush(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Processing Request</title>
<style type="text/css">
<!--
table.tiles {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #666666;
	background-color: #E8E8E8;
	border: 1px solid #CCCCCC;
}
td.head {
	color: #FFFFFF;
	background-color: #666666;
}
td.data {
	border: 1px solid #999999;
}
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #333333;
}
div.console {
	color: #FFFFFF;
	background-color: #666666;
	font-size: 10px;
	font-weight: bold;
}
pre.console {
	background-color: #EAEAEA;
	border: 1px solid #CCCCCC;
}
-->
</style>
</head>
<body>
<pre class="console">
<div class="console"><font color="#FFCC00">&lt;?</font> Console</div><?php

// Load char to be converted
$imsrc = imagecreatefrompng("../images/chars/".$_REQUEST['char'].".png");
imageantialias($imsrc, true);
imagealphablending($imsrc,true);

// Customization perform
$pixel_x = 0;
if (isset($_REQUEST['pixel_x'])) $pixel_x=$_REQUEST['pixel_x'];
$pixel_y = 0;
if (isset($_REQUEST['pixel_y'])) $pixel_y=$_REQUEST['pixel_y'];
$cell_x = 0;
if (isset($_REQUEST['cell_x'])) $cell_x=$_REQUEST['cell_x'];
$cell_y = 0;
if (isset($_REQUEST['cell_y'])) $cell_y=$_REQUEST['cell_y'];
$offset_x = 0;
if (isset($_REQUEST['offset_x'])) $offset_x=$_REQUEST['offset_x'];
$offset_y = 0;
if (isset($_REQUEST['offset_y'])) $offset_y=$_REQUEST['offset_y'];
$real_size = 0;
if (isset($_REQUEST['real_size'])) $real_size=$_REQUEST['real_size'];

// Load info
$tc = ($_REQUEST['truecolor'] == '1');
$w = imagesx($imsrc);
$h = imagesy($imsrc);

// Check/Resample
$cellsX = floor($w / 32) - $cell_x;
$cellsY = floor($h / 32) - $cell_y;
$Nw = $cellsX * 32;
$Nh = $cellsY * 32;

// If resize is requested, resize image
if (($pixel_x!=0 || $pixel_y!=0) || $real_size!=0) {
	$inw=$w-$pixel_x; $inh=$h-$pixel_y;
	$im_res = imagecreatetruecolor($Nw,$Nh);
	
	imagesavealpha($im_res,true);
	imageantialias($im_res, true);
	
	$c=imagecolorallocatealpha($im_res,0,255,0,127);
	imagecolortransparent($im_res,$c);
	imagefill($im_res,0,0,$c);
	
	imagecopyresized($im_res,$imsrc,-$offset_x,-$offset_y,0,0,$inw,$inh,$w,$h);
	$w=$Nw;$h=$Nh;
	imagedestroy($imsrc);
	$imsrc=$im_res;
	unset($im_res);
}

// Output info
echo "Image <strong>".$_REQUEST['char'].".png</strong> loaded.\n";
echo "Size: <strong>".$w."x".$h."</strong>\n";
echo "Cells: <strong>".$cellsX."x".$cellsY."</strong>\n";
echo "Resampled to: <strong>".$Nw."x".$Nh."</strong>\n";

// Resample image
$im_new = imagecreatetruecolor($Nw,$Nh);
imagesavealpha($im_new,true);

$c=imagecolorallocatealpha($im_new,0,255,0,127);
imagecolortransparent($im_new,$c);
imagefill($im_new,0,0,$c);

imagecopyresized($im_new,$imsrc,-$offset_x,-$offset_y,0,0,$Nw,$Nh,$w,$h);
imagegif($im_new,"cache/".$_REQUEST['char']."_res.gif");

// Break into tiles
for ($y=0;$y<$cellsY;$y++) {
	for ($x=0;$x<$cellsX;$x++) {
		$im = imagecreatetruecolor(32,32); 

		$c=imagecolorallocatealpha($im,0,255,0,127);
		imagecolortransparent($im,$c);
		imagefill($im,0,0,$c);

		imagecopy($im,$im_new,0,0,$x*32,$y*32,32,32);		
		imagegif($im,"cache/".$_REQUEST['char']."-{$x}-{$y}.gif");
		imagedestroy($im);	
	}
}

imagedestroy($im_new);	
imagedestroy($imsrc);

####  Rotate the latest 5 files for comparison ####

// Load all the cache files into the array
if (!is_dir("cache/.tilecache")) mkdir("cache/.tilecache");
$d = dir("cache/.tilecache");
$files = array();
while (false !== ($entry = $d->read())) {
	if (substr($entry,0,1)!='.') array_push($files, $entry);
}
$d->close();

?>
</pre>
<table class="tiles">
<tr>
	<td class="head">Original Image</td>
	<td class="head">Resampled Image</td>
	<td class="head">Tiled Image</td>
</tr>
<tr>
	<td class="data"><img src="../images/chars/<?php echo $_REQUEST['char'] ?>.png" /></td>
	<td class="data">
    <img src="cache/.tilecache/<?php echo $files[0]; ?>" />   
    <img src="cache/.tilecache/<?php echo $files[1]; ?>" />   
    <img border="1" src="cache/<?php echo $_REQUEST['char'] ?>_res.gif" />   
    <img src="cache/.tilecache/<?php echo $files[2]; ?>" />   
    <img src="cache/.tilecache/<?php echo $files[3]; ?>" />   
    </td>
	<td class="data">
	<table border="1" cellpadding="0" cellspacing="0">
<?php
for ($y=0;$y<$cellsY;$y++) {
	echo "<tr>\n";
	for ($x=0;$x<$cellsX;$x++) {
		echo "<td style=\"background-image:url(../images/tiles/castle-ext-0-0.gif)\"><img src=\"cache/".$_REQUEST['char']."-{$x}-{$y}.gif\" /></td>";
	}
	echo "</tr>\n";
}
?>
	</table>
	</td>
</tr>
</table>
<p>Need any modifications?<br />
<input type="button" value="Cells -" onclick="window.location='?char=<?php echo $_REQUEST['char'] ?>&cell_x=<?php echo $cell_x+1; ?>&cell_y=<?php echo $cell_y+1; ?>&pixel_x=<?php echo $pixel_x; ?>&pixel_y=<?php echo $pixel_y; ?>&offset_x=<?php echo $offset_x; ?>&offset_y=<?php echo $offset_y; ?>&real_size=<?php echo $real_size; ?>'" />
<input type="button" value="Cells +" onclick="window.location='?char=<?php echo $_REQUEST['char'] ?>&cell_x=<?php echo $cell_x-1; ?>&cell_y=<?php echo $cell_y-1; ?>&pixel_x=<?php echo $pixel_x; ?>&pixel_y=<?php echo $pixel_y; ?>&offset_x=<?php echo $offset_x; ?>&offset_y=<?php echo $offset_y; ?>&real_size=<?php echo $real_size; ?>'" />
<input type="button" value="Pixel -" onclick="window.location='?char=<?php echo $_REQUEST['char'] ?>&pixel_x=<?php echo $pixel_x+1; ?>&pixel_y=<?php echo $pixel_y+1; ?>&cell_x=<?php echo $cell_x; ?>&cell_y=<?php echo $cell_y; ?>&offset_x=<?php echo $offset_x; ?>&offset_y=<?php echo $offset_y; ?>&real_size=<?php echo $real_size; ?>'" />
<input type="button" value="Pixel +" onclick="window.location='?char=<?php echo $_REQUEST['char'] ?>&pixel_x=<?php echo $pixel_x-1; ?>&pixel_y=<?php echo $pixel_y-1; ?>&cell_x=<?php echo $cell_x; ?>&cell_y=<?php echo $cell_y; ?>&offset_x=<?php echo $offset_x; ?>&offset_y=<?php echo $offset_y; ?>&real_size=<?php echo $real_size; ?>'" />
<input type="button" value="Reset" onclick="window.location='?char=<?php echo $_REQUEST['char'] ?>'" />
<input type="button" value="Real Size" onclick="window.location='?char=<?php echo $_REQUEST['char'] ?>&pixel_x=<?php echo $pixel_x; ?>&pixel_y=<?php echo $pixel_y; ?>&cell_x=<?php echo $cell_x; ?>&cell_y=<?php echo $cell_y; ?>&offset_x=<?php echo $offset_x; ?>&offset_y=<?php echo $offset_y; ?>&real_size=<?php echo ($real_size==1)?"0":"1"; ?>'" />
<br />Detail modification:<br />
<input type="button" value="Cell X -" onclick="window.location='?char=<?php echo $_REQUEST['char'] ?>&cell_x=<?php echo $cell_x+1; ?>&cell_y=<?php echo $cell_y; ?>&pixel_x=<?php echo $pixel_x; ?>&pixel_y=<?php echo $pixel_y; ?>&offset_x=<?php echo $offset_x; ?>&offset_y=<?php echo $offset_y; ?>&real_size=<?php echo $real_size; ?>'" />
<input type="button" value="Cell X +" onclick="window.location='?char=<?php echo $_REQUEST['char'] ?>&cell_x=<?php echo $cell_x-1; ?>&cell_y=<?php echo $cell_y; ?>&pixel_x=<?php echo $pixel_x; ?>&pixel_y=<?php echo $pixel_y; ?>&offset_x=<?php echo $offset_x; ?>&offset_y=<?php echo $offset_y; ?>&real_size=<?php echo $real_size; ?>'" />
|
<input type="button" value="Cell Y -" onclick="window.location='?char=<?php echo $_REQUEST['char'] ?>&cell_x=<?php echo $cell_x; ?>&cell_y=<?php echo $cell_y+1; ?>&pixel_x=<?php echo $pixel_x; ?>&pixel_y=<?php echo $pixel_y; ?>&offset_x=<?php echo $offset_x; ?>&offset_y=<?php echo $offset_y; ?>&real_size=<?php echo $real_size; ?>'" />
<input type="button" value="Cell Y +" onclick="window.location='?char=<?php echo $_REQUEST['char'] ?>&cell_x=<?php echo $cell_x; ?>&cell_y=<?php echo $cell_y-1; ?>&pixel_x=<?php echo $pixel_x; ?>&pixel_y=<?php echo $pixel_y; ?>&offset_x=<?php echo $offset_x; ?>&offset_y=<?php echo $offset_y; ?>&real_size=<?php echo $real_size; ?>'" />
<br />
<input type="button" value="Offset X -" onclick="window.location='?char=<?php echo $_REQUEST['char'] ?>&pixel_x=<?php echo $pixel_x; ?>&pixel_y=<?php echo $pixel_y; ?>&cell_x=<?php echo $cell_x; ?>&cell_y=<?php echo $cell_y; ?>&offset_x=<?php echo $offset_x+1; ?>&offset_y=<?php echo $offset_y; ?>&real_size=<?php echo $real_size; ?>'" />
<input type="button" value="Offset X +" onclick="window.location='?char=<?php echo $_REQUEST['char'] ?>&pixel_x=<?php echo $pixel_x; ?>&pixel_y=<?php echo $pixel_y; ?>&cell_x=<?php echo $cell_x; ?>&cell_y=<?php echo $cell_y; ?>&offset_x=<?php echo $offset_x-1; ?>&offset_y=<?php echo $offset_y; ?>&real_size=<?php echo $real_size; ?>'" />
<input type="button" value="Offset Y -" onclick="window.location='?char=<?php echo $_REQUEST['char'] ?>&pixel_x=<?php echo $pixel_x; ?>&pixel_y=<?php echo $pixel_y; ?>&cell_x=<?php echo $cell_x; ?>&cell_y=<?php echo $cell_y; ?>&offset_x=<?php echo $offset_x; ?>&offset_y=<?php echo $offset_y+1; ?>&real_size=<?php echo $real_size; ?>'" />
<input type="button" value="Offset Y +" onclick="window.location='?char=<?php echo $_REQUEST['char'] ?>&pixel_x=<?php echo $pixel_x; ?>&pixel_y=<?php echo $pixel_y; ?>&cell_x=<?php echo $cell_x; ?>&cell_y=<?php echo $cell_y; ?>&offset_x=<?php echo $offset_x; ?>&offset_y=<?php echo $offset_y-1; ?>&real_size=<?php echo $real_size; ?>'" />
<br />
<input type="button" value="Pixel X -" onclick="window.location='?char=<?php echo $_REQUEST['char'] ?>&pixel_x=<?php echo $pixel_x+1; ?>&pixel_y=<?php echo $pixel_y; ?>&cell_x=<?php echo $cell_x; ?>&cell_y=<?php echo $cell_y; ?>&offset_x=<?php echo $offset_x; ?>&offset_y=<?php echo $offset_y; ?>&real_size=<?php echo $real_size; ?>'" />
<input type="button" value="Pixel X +" onclick="window.location='?char=<?php echo $_REQUEST['char'] ?>&pixel_x=<?php echo $pixel_x-1; ?>&pixel_y=<?php echo $pixel_y; ?>&cell_x=<?php echo $cell_x; ?>&cell_y=<?php echo $cell_y; ?>&offset_x=<?php echo $offset_x; ?>&offset_y=<?php echo $offset_y; ?>&real_size=<?php echo $real_size; ?>'" />
<input type="button" value="Pixel Y -" onclick="window.location='?char=<?php echo $_REQUEST['char'] ?>&pixel_x=<?php echo $pixel_x; ?>&pixel_y=<?php echo $pixel_y+1; ?>&cell_x=<?php echo $cell_x; ?>&cell_y=<?php echo $cell_y; ?>&offset_x=<?php echo $offset_x; ?>&offset_y=<?php echo $offset_y; ?>&real_size=<?php echo $real_size; ?>'" />
<input type="button" value="Pixel Y +" onclick="window.location='?char=<?php echo $_REQUEST['char'] ?>&pixel_x=<?php echo $pixel_x; ?>&pixel_y=<?php echo $pixel_y-1; ?>&cell_x=<?php echo $cell_x; ?>&cell_y=<?php echo $cell_y; ?>&offset_x=<?php echo $offset_x; ?>&offset_y=<?php echo $offset_y; ?>&real_size=<?php echo $real_size; ?>'" />
<br />Or is it correct?<br />
<input type="button" value="Import tile into database" onclick="window.location='tiles_import.php?w=<?php echo $cellsX ?>&h=<?php echo $cellsY ?>&n=<?php echo $_REQUEST['char'] ?>'" />
<input type="button" value="Back" onclick="window.location='tiles.php'" />
</p>
</body>
</html>
