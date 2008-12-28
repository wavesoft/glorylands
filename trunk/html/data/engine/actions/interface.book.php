<?php

$sql->query("SELECT * FROM `data_maps` WHERE `index` = ".$_REQUEST['book']);
$row = $sql->fetch_array();

$objects = array(
	array('name' => 'Shuriken', 'icon' => 'inventory/shuriken-48x48.png', 'desc' => 'Traditional japanese Shuriken', 'cost' => 32),
	array('name' => 'Kunai', 'icon' => 'inventory/Kunai-48x48.png', 'desc' => 'Ninja Kunai', 'cost' => 32),
	array('name' => 'Nunchaku', 'icon' => 'inventory/Nunchaku-48x48.png', 'desc' => 'Ninja Nunchaku', 'cost' => 32),
	array('name' => 'Sacred Scroll', 'icon' => 'inventory/parchemin-48x48.png', 'desc' => 'Sacred scroll of protection', 'cost' => 32)
);

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
		'desc'=>$row['desc'],
		'objects'=>$objects,
		'gold' => $_SESSION[PLAYER][DATA]['money']
	)
);

?>