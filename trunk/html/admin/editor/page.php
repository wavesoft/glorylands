<?php
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
if (!isset($_SESSION[PLAYER])) { header("Location: index.php"); die("Unauthorized"); };

// Helping functions
include "tools/auth.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>GloryLands :: Content Editor</title>
<link rel="stylesheet" type="text/css" href="theme/style.css">
<script src="tools/codepress/codepress.js" type="text/javascript"></script>
<script src="../../includes/mootools-release-1.11.js.js" type="text/javascript"></script>
</head>
<body>
<?php
define('IN_PAGE',true);

// Find out what page to display
$page = 'home';
if (isset($_REQUEST['page'])) {
	if (file_exists('pages/'.$_REQUEST['page'].'.php')) {
		$page=$_REQUEST['page'];
	} else {
		$page='err_notfound';
	}
}

// Show the page
ob_start();
include 'pages/'.$page.'.php';
if (!$authorized) {
	ob_end_clean();
	echo "<div class=\"msg_warn\">This page does not contain authorization information. By default, access to this page is denied!</div>";
} else {
	ob_end_flush();
}
?>
</body>
</html>
