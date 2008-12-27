<?php

$sql->query("SELECT * FROM `data_maps` WHERE `index` = ".$_REQUEST['book']);
$row = $sql->fetch_array();

ob_start();

$buf = ob_get_contents();
ob_end_clean();

$act_result=array(

	# Theese variables will reach JSON untouched
	'mode'=>'DEDICATED',
	'width'=>800,
	'height'=>500, 
	'rollback'=>true,
	/* 'text' Will be added automatically before post-procession */
	
	# `_my ` is unsetted after the pre-buffer process.
	# Theese are used only from SMARTY processor
	'_my'=>array(
		'name'=>$row['name'],
		'desc'=>$row['desc']
	)
);

?>