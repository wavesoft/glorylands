<?php ob_implicit_flush(); ?>
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
$a = $_REQUEST['a'];

if (!$f || !$w || !$h || !$a) {
	die('missing parameters!');
}

for ($y = 0; $y < $h; $y++) {
	for ($x = 0; $x < $w; $x++) {
		if ($a == 'c') {	// Copy
			echo "Moving '{$f}-{$x}-{$y}.gif'...";
			copy("cache/{$f}-{$x}-{$y}.gif", "cache/done/{$f}-{$x}-{$y}.gif");
			unlink("cache/{$f}-{$x}-{$y}.gif");
			echo "OK\n";
		} elseif ($a == 'd') {
			echo "Deleting '{$f}-{$x}-{$y}.gif'...";
			unlink("cache/{$f}-{$x}-{$y}.gif");
			echo "OK\n";
		}
	}
}

echo "\nCompleted!";

?>
</pre>
</body>
</html>
