<?php

$data = array(

	'width' => 50,
	'height' => 62,
	'title' => 'Luskan Village',
	'id' => 44,
		
	'images' => array(
		'images/column.png'
	),
	
	'objects' => array(
		array('image'=>0, 'x'=>5, 'y'=>5),
		array('image'=>0, 'x'=>6, 'y'=>6),		
		array('image'=>0, 'x'=>12, 'y'=>3)
	),

	'background' => array(
		'fill' => 'z-field-ext-1-2.png',
		'name' => 'images/luskan',
		'width' => 2912,
		'height' => 832,
		'xsize' => 1,
		'ysize' => 1
	),
	
	'collision' => array(
	)
	
);

for ($i=0; $i<100; $i++) {
	array_push($data['objects'], array('image'=>0, 'x'=>rand(0,100), 'y'=>rand(0,100), 'cy'=>160));
}

echo json_encode($data);

?>
