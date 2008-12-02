<?php

$data = array();

if ($_REQUEST['mode']==1){
	for ($i=0; $i<10; $i++) {
		array_push($data, array('image'=>'images/tree-1.png', 'cy' => 192, 'cx' => 0, 'x'=>rand(5,10), 'y'=>rand(5,10), 'fx_show'=>'zoom', 'fx_hide'=>'zoom', 'dynamic'=>true, 'title'=>'Dynamic object #'.$i));
	}
} elseif ($_REQUEST['mode']==2) {
	array_push($data, array('image'=>'images/tree-1.png', 'focus'=>true, 'fx_move' => 'bounce', 'cy' => 192, 'cx' => 0, 'x'=>rand(5,20), 'y'=>rand(5,20), 'fx_show'=>'fade', 'fx_hide'=>'fade', 'dynamic'=>true, 'title'=>'Fading object', 'id' =>100));
} elseif ($_REQUEST['mode']==3) {
	array_push($data, array('image'=>'images/tree-1.png', 'focus'=>true, 'fx_move' => 'bounce', 'cy' => 192, 'cx' => 0, 'x'=>rand(5,20), 'y'=>rand(5,20), 'fx_show'=>'pop', 'fx_hide'=>'pop', 'dynamic'=>true, 'title'=>'Popping object', 'id' =>101));
} elseif ($_REQUEST['mode']==4) {
	array_push($data, array('image'=>'images/tree-1.png', 'focus'=>true, 'fx_move' => 'bounce', 'cy' => 192, 'cx' => 0, 'x'=>rand(5,20), 'y'=>rand(5,20), 'fx_show'=>'zoom', 'fx_hide'=>'zoom', 'dynamic'=>true, 'title'=>'Zooming object', 'id' =>102));
} elseif ($_REQUEST['mode']==5) {
	array_push($data, array('image'=>'images/tree-1.png', 'focus'=>true, 'fx_move' => 'bounce', 'cy' => 192, 'cx' => 0, 'x'=>rand(5,20), 'y'=>rand(5,20), 'fx_show'=>'drop', 'fx_hide'=>'drop', 'dynamic'=>true, 'title'=>'Dropping object', 'id' =>103));
} elseif ($_REQUEST['mode']==6) {
	$x = rand(5,100);
	$y = rand(5,100);
	array_push($data, array('image'=>'images/tree-1.png', 'focus'=>true, 'fx_move' => 'slide', 'cy' => 192, 'cx' => 0, 'x'=>$x, 'y'=>$y, 'fx_show'=>'fade', 'fx_hide'=>'fade', 'dynamic'=>true, 'title'=>'Scrolling object', 'id' =>104));
} elseif ($_REQUEST['mode']==7) {
	$x = rand(5,100);
	$y = rand(5,100);
	array_push($data, array('image'=>'images/tree-1.png', 'focus'=>true, 'fx_move' => 'bounce', 'cy' => 192, 'cx' => 0, 'x'=>$x, 'y'=>$y, 'fx_show'=>'fade', 'fx_hide'=>'fade', 'dynamic'=>true, 'title'=>'Scrolling object', 'id' =>105));
} elseif ($_REQUEST['mode']==8) {
	$x = rand(5,100);
	$y = rand(5,100);
	array_push($data, array('image'=>'images/tree-1.png', 'focus'=>true, 'fx_move' => 'fade', 'cy' => 192, 'cx' => 0, 'x'=>$x, 'y'=>$y, 'fx_show'=>'fade', 'fx_hide'=>'fade', 'dynamic'=>true, 'title'=>'Scrolling object', 'id' =>106));
}

echo json_encode($data);

?>
