<?php

################# CERTAIN DYNAMIC CONTENT ##################

// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
// always modified
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
// HTTP/1.0
header("Pragma: no-cache");

// Include/Create environment
include "../../config/config.php";
include "../../engine/includes/base.php";
include "../../engine/includes/map_db.php";

// Check for correct parameters
if (!isset($_REQUEST['e'])) die('101 Engine not specified');
$engine = $_REQUEST['e'];
if (!file_exists($_CONFIG[GAME][BASE]."/admin/engine/ajax-engines/{$engine}.php")) die('102 Engine not found ');
if (!isset($_REQUEST['a'])) die('103 Action not specified');
global $action;
$action = $_REQUEST['a'];

// Process action
global $res_num, $res_str;
$res_num = 0;
$res_str = "";

ob_start();
include "ajax-engines/{$engine}.php";
$buf = ob_get_flush();
ob_end_clean();

echo $res_num." ".$res_str;

?>