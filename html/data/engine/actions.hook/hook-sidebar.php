<?php

// Convert seconds into hours & minutes
function sidebar_fix_time($sec) {
	$min = 0;
	$hour = 0;
	if ($sec >= 60) {
		$min = floor($sec / 60);
		$sec = $sec % 60;
		if ($min >= 60) {
			$hour = floor($min / 60);
			$min = $min % 60;
		}
	}
	
	if (strlen($hour)==1) $hour='0'.$hour;
	if (strlen($min)==1) $min='0'.$min;
	if (strlen($sec)==1) $sec='0'.$sec;
	return "$hour:$min:$sec";
}

// Feed user status on the sidebar module
registerEvent('sidebar_data_feed', 'system.clientpoll');
registerEvent('sidebar_data_feed', 'system.complete_operation');
function sidebar_data_feed() {

	// Display some general information about the player
	$icon = $_SESSION[PLAYER][DATA]['icon'];
	$head = "<h3>".$_SESSION[PLAYER][DATA]['name']."</h3>\n<p>".$_SESSION[PLAYER][DATA]['description']."</p>";

	// Check for active hooks
	$body = "<table width=\"100%\"><tr><td valign=\"top\" width=\"120\">{#STATISTICS#}:<br />\n<table class=\"sidebar_infotbl\">
<tr>
	<td align=\"right\" width=\"30\"><b>STR</b></td>
	<td>{$_SESSION[PLAYER][DATA]['STR']}</td>
	<td align=\"right\" width=\"30\"><b>INT</b></td>
	<td>{$_SESSION[PLAYER][DATA]['INT']}</td>
</tr>
<tr>
	<td align=\"right\" width=\"30\"><b>DEX</b></td>
	<td>{$_SESSION[PLAYER][DATA]['DEX']}</td>
	<td align=\"right\" width=\"30\"><b>WIS</b></td>
	<td>{$_SESSION[PLAYER][DATA]['WIS']}</td>
</tr>
<tr>
	<td align=\"right\" width=\"30\"><b>CON</b></td>
	<td>{$_SESSION[PLAYER][DATA]['CON']}</td>
	<td align=\"right\" width=\"30\"><b>CHA</b></td>
	<td>{$_SESSION[PLAYER][DATA]['CHA']}</td>
</tr>
</table></td><td valign=\"top\">Active Jobs:<br />\n
";
	
	// Check for scheduled events that need to be displaied
	$sched = gl_get_schedulees();	
	$scbody = "";
	if ($sched && (sizeof($sched)>0)) {
		foreach ($sched as $entry) {
			if ($entry['description']!='') {
				$cicon='';
				if (isset($entry['data']['icon'])) {
					$cicon = "<img src=\"images/".$entry['data']['icon']."\" />";
				}
				$scbody.="<tr><td align=\"left\" width=\"20\">$cicon</td><td>".$entry['description']."</td><td><b class=\"sidebar_timedown\">".sidebar_fix_time($entry['time'])."</b>\"</td></tr>\n";
			}
		}
		if ($scbody!='') {
			$body.="<div class=\"sidebar_scrollable\"><table width=\"100%\" class=\"sidebar_evtable\">".$scbody."</table></div>";
		} else {
			$body.="<em>(None)</em>";
		}
	}
	
	$body.="</td></tr></table>";
	
	// Make sure that we send data only when the buffer is changed 
	// since the last time we sent the data
	$content_hash = md5($body.'|'.$head.'|'.$icon);
	if ($_SESSION[DATA]['sidebar-hash']!=$content_hash) {		
		relayMessage(MSG_INTERFACE, 'SIDEBAR', $icon, $head, $body);
		$_SESSION[DATA]['sidebar-hash']=$content_hash;
	}

	return true;
}

// This function resets content hash when the main interface is requested
registerEvent('sidebar_data_initialize', 'system.init_operation');
function sidebar_data_initialize($lastop, $newop) {
	if ($newop == 'interface.main') {
		$_SESSION[DATA]['sidebar-hash']='';
		return sidebar_data_feed();
	}
	return true;
}

?>