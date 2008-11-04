<?php
// ===========================================
//   PROJECT: The Glory Lands MMORPG Game
// ===========================================
//   Version: v0.1 Beta
//	   Class: Output processor
//      File: Full-page HTML output processor
//                   _______
// _________________| TO DO |_________________
//  -
// ___________________________________________
//   (C) Copyright 2007, John Haralampidis
// ===========================================

// Check for interface availability
if (!file_exists(DIROF('DATA.INTERFACE')."{$act_interface}.tpl")) {
	echo "Not found : ".DIROF('DATA.INTERFACE')."{$act_interface}.tpl";
	return false;
}

// Load and init Smarty(R) Engine
include DIROF('OUTPUT.FILE')."interfaces/libs/Smarty.class.php";
$smarty = new Smarty;
$smarty->template_dir = DIROF('DATA.INTERFACE',true);
$smarty->compile_dir = DIROF('OUTPUT.PROCESSOR')."interfaces/cache";
$smarty->config_dir = DIROF('DATA.LANG');
$smarty->compile_check = true;
$smarty->debugging = false;
$smarty->config_load($_CONFIG[GAME][LANG].'.dat');

// Assign required variables
$smarty->assign($act_result);
$smarty->assign('operation', $act_operation);
$smarty->assign('interface', $act_interface);
$smarty->assign('CONFIG', $_CONFIG);
$smarty->assign('VERSION', $_VER);
$smarty->assign('images', DIROF('DATA.IMAGE',true));
$smarty->assign('theme', 'themes/'.$_CONFIG[GAME][THEME]);

// If result is not valid, display the standard error message
if (!$act_valid) {	
	$smarty->assign('error', 'You have no permission to execute this operation!');
	$smarty->display("general_error.tpl");
	return;
}

// Find out if there are any interfaces assigned to this action
$modules = array();
$ans=$sql->query("SELECT
				`interface_module_assign`.`position`,
				`interface_modules`.`filename`,
				`interface_modules`.`index`
				FROM
				`interface_modules`
				Inner Join `interface_module_assign` ON `interface_module_assign`.`module` = `interface_modules`.`index`
				WHERE
				`interface_module_assign`.`action` =  '{$operation}'
				ORDER BY
				`interface_module_assign`.`weight` ASC
				");
				
if ($ans && !$sql->emptyResults) {
	$qres='';
	while ($mod = $sql->fetch_array_fromresults($ans, MYSQL_ASSOC)) {
	
		// Render module and stack result on buffer
		ob_start();
		include DIROF('DATA.MODULE').'mod_'.$mod['filename'].'/render.php';
		if (!isset($modules[$mod['position']])) $modules[$mod['position']]='';
		$modules[$mod['position']] .= ob_get_contents();
		ob_end_clean();
		
		// Prepare the query to process afterwards to include any other required resources
		if ($qres!='') $qres.=' OR ';
		$qres.="`module` = '".mysql_escape_string($mod['index'])."'";
	}
		
	// Obdain any other resources
	$resources=array('javascript'=>'', 'stylesheet'=>'', 'header'=>'', 'footer'=>'');
	$ans=$sql->query("SELECT * FROM `interface_module_resources` WHERE $qres");
	while ($row = $sql->fetch_array_fromresults($ans, MYSQL_ASSOC)) {
		foreach ($_CONFIG[DIRS][ALIAS] as $key => $path) {
			if ($path!='') $row['filename'] = str_replace('{'.$key.'}', $_CONFIG[GAME][REF_URL].$path ,$row['filename']);
		}
		if ($row['mode']=='CSS') $resources['stylesheet'].="<link href=\"{$row['filename']}\" rel=\"stylesheet\" type=\"text/css\" />\n";
		if ($row['mode']=='JS') $resources['javascript'].="<script language=\"javascript\" src=\"{$row['filename']}\"></script>\n";
	}
}
$smarty->assign($resources);
$smarty->assign('modules', $modules);

$smarty->display("{$act_interface}.tpl");

// In case of global debug, display the debug console
if (defined('GLOB_DEBUG')) {
?>
<div class="debug" style="width: 100%; border: dashed 2px #666666; background-color: #E9E9E9; color: #333333; font-family: Arial, Helvetica, sans-serif; font-size: 10px;">
<a href="javascript:void(0);" onclick="var e=document.getElementById('debug_console'); if (e.style.display){e.style.display=''}else{e.style.display='none'};">Toggle Debug Console</a>
<pre id="debug_console" style="display: none;">
<?php echo $sql->getQueries() ?>
</pre>
</div>
<?php
}

?>