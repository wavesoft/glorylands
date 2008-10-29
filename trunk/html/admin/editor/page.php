<?php
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>GloryLands :: Content Editor</title>
<link rel="stylesheet" type="text/css" href="theme/style.css">
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
include 'pages/'.$page.'.php';
?>
</body>
</html>
