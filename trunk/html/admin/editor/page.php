<?php
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
if (!isset($_SESSION[PLAYER])) { header("Location: index.php"); die("Unauthorized"); };

// Some helping functions used by pages
define(ACCESS_LV1,1);
define(ACCESS_LV2,2);
define(ACCESS_LV3,4);
define(ACCESS_LV4,8);
define(ACCESS_ALL,15);
define(ACCESS_ADMIN,     ACCESS_LV1);
define(ACCESS_MODERATOR, ACCESS_LV1 | ACCESS_LV2);
define(ACCESS_EDITOR,    ACCESS_LV1 | ACCESS_LV2 | ACCESS_LV3);
define(ACCESS_USER,      ACCESS_LV1 | ACCESS_LV2 | ACCESS_LV3 | ACCESS_LV4);
function access_check($level) {
	// Check if the execution is valid
	if (!defined('IN_PAGE')) {
		header('Location: ../page.php?page=err_unauthorized');
		die('Out of use space');
		return;
	}

	// Calculate access based on the level
	$access = false;
	$user_level = $_SESSION[PLAYER][PROFILE]['level'];
	if (($level&ACCESS_LV1)!=0) $access=$access||($user_level=='ADMIN');
	if (($level&ACCESS_LV2)!=0) $access=$access||($user_level=='EDITOR');
	if (($level&ACCESS_LV3)!=0) $access=$access||($user_level=='MODERATOR');
	if (($level&ACCESS_LV4)!=0) $access=$access||($user_level=='USER');
	if (!$access) {
		header('Location: page.php?page=err_unauthorized');
		die('Unauthorized');
		return;
	}
}

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
