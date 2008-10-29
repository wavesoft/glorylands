<?php
include "../../config/config.php"; 
include "../../engine/includes/base.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-7" />
<title>GloryLands :: Content Editor :: Navigator</title>
<link rel="stylesheet" type="text/css" href="theme/style.css">
<script language="javascript" src="../../includes/mootools-1.11.js"></script>
<script language="javascript">
var v_page = '';
// ### DEBUG FUNCTIONS ###

function $trace(obj) {
	ans='';
	$each(obj, function(value, name) {
		if (ans!='') ans+=', ';
		ans+='['+name+'] = '+value;
	});	
	return ans;
}
</script>
</head>
<body class="nav">
<form target="main" action="page.php" onsubmit="if(v_page)window.location='nav.php?page='+v_page">
<?php 
global $MENU;
include "data/menus.php";

// Load menu
$render_menu = array();
if (isset($_REQUEST['page']) && $_REQUEST['page']!='home') {
	$render_menu = $MENU[$_REQUEST['page']]['submenu'];
	if (isset($MENU[$_REQUEST['page']]['customnav'])) {
		array_unshift($render_menu, array(
			'type' => 'subnav',
			'file' => $MENU[$_REQUEST['page']]['customnav']
		));
	}
	array_unshift($render_menu, array(
		'text' => 'Back',
		'page' => 'home',
		'icon' => '1leftarrow.png',
		'submenu' => true
	), $MENU[$_REQUEST['page']]
	, array(
		'type' => 'spacer'
	));
	
	// Save all the custom parameters we have received as FORM elements
	foreach ($_REQUEST as $id => $value) {
		if (($id!='page') && ($id!='PHPSESSID')) {
			echo "<input type=\"hidden\" name=\"{$id}\" value=\"{$value}\" />\n";
		}
	}

} else {
	$render_menu = $MENU;
}

// Render menu
foreach ($render_menu as $key => $menu) {
	$type='menu';
	if (isset($menu['type'])) $type=$menu['type'];
	if ($type=='menu') {
?>
<button class="button" style="width: 100%" <?php if (isset($menu['submenu'])) { echo 'onclick="v_page=this.value;"'; } else { echo 'onclick="v_page=false;"'; } ?> type="submit" name="page" value="<?php echo $menu['page']; ?>">
<?php if (isset($menu['icon'])) { ?> <img src="theme/icons/<?php echo $menu['icon']; ?>" align="absmiddle" /> <?php } ?><?php echo $menu['text']; ?>
</button>
<?php
	} elseif ($type=='spacer') {
?>
<br />
<?php
	} elseif ($type=='separator') {
?>
<hr />
<?php
	} elseif ($type=='subnav') {
		include "navigators/".$menu['file'].".php";
	}
}
?>
</form>
</body>
</html>