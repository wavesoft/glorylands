<?php
//file_put_contents('dump.txt', print_r($_REQUEST,true));
include "includes/renderer.php";
error_reporting(0);

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
	
	$savefile = 'saved/'.stripslashes($_REQUEST['f']).'.sav';	
	file_put_contents($savefile, json_encode($buffer));
	
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
	$im = imagecreatefrompng('../../data/maps/'.$valid_name.'-0-0.png');
	$back_width = imagesx($im);
	$back_height = imagesy($im);
	imagedestroy($im);
	
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
			'width' => $back_width,
			'height' => $back_height,
			'xsize' => 1,
			'ysize' => 1
		)
	);
	
	// Keep map data and z-grid separately because two different
	// engines read those files. No need to bother the one engine
	// with junk data.
	file_put_contents('../../data/maps/'.$valid_name.'.map', json_encode($map));
	file_put_contents('../../data/maps/'.$valid_name.'.zmap', serialize($buffer['zgrid']));
	
	echo json_encode(array('message' => 'OK'));

} elseif ($_REQUEST['a']=='define') {

	$buffer = json_decode(stripslashes($_REQUEST['json']),true);
	file_put_contents('dump.txt', stripslashes($_REQUEST['json']));
	file_put_contents('trace.txt', print_r($buffer,true));
	render_object($buffer['grid'], '../../images/elements/'.$buffer['name'].'.png');
	echo json_encode(array('message' => 'OK'));

} elseif ($_REQUEST['a']=='rdef') {

	$str_buffer = stripslashes($_REQUEST['json']);
	
	// BUGFIX: json_decode does not understand arrays in: [,,,,,,,,1,,,,3,,] format >>>>
	$str_buffer = str_replace('[,','[false,',$str_buffer);
	$str_buffer = str_replace(',]',',false]',$str_buffer);
	$str_buffer = str_replace(',,',',false,',$str_buffer);
	$str_buffer = str_replace(',,',',false,',$str_buffer);
	// <<<<	
	
	$buffer = json_decode($str_buffer,true);	
	file_put_contents('dump.txt', $str_buffer);
	file_put_contents('trace.txt', print_r($buffer,true));
	render_fegion_object($buffer['grid'], '../../images/elements/'.$buffer['name'].'.png');
	echo json_encode(array('message' => 'OK'));

} elseif ($_REQUEST['a']=='load') {

	$savefile = 'saved/'.stripslashes($_REQUEST['f']).'.sav';	
	$f = file_get_contents($savefile);
	echo $f;

} elseif ($_REQUEST['a']=='objects') {
	$base = $_REQUEST['base'];
	if (!$base) $base='furniture';
	
	$files = array();
	$x=0; $ok=true;
	
	$d = dir("../../images/elements");
	while (false !== ($entry = $d->read())) {
		if ( (substr($entry,0,1)!='.') && (substr($entry,-4)=='.png') && (substr($entry,0,strlen($base)+1)==$base.'-') )  {
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

} elseif ($_REQUEST['a']=='filelist') {

	$files = array();
	$x=0; $ok=true;
	
	$ext='';
	if ($_REQUEST['f']=='saved') {
		$d = dir("saved");
		$ext='.sav';
	} elseif ($_REQUEST['f']=='compile') {
		$d = dir("../../data/maps");
		$ext='.map';
	}
	while (false !== ($entry = $d->read())) {
		if ((substr($entry,0,1)!='.') && (substr($entry,-4)==$ext))  {
			$files[]=substr($entry,0,-4);
		}
	}
	$d->close();
	
	if (sizeof($files)==0) {
		echo json_encode(false);
	} else {
		echo json_encode($files);
	}

}

?>