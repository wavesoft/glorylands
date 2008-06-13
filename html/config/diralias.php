<?php
global $_CONFIG;

# Constants
define(DIRS,'DIRS');
define(ALIAS,'ALIAS');
define(NAMES,'NAMES');

# Define directory alias info
$_CONFIG[DIRS][ALIAS] = array(

	'ACTION.MANIFEST' 	=> "/data/engine/actions.manifest",
	'ACTION.LIBRARY' 	=> "/data/engine/actions.lib",
	'ACTION' 			=> "/data/engine/actions",
	'SYSTEM.MANAGER' 	=> "/engine/managers",
	'SYSTEM.INCLUDE' 	=> "/engine/includes",
	'SYSTEM.ENGINE' 	=> "/engine",
	'SYSTEM' 			=> "",
	'OUTPUT.PROCESSOR' 	=> "/engine/outputprocessors",
	'OUTPUT.FILE' 		=> "/engine/outputprocessors",
	'IMAGE.CHAR' 		=> "/images/chars",
	'IMAGE.ELEMENTS' 	=> "/images/elements",
	'IMAGE.INVENTORY' 	=> "/images/inventory",
	'IMAGE.PLAYER' 		=> "/images/players",
	'IMAGE.PORTRAITS' 	=> "/images/portraits",
	'IMAGE.SIGHTSEEN' 	=> "/images/sightseens",
	'IMAGE.UI' 			=> "/images/ui",
	'IMAGE.TILE' 		=> "/images/tiles",
	'IMAGE.TILES' 		=> "/images/tiles",
	'INTERFACE.INCLUDE'	=> "/includes",
	'INTERFACE.THEME'	=> "/themes",
	'DOCUMENTATION'		=> "/doc",
	'DATA.MAP'			=> "/data/maps",
	'DATA.MODEL'		=> "/data/models",
	'DATA.ENGINE'		=> "/engine/data",
	'DATA.INTERFACE'	=> "/data/interfaces",
	'DATA.MODULE'		=> "/data/modules",
	'DATA.HOOK'			=> "/data/engine/actions.hook",
	'DATA.IMAGE'		=> "/images"

	);

# Define directory alias names
$_CONFIG[DIRS][NAMES] = array(

	'ACTION.MANIFEST'	=> "Action Profile",
	'ACTION.LIBRARY'	=> "Action Library",
	'SYSTEM.MANAGER'	=> "URI Manager",
	'ACTION.INCLUDE'	=> "Engine Library",
	'ACTION' 			=> "Action",			/* Note: class root name always to the end! */
	'SYSTEM.ENGINE'		=> "Engine module",
	'SYSTEM'			=> "System file",
	'OUTPUT.PROCESSOR'	=> "Output processor",
	'OUTPUT.FILE'		=> "Output system file",
	'IMAGE.CHAR'		=> "Charachter Image",
	'IMAGE.ELEMENT'		=> "Gameobject Element Image",
	'IMAGE.INVENTORY'	=> "Inventory item Image",
	'IMAGE.PLAYER'		=> "Player image",
	'IMAGE.PORTRAITS'	=> "NPC Portrait image",
	'IMAGE.SIGHTSEEN'	=> "Sightseen Image",
	'IMAGE.UI'			=> "User Interface Image",
	'IMAGE.TILE'		=> "Map tile",
	'IMAGE.TILES'		=> "Map tile",
	'INTERFACE.INCLUDE'	=> "Interface Element",
	'INTERFACE.THEME'	=> "Interface Theme",
	'DOCUMENTATION'		=> "Documentation",
	'DATA.MAP'			=> "Map Data",
	'DATA.MODEL'		=> "Model Data",
	'DATA.ENGINE'		=> "Engine Data",
	'DATA.INTERFACE'	=> "Interface Data",
	'DATA.MODULE'		=> "Interface Module",
	'DATA.HOOK'			=> "Action message hook",
	'DATA.IMAGE'		=> "Generic Image File"
	
	);
	
## Some macros to obadin the folder base
function DIROF($part, $notrail=false) {
	global $_CONFIG;
	$place='';
	if (isset($_CONFIG[DIRS][ALIAS][$part])) $place = $_CONFIG[DIRS][ALIAS][$part];
	if (!$notrail) $place.='/';
	return $_CONFIG[GAME][BASE].$place;
}
?>