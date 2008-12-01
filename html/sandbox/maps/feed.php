<?php

$data = array();

/*
for ($i=0; $i<2; $i++) {
	array_push($data, array('image'=>'images/tree-1.png', 'cy' => 192, 'cx' => 0, 'x'=>rand(5,10), 'y'=>rand(5,10), 'fx_show'=>'zoom', 'fx_hide'=>'zoom', 'dynamic'=>true, 'title'=>'Dynamic object #'.$i));
}
array_push($data, array('image'=>'images/tree-1.png', 'cy' => 192, 'cx' => 0, 'x'=>0, 'y'=>0, 'fx_show'=>'pop', 'fx_hide'=>'pop', 'dynamic'=>true, 'title'=>'Dynamic object at 0,0'));
*/

/*
array_push($data, array('image'=>'images/tree-1.png', 'fx_move' => 'bounce', 'cy' => 192, 'cx' => 0, 'x'=>rand(5,20), 'y'=>rand(5,20), 'fx_show'=>'drop', 'fx_hide'=>'drop', 'dynamic'=>true, 'title'=>'Moevable object at 0,0', 'id' =>4123));
array_push($data, array('image'=>'images/tree-1.png', 'fx_move' => 'bounce', 'cy' => 192, 'cx' => 0, 'x'=>rand(5,20), 'y'=>rand(5,20), 'fx_show'=>'pop', 'fx_hide'=>'pop', 'dynamic'=>true, 'title'=>'Moevable object at 0,0', 'id' =>41));
array_push($data, array('image'=>'images/tree-1.png', 'fx_move' => 'bounce', 'cy' => 192, 'cx' => 0, 'x'=>rand(5,20), 'y'=>rand(5,20), 'fx_show'=>'zoom', 'fx_hide'=>'zoom', 'dynamic'=>true, 'title'=>'Moevable object at 0,0', 'id' =>233));
array_push($data, array('image'=>'images/tree-1.png', 'fx_move' => 'slide', 'cy' => 192, 'cx' => 0, 'x'=>rand(5,20), 'y'=>rand(5,20), 'fx_show'=>'fade', 'fx_hide'=>'fade', 'dynamic'=>true, 'title'=>'Moevable object at 0,0', 'id' =>42));
array_push($data, array('image'=>'images/tree-1.png', 'fx_move' => 'slide', 'cy' => 192, 'cx' => 0, 'x'=>rand(5,20), 'y'=>rand(5,20), 'fx_show'=>'zoom', 'fx_hide'=>'zoom', 'dynamic'=>true, 'title'=>'Moevable object at 0,0', 'id' =>43));
array_push($data, array('image'=>'images/tree-1.png', 'fx_move' => 'slide', 'cy' => 192, 'cx' => 0, 'x'=>rand(5,20), 'y'=>rand(5,20), 'fx_show'=>'fade', 'fx_hide'=>'fade', 'dynamic'=>true, 'title'=>'Moevable object at 0,0', 'id' =>25123));
*/

$x = rand(5,100);
$y = rand(5,100);
//array_push($data, array('image'=>'images/tree-1.png', 'fx_move' => 'bounce', 'cy' => 192, 'cx' => 0, 'x'=>$x, 'y'=>$y, 'fx_show'=>'drop', 'fx_hide'=>'drop', 'dynamic'=>true, 'title'=>'Bouncing', 'id' =>32));
//array_push($data, array('image'=>'images/tree-1.png', 'fx_move' => 'slide', 'cy' => 192, 'cx' => 0, 'x'=>$x, 'y'=>$y, 'fx_show'=>'drop', 'fx_hide'=>'drop', 'dynamic'=>true, 'title'=>'Sliding', 'id' =>44));
array_push($data, array('image'=>'images/tree-1.png', 'focus'=>true, 'fx_move' => 'fade', 'cy' => 192, 'cx' => 0, 'x'=>$x, 'y'=>$y, 'fx_show'=>'drop', 'fx_hide'=>'pop', 'dynamic'=>true, 'title'=>'No fx_move', 'id' =>53));

echo json_encode($data);

?>
