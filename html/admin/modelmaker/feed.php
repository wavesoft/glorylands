<form name="form1" id="form1">
  <select name="menu1" onchange="window.location='feed.php?base='+this.value">
<?php
	$d = dir("../../images/tiles/");
	while (false !== ($entry = $d->read())) {
		if (substr($entry,-8)=='-0-0.gif') {
			$v = substr($entry,0,-8);
			echo "  	<option value=\"$v\">$v</option>\n";
		}
	}
	$d->close();
?>
  </select>
</form>
<?php

$base = 'z-castle-ext';
if (isset($_REQUEST['base'])) $base=$_REQUEST['base'];

echo "<table cellpadding=\"0\" cellspacing=\"0\">";
$y=0; $exists=true; $buf='';
while ($exists) {
	echo $buf."\n";
	$buf='<tr>';
	$exists=false;
	for ($x=0; $x<8; $x++) {
		$fl = $base.'-'.$x.'-'.$y.'.gif';
		$f = '../../images/tiles/'.$fl;
		if (file_exists($f)) $exists.=true;
		$buf.='<td><a target="main" href="javascript:selectTile(\''.$fl.'\');"><img onmousemove="this.border=1" onmouseout="this.border=0" border="0" src="'.$f.'" /></a><td>';
	}
	$buf.='</tr>';
	$y++;
}
echo "</table>";

?>