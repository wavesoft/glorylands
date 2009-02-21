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

}

?>