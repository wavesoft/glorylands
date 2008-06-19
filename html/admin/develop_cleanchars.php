<?php
include "../config/config.php";
include "../engine/includes/base.php";

echo "<pre>Archiving tiles...\n";
$base = DIROF('IMAGE.TILES');
$d = dir($base);
$count=0;
while (false !== ($entry = $d->read())) {
   if ((substr($entry,-4)=='.gif') && (substr($entry,0,6)=='chars-')) {
   	echo("Deleting ".$base.$entry."\n");
	$count++;
   }
}
$d->close();
echo "<b>$count files OK</b></pre>";

?>