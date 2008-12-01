<?php

$base = $_REQUEST['base'];
if (!$base) $base='z-field-ext';

$files = array();
$x=0; $ok=true;
while ($ok) {
	if (file_exists('../../images/tiles/'.$base.'-0-'.$x.'.png')) {
		for ($y=0;$y<8;$y++) {
			$files[]='../../images/tiles/'.$base.'-'.$y.'-'.$x.'.png';
		}
	} else {
		$ok=false;
	}
	$x++;
}

echo json_encode($files);

?>