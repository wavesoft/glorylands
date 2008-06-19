<?php
include "../config/config.php";
include "../engine/includes/base.php";

echo "Cleaning up model cache...";
$base = DIROF('DATA.MODEL');
$d = dir($base);
$count=0;
while (false !== ($entry = $d->read())) {
   if (substr($entry,-4)=='.obj') {
   	unlink($base.$entry);
	$count++;
   }
}
$d->close();
echo "<b>$count files OK</b>";

?>