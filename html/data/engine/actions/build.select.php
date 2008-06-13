<?php

if (!isset($_REQUEST['model'])) {
	$act_result=array('mode'=>'NONE');
	return;
}

// Load the model we want to place
$model = new mapobj('data/models/'.$_REQUEST['model']);

// Get dimensions and display the rectangle
relayMessage(MSG_INTERFACE,'RECT', true, '?a=build.place&model='.$_REQUEST['model'], 
			 $model->width, $model->height, $model->bindX, $model->bindY-1, true, true); // ClickDisposable & Silent
	
// No interface action
$act_result=array('mode'=>'NONE');
return;

?>