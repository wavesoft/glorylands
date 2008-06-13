<?php

if (!isset($_REQUEST['char'])) {
	$text = "";
	$d = dir(DIROF('DATA.MODEL',true));
	while (false !== ($entry = $d->read())) {
		if (substr($entry,-2) == '.o') {
			$text.="<a href=\"javascript:gloryIO('?a=interface.ad.swapchar&char={$entry}');\">$entry</a><br />\n";
		}
	}
	$d->close();
	
	
	// Return result
	$act_result = array(
			'mode' => 'POPUP',
			'text' => "<div style=\"height: 200px; overflow: auto\">$text</div>",
			'title' => 'Change your model'
	);

} else {

	$_SESSION[DATA]['model'] = $_REQUEST['char'];
	gl_update_guid_vars($_SESSION[PLAYER][GUID], array('model'=>$_REQUEST['char']));
	gl_do('map.grid.get');
}

?>