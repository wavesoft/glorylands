<pre>
<?php
ob_implicit_flush();
define('NOZIP',true);
include "../config/config.php";
include "../engine/includes/base.php";
$g_pcltar_lib_dir = $_CONFIG[GAME][BASE]."/admin/includes/lib";
include "includes/lib/pclzip.lib.php";
include "includes/lib/pcltar.lib.php";

function cleanDir($base) {
	$d = dir($base);
	while (false !== ($entry = $d->read())) {
		if (substr($entry,0,1)=='.') {
			// skip '.', '..', and hidden files (linux)
		} elseif (is_dir($base."/".$entry)) {
			cleanDir($base."/".$entry);
			rmdir($base."/".$entry);
		} else {
			unlink($base."/".$entry);
		}
	}
	$d->close();
}

echo "Cleaning up...";
cleanDir($_CONFIG[GAME][BASE]."/admin/cache");
mkdir($_CONFIG[GAME][BASE]."/admin/cache/tiles");
mkdir($_CONFIG[GAME][BASE]."/admin/cache/models");
echo "<font color=\"green\">done</font>\n";

echo "Archiving tiles...";
$base = DIROF('IMAGE.TILES');
$d = dir($base);
$count=0;
$z_tiles = array();
while (false !== ($entry = $d->read())) {
   if ((substr($entry,-4)=='.gif') && (substr($entry,0,6)=='chars-')) {
//   	echo("Deleting ".$base.$entry."\n");
	copy($base.$entry,$_CONFIG[GAME][BASE]."/admin/cache/tiles/".$entry);
	array_push($z_tiles, $_CONFIG[GAME][BASE]."/admin/cache/tiles/".$entry);
	$count++;
   }
}
$d->close();
echo "<font color=\"green\"><b>$count files</b> done</font>\n";

echo "Archiving models...";
$base = DIROF('DATA.MODEL');
$count=0;
$z_models = array();

$info = parse_ini_file(DIROF('IMAGE.TILES')."chars.ini");
if (!$info) die('Cannot open <b>chars.ini</b>!');
foreach ($info as $entry => $value) {
	if ($entry!='new_y' && $entry!='last_x' && $entry!='last_y' && $entry!='version' && (substr($entry,0,1)!=';') && ($entry!='')) {
		$entry.='.o';
		copy($base.$entry,$_CONFIG[GAME][BASE]."/admin/cache/models/".$entry);
		array_push($z_models, $_CONFIG[GAME][BASE]."/admin/cache/models/".$entry);
		$count++;
   	}
}
echo "<font color=\"green\"><b>$count files</b> done</font>\n";

echo "Building package...";

// Increment version on config file
if (!isset($info['version'])) $info['version']=0;
$info['version']++;

// Create package XML
$xml="<?xml version=\"1.0\" encoding=\"iso-8859-7\"?>\n";
$xml.="<package>\n";
$xml.="	<guid>ca3df5d6fced376a942d76e1bbfcd435</guid>\n";
$xml.="	<name>GloryLands Character Set</name>\n";
$xml.="	<version>".$info['version']."</version>\n";
$xml.="	<description></description>\n";
$xml.="	<files>\n";
$xml.="		<file type=\"IMAGE.TILES\" subdir=\"/\" recurse=\"yes\">tiles</file>\n";
$xml.="		<file type=\"DATA.MODEL\" subdir=\"/\" recurse=\"yes\">models</file>\n";
$xml.="	</files>\n";
$xml.="</package>\n";
file_put_contents($_CONFIG[GAME][BASE]."/admin/cache/package.xml", $xml);
$z_extras = array($_CONFIG[GAME][BASE]."/admin/cache/package.xml");

// Create zip
$zip = new PclZip($_CONFIG[GAME][BASE]."/admin/cache/chars.zip");
$zip->create(array_merge($z_tiles, $z_models, $z_extras), '', $_CONFIG[GAME][BASE]."/admin/cache/");

// Update ini
$f = fopen(DIROF('IMAGE.TILES')."chars.ini","w");
fwrite($f,"; Cache file for the charachters tileset\r\n");
foreach ($info as $parm => $value) {
	fwrite($f,"{$parm} = {$value}\r\n");	
}

echo "<font color=\"green\">done</font>\n";

?>

<a href="cache/chars.zip">Get It!</a>
</pre>