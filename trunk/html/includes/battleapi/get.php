<?php
header('Content-Type: text/javascript');

echo "/**\n  * GloryLands MMORPG - Battle API v0.4\n  * Automatically generated from the file store\n  */\n\n";

function stack_dir($dirname) {
	$d = dir($dirname);
	while (false !== ($entry = $d->read())) {		
		if (substr($entry,0,1) != '.') {		
			if (is_dir($dirname.'/'.$entry)) {
				stack_dir($dirname.'/'.$entry);
			} elseif (substr($entry,-3)=='.js') {
				$fname = $dirname.'/'.$entry;
				$pad = 40 - strlen($fname);				
				echo "/* ---=={ ".$fname." }==---".str_repeat('-',$pad)." */\n";
				echo file_get_contents($dirname.'/'.$entry)."\n";
			}			
		}		
	}
	$d->close();
}

stack_dir(dirname(__FILE__));

?>