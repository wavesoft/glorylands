<?php
header('Content-Type: text/javascript');

echo "/**\n  * GloryLands MMORPG - GLAPI v2.3\n  * Automatically generated from the file store\n  */\n\n";

function stack_dir($dirname) {
	$d = dir($dirname);
	$buf = '';
	while (false !== ($entry = $d->read())) {		
		if (substr($entry,0,1) != '.') {		
			if (is_dir($dirname.'/'.$entry)) {
				$buf.= stack_dir($dirname.'/'.$entry);
			} elseif (substr($entry,-3)=='.js') {
				$fname = $dirname.'/'.$entry;
				$pad = 120 - strlen($fname);				
				$buf.= "/* ---=={ ".$fname." }==---".str_repeat('-',$pad)." */\n";
				$buf.= file_get_contents($dirname.'/'.$entry)."\n";
			}			
		}		
	}
	$d->close();
	return $buf;
}

function optimize(&$buffer) {
	
	// Remove comments
	$buffer = preg_replace('%/\\*.*?\\*/%s', '', $buffer);
	$buffer = preg_replace('%//.*$%m', '', $buffer);

	// Remove spaces before and after the lines
	$buffer = preg_replace('/^[ \\t]+|[ \\t]+$/m','', $buffer);

	// Replace new lines inside string with \n
	//$buffer = preg_replace('/(?<=[\'"][\\w\\s]*)\\r?\\n(?=[\\w\\s]*[\'"])/','\\n', $buffer);
	
	// Clean \r\n's
	//$buffer = str_replace("\n",'',$buffer);
	//$buffer = str_replace("\r",'',$buffer);
	
	// Clean whitespaces that are not inside strings
	//$buffer = preg_replace('/(["\'][^"\'\\\\]*(?:\\\\.[^"\'\\\\]*)*["\'])|((?:\\t+| {3,}|\r|\n))/','\\1', $buffer);
	
	// Clean blank lines
	//$buffer = preg_replace('/^[ \\t]*$/m', '', $buffer);
}

$buffer = stack_dir('.');
//optimize($buffer);
echo $buffer;

?>