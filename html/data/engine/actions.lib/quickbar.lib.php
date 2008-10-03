<?php

function qb_update_hostview($host, $viewmode) {
	gl_do($viewmode, array('guid' => $host));
}

function qb_update_view() {
	global $sql;
	
	$slot = array();
	$ans=$sql->query("SELECT * FROM `mod_quickbar_slots` WHERE `player` = ".$_SESSION[PLAYER][GUID]);
	while ($row = $sql->fetch_array_fromresults($ans)) {
		$vars = gl_get_guid_vars($row['guid']);
		$slot[$row['slot']] = "<img src=\"images/".$vars['icon']."\" onload=\"qb_makeqbutton(this, ".$row['guid'].", ".$row['slot'].");\" title=\"".$vars['name']."\" />";
	}
	
	// Build data
	$data = '
	<table id="quickbar">
		<tr>
			<td><div slot="1">'.$slot[1].'</div></td>
			<td><div slot="2">'.$slot[2].'</div></td>
			<td><div slot="3">'.$slot[3].'</div></td>
			<td><div slot="4">'.$slot[4].'</div></td>
			<td><div slot="5">'.$slot[5].'</div></td>
			<td><div slot="6">'.$slot[6].'</div></td>
			<td><div slot="7">'.$slot[7].'</div></td>
			<td><div slot="8">'.$slot[8].'</div></td>
			<td><div slot="9">'.$slot[9].'</div></td>
			<td><div slot="10">'.$slot[10].'</div></td>
		</tr>
	</table>
	';
	
	// Feed data
	relayMessage(MSG_INTERFACE, 'QBAR', $data);
}

?>