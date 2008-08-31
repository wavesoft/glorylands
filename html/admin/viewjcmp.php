<?php

if (!isset($_GET['map'])) die("Please specify a map using the <b>?map=</b> parameter");

$map = '../data/maps/'.$_GET['map'].'.jmap';
if (!is_file($map)) die("Data grid found: <b>$map</b>");

$zid = '../data/maps/'.$_GET['map'].'.zmap';
if (!is_file($map)) die("Z-Map found: <b>$map</b>");

echo "<pre>";
echo "<b>Data grid: </b>\n";
print_r(json_decode(file_get_contents($map),true));
echo "\n<b>Z grid:</b>\n";
print_r(unserialize(file_get_contents($zid)));
echo "</pre>";
?>