<?php
global $_CONFIG;

# Constants
define('DIRS','DIRS');
define('ALIAS','ALIAS');
define('NAMES','NAMES');

# Define directory alias info
$_CONFIG[DIRS][ALIAS] = array(

	'ACTION.MANIFEST' 	=> "/data/engine/actions.manifest",
	'ACTION.LIBRARY' 	=> "/data/engine/actions.lib",
	'DATA.HOOK'			=> "/data/engine/actions.hook",
	'ACTION' 			=> "/data/engine/actions",
	'OUTPUT.PROCESSOR' 	=> "/engine/outputprocessors",
	'OUTPUT.FILE' 		=> "/engine/outputprocessors",
	'IMAGE.CHAR' 		=> "/images/chars",
	'IMAGE.ELEMENT' 	=> "/images/elements",
	'IMAGE.INVENTORY' 	=> "/images/inventory",
	'IMAGE.PLAYER' 		=> "/images/players",
	'IMAGE.PORTRAIT' 	=> "/images/portraits",
	'IMAGE.SIGHTSEEN' 	=> "/images/sightseens",
	'IMAGE.UI' 			=> "/images/ui",
	'IMAGE.TILE' 		=> "/images/tiles",
	'INTERFACE.INCLUDE'	=> "/includes",
	'INTERFACE.THEME'	=> "/themes",
	'DOCUMENTATION'		=> "/doc",
	'DATA.LANG'			=> "/data/lang",
	'DATA.MAP'			=> "/data/maps",
	'DATA.MODEL'		=> "/images/elements",
	'DATA.ENGINE'		=> "/engine/data",
	'DATA.INTERFACE'	=> "/data/interfaces",
	'DATA.MODULE'		=> "/data/modules",
	'DATA.IMAGE'		=> "/images",
	'DATA'				=> "/data",
	'SYSTEM.MANAGER' 	=> "/engine/managers",
	'SYSTEM.INCLUDE' 	=> "/engine/includes",
	'SYSTEM.ENGINE' 	=> "/engine",
	'SYSTEM.ADMIN' 		=> "/admin",
	'SYSTEM' 			=> ""					/* Note: blank entries must be to the end! */

	);

# Define directory alias names
$_CONFIG[DIRS][NAMES] = array(

	'ACTION.MANIFEST'	=> "Action Profile",
	'ACTION.LIBRARY'	=> "Action Library",
	'SYSTEM.MANAGER'	=> "URI Manager",
	'SYSTEM.ENGINE'		=> "Engine module",
	'SYSTEM.INCLUDE'	=> "Engine include",
	'SYSTEM.ADMIN'		=> "System administration",
	'SYSTEM'			=> "System file",
	'ACTION.INCLUDE'	=> "Engine Library",
	'ACTION' 			=> "Action",			/* Note: class root name always to the end! */
	'OUTPUT.PROCESSOR'	=> "Output processor",
	'OUTPUT.FILE'		=> "Output system file",
	'IMAGE.CHAR'		=> "Charachter Image",
	'IMAGE.ELEMENT'		=> "Gameobject Element Image",
	'IMAGE.INVENTORY'	=> "Inventory item Image",
	'IMAGE.PLAYER'		=> "Player image",
	'IMAGE.PORTRAIT'	=> "NPC Portrait image",
	'IMAGE.SIGHTSEEN'	=> "Sightseen Image",
	'IMAGE.UI'			=> "User Interface Image",
	'IMAGE.TILE'		=> "Map tile",
	'INTERFACE.INCLUDE'	=> "Interface Element",
	'INTERFACE.THEME'	=> "Interface Theme",
	'DOCUMENTATION'		=> "Documentation",
	'DATA.LANG'			=> "Language Data",
	'DATA.MAP'			=> "Map Data",
	'DATA.MODEL'		=> "Model Data",
	'DATA.ENGINE'		=> "Engine Data",
	'DATA.INTERFACE'	=> "Interface Data",
	'DATA.MODULE'		=> "Interface Module",
	'DATA.HOOK'			=> "Action message hook",
	'DATA.IMAGE'		=> "Generic Image File",
	'DATA'				=> "Game Data"
	
	);
	
## Some macros to obadin the folder base
function DIROF($part, $notrail=false) {
	global $_CONFIG;
	$place='';
	if (substr($part,-1)=='S') $part = substr($part, 0, -1); /* Remove 'S' in the end (ex. IMAGES instead of IMAGE) */
	if (isset($_CONFIG[DIRS][ALIAS][$part])) $place = $_CONFIG[DIRS][ALIAS][$part];
	if (!$notrail) $place.='/';
	return $_CONFIG[GAME][BASE].$place;
}
function DESCOF($part) {
	global $_CONFIG;
	$name='Unknown';
	if (substr($part,-1)=='S') $part = substr($part, 0, -1); /* Remove 'S' in the end (ex. IMAGES instead of IMAGE) */
	if (isset($_CONFIG[DIRS][NAMES][$part])) $name = $_CONFIG[DIRS][NAMES][$part];
	return $_CONFIG[GAME][NAMES].$name;
}
?>