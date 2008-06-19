<?php ob_implicit_flush(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="refresh" content="1;URL=tiles.php" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>Importing tileset</title>
<style type="text/css">
<!--
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #000000;
}
-->
</style>
</head>
<body>
Importing tiles into database
<?php

$lastx = 0;
$lasty = 0;

// Get the last information
if (file_exists("../images/tiles/chars.ini")) {
	$info = parse_ini_file("../images/tiles/chars.ini");
	$lastx = $info['last_x'];
	$lasty = $info['last_y'];
}

$imgW = $_REQUEST['w'];
$imgH = $_REQUEST['h'];
$imgName = $_REQUEST['n'];

// Maximum width check
if ($imgW>8) {
	die("<font face=\"Arial\" size=\"2\" color=\"#FF0000\"><b>Warning!</b></font><font face=\"Arial\" size=\"2\"> This width is more than the maximum available!</font>");
}

//  [o][o][o][o][x][ ][ ]
//  [o][o][o][o][ ][ ][ ]
//  [x][ ][ ][ ][ ][ ][ ]
//  [ ][ ][ ][ ][ ][ ][ ]
//  [ ][ ][ ][ ][ ][ ][ ]
//  [ ][ ][ ][ ][ ][ ][ ]

// Width fit check
if ($lastx+$imgW>7) {
	$lasty=$info['new_y'];
	$lastx=0;
}

// Store this image's info
$info[$imgName] = "{$lastx},{$lasty},{$imgW},{$imgH}";

// Open and save charachter map file
$fmap = fopen("../data/models/{$imgName}.o","w");
 # PlacePoint is guessed as the bottom-center tile
 $cx = ceil($imgW/2);
 $cx--;
 if ($cx<0) $cx=0;
 $cy = $imgH;
 fwrite($fmap, "{$cx},{$cy}\r\n");

// Copy images
$bx = $lastx;
$by = $lasty;
for ($y=0;$y<$imgH;$y++) {
	$maprow="";
	for ($x=0;$x<$imgW;$x++) {
		// Move tile
		copy("cache/{$imgName}-{$x}-{$y}.gif","../images/tiles/chars-{$bx}-{$by}.gif");	
		unlink("cache/{$imgName}-{$x}-{$y}.gif");
		
		// Append row entry to map cache
		if ($maprow!='') $maprow.=',';
		$maprow.="chars-{$bx}-{$by}.gif";
		
		echo ".";
		$bx++;
	}
	fwrite($fmap,$maprow."\r\n");
	$bx = $lastx;
	$by++;
}
fclose($fmap);

// Calculate new locations for the next file
$lastx+=$imgW;
## X Overflow ##
if ($lastx>=7) {
	$lastx=0;
	$lasty = $info['new_y'];
}
## New Y Calculation ##
$newy = $lasty+$imgH;
if ($newy>$info['new_y']) $info['new_y'] = $newy;
## Save vars to ini array ##
$info['last_x']=$lastx;
$info['last_y']=$lasty;

// Write ini file
$f = fopen("../images/tiles/chars.ini","w");
fwrite($f,"; Cache file for the charachters tileset\r\n");
foreach ($info as $parm => $value) {
	fwrite($f,"{$parm} = {$value}\r\n");	
}

####  Rotate the latest 5 files for comparison ####

// Load all the cache files into the array
if (!is_dir("cache/.tilecache")) mkdir("cache/.tilecache");
$d = dir("cache/.tilecache");
$files = array();
while (false !== ($entry = $d->read())) {
	if (substr($entry,0,1)!='.') array_push($files, $entry);
}
$d->close();
// Perform rotation if items are more than 4
if (sizeof($files)>=4) {
	$fkill = array_shift($files);
	unlink("cache/.tilecache/".$fkill);
	array_push($files,$imgName.".gif");
}
copy("cache/".$imgName."_res.gif", "cache/.tilecache/".$imgName.".gif");

echo " done!";

?>
<br /><br />
<a href="tiles.php">Go Back</a>
</body>
</html>
