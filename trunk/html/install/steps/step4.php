<h2>Installing database</h2>
<p>Installing database. Please wait a moment...</p>
<div class="separator">Installation console</div>
<pre>
<?php

// Start using GloryLands SQL from now on
include "../config/config.php";
include "../engine/includes/base.php";
global $sql;

// Failures storage
$failures = array();
global $failures;

// Local function to run a PHP script
function sql_run($file) {
	global $failures, $sql;

	// Prepare and run the database qureies
	$queries = file_get_contents($file);
	$queries = mb_ereg_replace('/\\*[^\\*]*\\*/', "", $queries);
	$queries = mb_ereg_replace('-- [^\\r\\n]*\\r?\\n', "", $queries);
	$queries = mb_split(";\\r?\\n", $queries);
	
	$q_count = 0;
	$q_step = 0;
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
	echo "Running query ".$q_count." of ".$q_count." (100%)...\n";
}

// Check if we have patch or install mode
if ($_SESSION['dbmode'] == 'patch') {

	echo "Patching database <b>".$_CONFIG[DB][DATABASE]." from revision ".$_SESSION['dbrev']."</b> to latest. This might take a while..\n\n";

	// Search for patches and build patch filename tree
	$patches = array();
	$d = dir("data/sql");
	while (false !== ($entry = $d->read())) {
		if (strtolower(substr($entry,-4))=='.sql') {
			if (strtolower(substr($entry,0,6)) == 'patch-') {
				$rev = (int) substr(substr($entry,6),0,-4);
				if ($rev > $_SESSION['dbrev']) {
					$patches[]=$entry;
				}
			}
		}
	}
	$d->close();
	sort($patches);
	
	if (sizeof($patches) == 0) {
		echo "No patching required. Your database is up to date!\n";
	} else {		
		// Run patches	
		foreach ($patches as $patch) {
			echo "Importing patch <b>$patch</b>...\n";
			sql_run("data/sql/$patch");
		}
	}

} else {

	echo "Importing data into <b>".$_CONFIG[DB][DATABASE].". This might take a while..</b>\n\n";
	sql_run("data/sql/dbschema.sql");
	
}

// Check and show status
if (sizeof($failures)>0) {
	echo "\nOperation completed with ".sizeof($failures)." failures\n";
} else {
	echo "\nOperation completed successfully!\n";
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