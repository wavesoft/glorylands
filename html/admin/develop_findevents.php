<?php
ob_implicit_flush();

global $events;
include "../config/config.php";
include "../config/diralias.php";

echo "<span id=\"waiter\" style=\"display:block;\">Please wait a moment. Filesystem is being searched...</span>";

$events = array();

function search_events($basedir) {
	global $events;
	$d = dir($basedir);
	while (false !== ($entry = $d->read())) {
		if (substr($entry,0,1)!='.') {
			$f = $basedir.'/'.$entry;
			if (is_dir($f)) {
				search_events($f);
			} else {
				if (substr($f,-4)=='.php') {
					$buf = file_get_contents($f);
					preg_match_all("/callEvent\(\'([\w.]+)\'/", $buf, $matches, PREG_SET_ORDER);
					foreach ($matches as $val) {
						array_push($events, array($val[1], $f));
					}
				}
			}
		}
	}
	$d->close();
}

search_events($_CONFIG[GAME][BASE]);
array_unique($events);
sort($events);

echo "<p>Currently declared system events:</p>";
echo "<table border=1>";
echo "<tr><th>Event Name</th><th>Declared in file</th><th>Hooked by</th></tr>\n";
foreach ($events as $event) {
	$file = $event[1];
	$file = str_replace(DIROF('SYSTEM.ENGINE'), '', $file);
	echo "<tr><td>".$event[0]."</td><td>".$file."</td>";
	$hb='';
	if (isset($EventChain[$event[0]])) {
		foreach ($EventChain[$event[0]] as $event) {
			if ($hb!='') $hb.=',';
			$hb.=$event[0];
		}
	}
	if ($hb=='') $hb='-';
	echo "<td>$hb</td></tr>\n";
}
echo "</table>";

/*
$html = "faef callEvent('system.init_operation', $last_operation, $operation); and(); then '' { some } other callEvent('system.init_operation', $last_operation, $operation); teasd";

preg_match_all("/callEvent\(\'([\w.]+)\'/", $html, $matches, PREG_SET_ORDER);

foreach ($matches as $val) {
    echo "matched: " . $val[0] . "\n";
    echo "part 1: " . $val[1] . "\n";
    echo "part 2: " . $val[3] . "\n";
    echo "part 3: " . $val[4] . "\n\n";
}
*/

?>
<script language="javascript">
var e = document.getElementById('waiter');
e.style.display = 'none';
</script>