<?php

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

global $authorized;
$authorized = false;
function access_check($level) {
	global $authorized;
	
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

	// Notify page.php that we are now authorized
	$authorized = true;
}

?>