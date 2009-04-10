<?php ob_implicit_flush(); 
$g_pcltar_lib_dir = 'includes/lib';
include "includes/lib/pclzip.lib.php";
include "includes/lib/pcltar.lib.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>GloryLands Tilesets management</title>
</head>

<body>
<pre>
<a href="tilesets.php">Back to tilesets</a>
<?php

$f = $_REQUEST['f'];
$w = $_REQUEST['w'];
$h = $_REQUEST['h'];
$t = $_REQUEST['target'];

if (!$f || !$w || !$h) {
	die('missing parameters!');
}

// Target:
$t='cache/.done';

if (!is_dir($t)) mkdir($t);
$f_name = str_replace('-','_',$f);
$t_root = $t.'/'.$f_name;
if (!is_dir($t_root)) mkdir($t_root);
$t = $t_root.'/tiles';
if (!is_dir($t)) mkdir($t);
$files = array();

echo "Crating the tilesets...\n";
for ($y = 0; $y < $h; $y++) {
	for ($x = 0; $x < $w; $x++) {
		echo "Moving '{$f}-{$x}-{$y}.png'...";
		copy("cache/{$f}-{$x}-{$y}.png", "{$t}/{$f_name}-{$x}-{$y}.png");
		$files[] = "{$t}/{$f_name}-{$x}-{$y}.png";
		unlink("cache/{$f}-{$x}-{$y}.png");
		echo "OK\n";
	}
}

echo "Making the configuration XML...\n";
$guid = md5(rand(1,100000).microtime().date("YmdHis").rand(1,100000).$_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']);
$package = '<?xml version="1.0" encoding="iso-8859-7"?>
<package>
	<guid>'.$guid.'</guid>
	<name>'.$f_name.' Tileset</name>
	<version>1</version>
	<description>'.$f_name.' tileset</description>
	
	<files>
		<file type="IMAGE.TILES" subdir="/" recurse="yes">tiles</file>
	</files>
</package>';
file_put_contents($t_root.'/package.xml', $package);
$files[] = $t_root.'/package.xml';

echo "Archiving...\n";
$zip = new PclZip($t_root.'/'.$f_name.'-pckg.zip');
$zip->create($files, '', $t_root);

echo "\nCompleted!\n<a href=\"cache/.done/{$f_name}/{$f_name}-pckg.zip\">Download file</a>";

?>
</pre>
</body>
</html>
