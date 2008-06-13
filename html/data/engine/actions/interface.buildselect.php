<?php

$text = "";
$d = dir(DIROF('DATA.MODEL',true));
while (false !== ($entry = $d->read())) {
	if (substr($entry,-2) == '.o') {
		$text.="<a href=\"javascript:gloryIO('?a=build.select&model={$entry}',false,true);\">$entry</a><br />\n";
	}
}
$d->close();


// Return result
$act_result = array(
		'mode' => 'POPUP',
		'text' => "<div style=\"height: 200px; overflow: auto\">$text</div>",
		'title' => 'Place a model'
);

?>