<?php

if (!isset($_REQUEST['char'])) {

	$text = "<table><tr><td>";

	$d = dir(DIROF('DATA.MODEL',true));
	while (false !== ($entry = $d->read())) {
		if (substr($entry,-4) == '.png') {
			//$text.="<a href=\"javascript:gloryIO('?a=interface.ad.swapchar&char={$entry}');\">$entry</a><br />\n";
			$text.="<a onMouseOver=\"var e=document.getElementById('ad_char_preview'); e.src='images/elements/{$entry}';\" href=\"javascript:gloryIO('?a=interface.ad.swapchar&char={$entry}');\">$entry</a><br />\n";
		}
	}
	$d->close();
	
	$text.="</td><td valign=\"top\"><img style=\"position: absolute; top: 32px;\" id=\"ad_char_preview\" name=\"ad_char_preview\" /></td></tr></table>";
	
	
	// Return result
	$act_result = array(
			'mode' => 'POPUP',
			'text' => "<div style=\"height: 200px; overflow: auto\">$text</div>",
			'title' => 'Change your model',
			'width' => 300
	);

} else {

	$_SESSION[DATA]['model'] = $_REQUEST['char'];
	gl_update_guid_vars($_SESSION[PLAYER][GUID], array('model'=>$_REQUEST['char']));
	gl_do('map.grid.get');
}

?>
