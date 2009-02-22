<?php

include "../../config/config.php";
include "../../engine/includes/base.php";

$a = '';
if (isset($_REQUEST['a'])) $a=$_REQUEST['a'];

if ($a == 'save') {

	file_put_contents('saved.txt', stripslashes($_REQUEST['json']));
	echo "{}";

} elseif ($a == 'load') {

	echo file_get_contents('saved.txt');

} elseif ($a == 'images') {

	$filter = $_REQUEST['f'];
	$files = array();
	
	if (!isset($_REQUEST['searchcache'])) {	
		if ($_REQUEST['searchcache']['filter'] != $filter) {

			// Convert wildcards to regex
			$save_filter = $filter;
			$filter = addslashes($filter);
			$filter = str_replace('.','\\.', $filter);
			$filter = str_replace('$','\\$', $filter);
			$filter = str_replace('%','\\%', $filter);
			$filter = str_replace('^','\\^', $filter);
			$filter = str_replace('[','\\[', $filter);
			$filter = str_replace(']','\\]', $filter);
			$filter = str_replace('{','\\{', $filter);
			$filter = str_replace('}','\\}', $filter);
			$filter = str_replace('+','\\+', $filter);
			$filter = str_replace('*','(.*)', $filter);
			$filter = str_replace('?','.', $filter);
			$filter = "/{$filter}/i";
		
			$d = dir(DIROF('IMAGE.INVENTORY',true));
			while (false !== ($entry = $d->read())) {
				if (!is_dir(DIROF('IMAGE.INVENTORY').$entry)) {
					if (preg_match($filter, $entry)) {
						$files[]=$entry;
					}
				}
			}
			$d->close();
			$_REQUEST['searchcache'] = array(
				'filter' => $save_filter,
				'results' => $files
			);
		} else {
			$files=$_REQUEST['searchcache']['results'];
		}
	} else {
		$files=$_REQUEST['searchcache']['results'];
	}
	
	// Paging
	if (isset($_REQUEST['p'])) {
		$start = $_REQUEST['p']*50;
		if ($start>=sizeof($files)) $start=sizeof($files);
		if ($start<0) $start=0;		
		echo json_encode(array_splice($files, $start, 50));
	} else {
		echo json_encode($files);
	}

}

?>