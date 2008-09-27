<?php
return Array(
   "helpers" => false,
   "managers" => false,
   "lib" => false,
   "default_outmode" => "html",
   "default_interface" => "battle.main",
   "post_processor" => "json",
   "post_result" => array(
		'mode' => 'MAIN',
		'head_image'=>'UI/navbtn_abort.gif', 
		'head_link'=>'?a=map.grid.get',
		'title'=>'Quit battle',
		'rollback'=>false
   )
);
?>