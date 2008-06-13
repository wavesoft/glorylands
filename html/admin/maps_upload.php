<?php
include "../config/config.php";
define("NOZIP",true);
include "../engine/includes/base.php";
$g_pcltar_lib_dir = $_CONFIG[GAME][BASE]."/admin/includes/lib";
include "includes/lib/pclzip.lib.php";
include "includes/lib/pcltar.lib.php";
//ob_end_flush();
ob_implicit_flush();
?>
<style type="text/css">
<!--
pre {
	font-size: 12px;
	color: #333333;
	background-color: #E9E9E9;
	border: 1px dashed #CCCCCC;
	margin: 3px;
	padding: 3px;
}
table {
	border: 1px solid #999999;
}
-->
</style>
<pre>
<?php
error_reporting(E_ALL);

function cleanDir($base) {
	$d = dir($base);
	while (false !== ($entry = $d->read())) {
		if (($entry=='.') || ($entry=='..')) {
			// skip
		} elseif (is_dir($base."/".$entry)) {
			cleanDir($base."/".$entry);
			rmdir($base."/".$entry);
		} else {
			unlink($base."/".$entry);
		}
	}
	$d->close();
}

function done() {
	echo "</pre>\n<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />\n";
	echo "</body></html>";
	die();
}

// Clean cache
echo "Initializing uploader engine...";
cleanDir($_CONFIG[GAME][BASE]."/admin/cache");
echo "<font color=\"green\">done</font>\n";
echo "Checking uploaded file...\n\n";

$file = $_FILES['file'];
if (!isset($file)) {
	die("<font color=\"red\">Warning! No file uploaded!</font></pre>\n<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />");
}

// Detect package mode and extract it
$extractok = false;
if (strtolower(substr($file['name'],-4)) == '.zip') {	
	// ZIP Archive

	echo "<b>ZIP</b> Detected. Deflating...";
	$zip = new PclZip($file['tmp_name']);
	$files=$zip->extract($_CONFIG[GAME][BASE]."/admin/cache");	
	if (!$files) {
		$extractok = false;
		echo "<font color=\"red\">failed</font>\n";
	} else {
		$extractok = true;
		echo "<font color=\"green\">done</font>\n";
	}

} elseif (strtolower(substr($file['name'],-4)) == '.tar') {	
	// TAR Archive

	echo "<b>TAR</b> Detected. Extracting...";
	$files=PclTarExtract($file['tmp_name'], $_CONFIG[GAME][BASE]."/admin/cache");
	if (!$files) {
		$extractok = false;
		echo "<font color=\"red\">failed</font>\n";
	} else {
		$extractok = true;
		echo "<font color=\"green\">done</font>\n";
	}

} elseif (strtolower(substr($file['name'],-7)) == '.tar.gz') {	
	// GZip TAR Archive

	echo "<b>GZip</b> Detected. Deflating...";	
	if (!function_exists('gzopen')) {
		echo "<font color=\"red\">failed</font>\n";
		die("\n<b>Your webserver does not support Zlib Compression Functions! Please install the <a href=\"http://www.gzip.org/zlib/\">zlib</a> extension for php, or try another package compression</b></pre>\n<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" /><input type=\"button\" onclick=\"window.location='http://www.gzip.org/zlib/'\" value=\"Download ZLib\" />");
	}
	
	$fin = gzopen($file['tmp_name'],"r");
	$fout = fopen($_CONFIG[GAME][BASE]."/admin/cache/package.tar", "w");
	while ($buf = gzread($fin,10240)) {
		fwrite($fout,$buf);
	}
	fclose($fin);
	fclose($fout);
	echo "<font color=\"green\">done</font>\n";

	echo "<b>TAR</b> Detected. Extracting...";
	$files=PclTarExtract($_CONFIG[GAME][BASE]."/admin/cache/package.tar", $_CONFIG[GAME][BASE]."/admin/cache");
	if (!$files) {
		$extractok = false;
		echo "<font color=\"red\">failed</font>\n";
	} else {
		$extractok = true;
		echo "<font color=\"green\">done</font>\n";
	}

} elseif (strtolower(substr($file['name'],-8)) == '.tar.bz2') {	
	// BZ2 TAR Archive
	echo "<b>BZip2</b> Detected. Deflating...";
	if (!function_exists('bzopen')) {
		echo "<font color=\"red\">failed</font>\n";
		die("\n<b>Your webserver does not support Bzip2 Compression Functions! Please install the <a href=\"http://sources.redhat.com/bzip2/\">bzip2</a> extension for php, or try another package compression</b></pre>\n<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" /><input type=\"button\" onclick=\"window.location='http://sources.redhat.com/bzip2/'\" value=\"Download BZip2\" />");
	}

	$fin = bzopen($file['tmp_name'],"r");
	$fout = fopen($_CONFIG[GAME][BASE]."/admin/cache/package.tar", "w");
	while ($buf = bzread($fin,10240)) {
		fwrite($fout,$buf);
	}
	fclose($fin);
	fclose($fout);
	echo "<font color=\"green\">done</font>\n";

	echo "<b>TAR</b> Detected. Extracting...";
	$files=PclTarExtract($_CONFIG[GAME][BASE]."/admin/cache/package.tar", $_CONFIG[GAME][BASE]."/admin/cache");
	if (!$files) {
		$extractok = false;
		echo "<font color=\"red\">failed</font>\n";
	} else {
		$extractok = true;
		echo "<font color=\"green\">done</font>\n";
	}
} else {
	die("<font color=\"red\">Cannot identify package type! Please use one of the follow formats: ZIP, TAR, GZIP, BZIP2</font></pre>\n<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />");
}

if (!$extractok) {
	die("<font color=\"red\">Extraction failed! (Is the file corrupted?)</font></pre>\n<input type=\"button\" onclick=\"window.history.go(-1)\" value=\"Back\" />");
} 
echo sizeof($files) ." file(s) extracted\n\n";

echo "Reading conficuration file..";
$has_jmap = false;
$has_zmap = false;
$jmapfile = '';
$d = dir($_CONFIG[GAME][BASE]."/admin/cache");
while (false !== ($entry = $d->read())) {
	if (substr($entry,-5)=='.jmap') { $has_jmap=true; $jmapfile = $entry; };
	if (substr($entry,-5)=='.zmap') $has_zmap=true;
}
$d->close();

if (!$has_jmap || !$has_zmap) {
	echo "<font color=\"red\">failed</font>";
	echo("\n<b>Package file does not contain both .jmap and .zmap files!</b>");
	done();
}
echo "<font color=\"green\">done</font>\n";

echo "\nYour map is this: <a href=\"rendermap.php?map=_{$jmapfile}\">(Click for real-size)</a>\n";
echo "<div style=\"width: 500px; height: 200px; overflow: scroll\"><img src=\"rendermap.php?map=_{$jmapfile}&scale=16\" /></div>";

done();

?>
