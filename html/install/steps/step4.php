<h2>Installing database</h2>
<p>Installing database. Please wait a moment...</p>
<div class="separator">Installation console</div>
<pre>
<?php

// Start using GloryLands SQL from now on
include "../config/config.php";
include "../engine/includes/base.php";
global $sql;

echo "Importing data into <b>".$_CONFIG[DB][DATABASE]."</b>\n\n";

// Prepare and run the database qureies
$queries = file_get_contents("data/sql/dbschema.sql");
$queries = mb_ereg_replace('/\\*[^\\*]*\\*/', "", $queries);
$queries = mb_ereg_replace('-- [^\\r\\n]*\\r?\\n', "", $queries);
$queries = mb_split(";\\r?\\n", $queries);

$q_count = 0;
$q_step = 0;
$failures = array();
foreach ($queries as $query) {
	$query=trim($query);
	if ($query!='') {
		$ans = $sql->query($query);
		if (!$ans) {
			$failures[]=$sql->getError();
		}
		$q_count++;
		$q_step++;
		if ($q_step >= 10) {
			$q_step=0;
			$perc = round(100*$q_count/sizeof($queries));
			echo "Running query ".$q_count." of ".sizeof($queries)." ({$perc}%)...\n";
		}
	}
}

if (sizeof($failures)>0) {
	echo "Import completed with ".sizeof($failures)." failures\n";
} else {
	echo "Import completed successfully!\n";
}
?>
</pre>
<?php
if (sizeof($failures)>0) {
?>
<div class="separator">Failures</div>
<p>There were errors while performing the installation queries. Your installation might be incomplete unless those problems are solved. If you are sure about the installation state, you can try to continue, but it is strongly recommended to go back and run the database installation again.</p>
<p>
<ul>
<?php
	foreach ($failures as $failure) {
		echo "<li>".$failure."</li>\n";
	}
?>
</ul>
</p>
<?php
}
?>
<p><?php if (sizeof($failures)>0) { ?><a href="?step=3" class="button">&lt;&lt; Back</a> <?php } ?><a href="?step=5" class="button">Next &gt;&gt;</a></p>