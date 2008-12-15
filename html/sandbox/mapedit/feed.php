<?php

if ($_REQUEST['a']=='save') {

	$buffer = json_decode(stripslashes($_REQUEST['json']),true);
	file_put_contents('dump.txt', json_encode(stripslashes($_REQUEST['json'])));
	file_put_contents('trace.txt', print_r($buffer,true));
	
	$data = $buffer['map'];
	foreach ($data as $gid => $grid) {
		foreach ($grid as $eid => $element) {
			$data[$gid][$eid]['s'] = basename($data[$gid][$eid]['s']);
		}
	}
	$buffer['map']=$data;
	
	file_put_contents('test.txt', json_encode($buffer));
	
	echo json_encode(array('message' => 'OK'));

} elseif ($_REQUEST['a']=='load') {

	$f = file_get_contents('test.txt');
	echo $f;

} elseif ($_REQUEST['a']=='objects') {
	$base = $_REQUEST['base'];
	if (!$base) $base='furniture';
	
	$files = array();
	$x=0; $ok=true;
	
	$d = dir("objects");
	while (false !== ($entry = $d->read())) {
		if ( (substr($entry,0,1)!='.') && (substr($entry,-4)=='.png') )  {		
			$files[]='objects/'.$entry;		
		}
	}
	$d->close();
	
	echo json_encode($files);
	
} elseif ($_REQUEST['a']=='tiles') {
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