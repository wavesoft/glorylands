<?php
// ===========================================
//   PROJECT: The Glory Lands MMORPG Game
// ===========================================
//   Version: v0.1 Beta
//      File: Scheduled events manager
//                   _______
// _________________| TO DO |_________________
// ___________________________________________
//   (C) Copyright 2007, John Haralampidis
// ===========================================

## When included, this file checks if a specific time interval
## is passed

function fc_callback() {
	set_time_limit(0);
	$i=0;
	for ($i=0; $i<5; $i++) {
		$f = fopen("c:\\t.txt", "w");
		fwrite($f,"Pass #".$i);
		fclose($f);
		sleep(5);
	}
}
register_shutdown_function("fc_callback");

echo "Check c:\t.txt";
die();

?>