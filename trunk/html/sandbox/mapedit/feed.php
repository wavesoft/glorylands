<?php

if ($_REQUEST['a']=='save') {

	$data = json_decode(stripslashes($_REQUEST['json']),true);
	file_put_contents('dump.txt', json_encode(stripslashes($_REQUEST['json'])));
	file_put_contents('trace.txt', print_r($data,true));
	foreach ($data as $gid => $grid) {
		foreach ($grid as $eid => $element) {
			$data[$gid][$eid]['s'] = basename($data[$gid][$eid]['s']);
		}
	}
	file_put_contents('test.txt', json_encode($data));
	
	echo json_encode(array('message' => 'OK'));

} elseif ($_REQUEST['a']=='load') {

	$f = file_get_contents('test.txt');
	echo $f;

} else {
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
}

?>