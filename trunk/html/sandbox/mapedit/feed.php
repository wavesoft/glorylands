<?php
include "includes/renderer.php";

if ($_REQUEST['a']=='save') {

	$str_buffer = stripslashes($_REQUEST['json']);
	
	// BUGFIX: json_decode does not understand arrays in: [,,,,,,,,1,,,,3,,] format >>>>
	$str_buffer = str_replace('[,','[false,',$str_buffer);
	$str_buffer = str_replace(',]',',false]',$str_buffer);
	$str_buffer = str_replace(',,',',false,',$str_buffer);
	$str_buffer = str_replace(',,',',false,',$str_buffer);
	// <<<<
	
	$buffer = json_decode($str_buffer,true);
	//file_put_contents('dump.txt', $str_buffer);
	//file_put_contents('trace.txt', print_r($buffer,true));
	
	$data = $buffer['map'];
	foreach ($data as $gid => $grid) {
		foreach ($grid as $eid => $element) {
			$data[$gid][$eid]['s'] = basename($data[$gid][$eid]['s']);
		}
	}
	$buffer['map']=$data;
	
	file_put_contents('test.txt', json_encode($buffer));
	
	echo json_encode(array('message' => 'OK'));

} elseif ($_REQUEST['a']=='compile') {

	$str_buffer = stripslashes($_REQUEST['json']);
	
	// BUGFIX: json_decode does not understand arrays in: [,,,,,,,,1,,,,3,,] format >>>>
	$str_buffer = str_replace('[,','[false,',$str_buffer);
	$str_buffer = str_replace(',]',',false]',$str_buffer);
	$str_buffer = str_replace(',,',',false,',$str_buffer);
	$str_buffer = str_replace(',,',',false,',$str_buffer);
	// <<<<	
	
	$buffer = json_decode($str_buffer,true);	
	
	// Analyze map dimensions
	$data = $buffer['map'];
	$map_width=0;
	$map_height=0;
	foreach ($data as $gid => $grid) {
		foreach ($grid as $eid => $element) {
			$data[$gid][$eid]['s'] = basename($data[$gid][$eid]['s']);
			if ($data[$gid][$eid]['x']>$map_width) $map_width=$data[$gid][$eid]['x'];
			if ($data[$gid][$eid]['y']>$map_height) $map_height=$data[$gid][$eid]['y'];
		}
	}
	$buffer['map']=$data;

	// Re-map object images
	$data = $buffer['objects'];
	$map_images = array();
	foreach ($data as $id => $object) {
		$img = basename($data[$id]['image']);
		$pos = array_search($img, $map_images);
		if ($pos === false) {
			$pos = sizeof($map_images);
			$map_images[$pos] = $img;
			$data[$id]['image'] = $pos;
		}
		$data[$id]['image'] = $pos;
	}
	$buffer['objects']=$data;
	
	// Create a valid filename for the map
	$valid_name = strtolower($buffer['data']['title']);
	$valid_name = str_replace('\\','_', $valid_name);
	$valid_name = str_replace('/','_', $valid_name);
	$valid_name = str_replace('*','.', $valid_name);
	$valid_name = str_replace('?','_', $valid_name);
	$valid_name = str_replace(' ','_', $valid_name);
	$valid_name = str_replace(':','.', $valid_name);
	$valid_name = str_replace('"','-', $valid_name);
	$valid_name = str_replace('<','(', $valid_name);
	$valid_name = str_replace('>',')', $valid_name);
	$valid_name = str_replace('|','~', $valid_name);
	
	// Calculate map ID
	
	// Render background
	render_grid($buffer['map'], '../../data/maps/'.$valid_name.'-0-0.png', $buffer['background'], $map_width, $map_height);
	
	// Prepare result array
	$map = array(
		'width' => $map_width,
		'height' => $map_height,
		'title' => $buffer['data']['title'],
		'id' => 44,			
		'images' => $map_images,		
		'objects' => $buffer['objects'],	
		'background' => array(
			'fill' => basename($buffer['background']),
			'name' => $valid_name,
			'width' => 2912,
			'height' => 832,
			'xsize' => 1,
			'ysize' => 1
		),
		
		'collision' => $buffer['zgrid']
	);
	
	file_put_contents('../../data/maps/'.$valid_name.'.map', json_encode($map));
	
	echo json_encode(array('message' => 'OK'));

} elseif ($_REQUEST['a']=='define') {

	$buffer = json_decode(stripslashes($_REQUEST['json']),true);
	file_put_contents('dump.txt', stripslashes($_REQUEST['json']));
	file_put_contents('trace.txt', print_r($buffer,true));
	render_object($buffer['grid'], '../../images/elements/'.$buffer['name'].'.png');
	echo json_encode(array('message' => 'OK'));

} elseif ($_REQUEST['a']=='load') {

	$f = file_get_contents('test.txt');
	echo $f;

} elseif ($_REQUEST['a']=='objects') {
	$base = $_REQUEST['base'];
	if (!$base) $base='furniture';
	
	$files = array();
	$x=0; $ok=true;
	
	$d = dir("../../images/elements");
	while (false !== ($entry = $d->read())) {
		if ( (substr($entry,0,1)!='.') && (substr($entry,-4)=='.png') && (substr($entry,0,strlen($base))==$base) )  {		
			$files[]='../../images/elements/'.$entry;		
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