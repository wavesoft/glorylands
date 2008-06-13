<?php

function gl_clean_dir($base) {
	$d = dir($base);
	while (false !== ($entry = $d->read())) {
		if (($entry=='.') || ($entry=='..')) {
			// skip
		} elseif (is_dir($base."/".$entry)) {
			gl_clean_dir($base."/".$entry);
			rmdir($base."/".$entry);
		} else {
			unlink($base."/".$entry);
		}
	}
	$d->close();
}

function gl_clear_cache() {
	global $_CONFIG;
	gl_clean_dir($_CONFIG[GAME][BASE]."/admin/cache");
}

function gl_archive_package($id, $visual = true) {
	global $_CONFIG, $sql;
	
	if ($visual) echo "<pre>";
	
	// Get package info
	$sql->query("SELECT * FROM `system_packages` WHERE `index` = ".$_REQUEST['id']);
	if (!$sql->lastResult) {
		if ($visual) echo("SQL Error! ".$sql->getError()."</pre>\n");
		return "SQL Error! ".$sql->getError();
	}
	if ($sql->emptyResults) {
		if ($visual) echo("This plugin ID is invalid!</pre>\n");
		return "This plugin ID is invalid!";
	}
	$p_info = $sql->fetch_array();
	
	// Clear Cache
	if ($visual) echo "Initializing backup engine...";
	gl_clear_cache();
	if ($visual) echo "<font color=\"green\">done</font>\n";
	
	// Initialize file map
	if ($visual) echo "Initializing file map...\n";
	$f_list = fopen($_CONFIG[GAME][BASE]."/admin/cache/filemap.inf","w");
	
	// Count the files needed
	$sql->query("SELECT COUNT(*) FROM `system_files` WHERE `package` = ".$_REQUEST['id']);
	if (!$sql->lastResult) {
		if ($visual) echo("SQL Error! ".$sql->getError()."</pre>\n");
		return "SQL Error! ".$sql->getError();
	}
	if ($sql->emptyResults) {
		if ($visual) echo("This plugin ID is invalid!</pre>\n");
		return "This plugin ID is invalid!";
	}
	$num_rows = $sql->get_value();
	if ($visual) echo "Gathering <b>$num_rows</b> files...\n\n";
	
	// Keep file names in an array
	$zip_files = array($_CONFIG[GAME][BASE]."/admin/cache/filemap.inf");
	
	// Display progress bar and calculate progress step
	if ($visual) {
		?>
		<script language="javascript">
		<!-- 
		function setPos(p) {
			var elm = document.getElementById('progBar');
			elm.style.width = p + "px";
		}
		function hideBar() {
			var elm = document.getElementById('progWin');
			elm.style.display = 'none';
		}
		//-->
		</script>
		<div style="border: solid 1px #333333; height: 24px; width: 204px; position: relative;">
		<div style="background-color: #333366; height: 20px; position: absolute; left: 2px; top: 2px;" id="progBar"></div>
		</div>
		<?php 
	}
	
	if ($num_rows <= 0) {
		$p_step = 200;
	} else {
		$p_step = 200/$num_rows;
	}
	$p_value = 0;
	$p_last = 0;
	
	// Traverse on the array
	$files = $sql->query("SELECT * FROM `system_files` WHERE `package` = ".$_REQUEST['id']);
	while ($file = $sql->fetch_array_fromresults($files)) { 
	
		// Create filename's hash (used to store all the files and directories into a single level dir)
		$cache_file = md5($file['filename']).'.bak';
		
		// Copy the file
		copy($file['filename'], $_CONFIG[GAME][BASE]."/admin/cache/".$cache_file);
		array_push($zip_files, $_CONFIG[GAME][BASE]."/admin/cache/".$cache_file);
	
		// Change the file name into a relative location (where available)
		$f_name = $file['filename'];
		$f_name = str_ireplace($_CONFIG[GAME][BASE], '{$BASE}', $f_name);
		foreach ($_CONFIG[DIRS][ALIAS] as $key => $path) {
			if ($path!='') $f_name = str_ireplace($path, '{$'.$key.'}',$f_name);
		}
	
		// Map the file Hash with the real file name and location
		fwrite($f_list, $cache_file.'='.$f_name."\r\n");
	
		// Move progressbar forward
		$p_value += $p_step;
		$i = ceil($p_value);
		if (($i != $p_last) && $visual) echo "<script language=\"javascript\">setPos({$i});</script>";
		$p_last = $i;
	}
	
	// Hide the progress
	if (($i != $p_last) && $visual) echo "<script language=\"javascript\">hideBar();</script>";
	
	// Close map file handler
	fclose($f_list);
	if ($visual) echo "\nCompleted.\nCompressing archive...";
	
	// Compress archive
	$z_file = $_CONFIG[GAME][BASE].'/admin/archive/'.$p_info['guid'].'_'.date('y-m-d-H-i-s').'.zip';
	$zip = new PclZip($z_file);
	$zip->create($zip_files, PCLZIP_OPT_REMOVE_ALL_PATH);
	
	if ($visual) echo "<font color=\"green\">done</font>\n";
	if ($visual) echo "\nCompleted!\n</pre>";
	return true;
}

?>