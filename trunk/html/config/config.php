<?php
global $_CONFIG;

## Constants
define('DB','DB');
define('HOST','HOST');
define('PASSWORD','PASSWORD');
define('DATABASE','DATABASE');
define('USER','USER');
define('GAME','GAME');
define('TITLE','TITLE');
define('THEME','THEME');
define('CHARSET','CHARSET');
define('BASE','BASE');
define('REF_URL','REF_URL');
define('TUNE','TUNE');
define('MAP_GRID_W','MAP_GRID_W');
define('MAP_GRID_H','MAP_GRID_H');
define('INDX_INTERFACE', 'INDX_INTERFACE');

define('LANG','LANG');
### Uncomment the next line to enable debugging
//define('GLOB_DEBUG', true);

### The database configuration
$_CONFIG[DB][HOST] 			= "localhost";
$_CONFIG[DB][DATABASE] 		= "glory";
$_CONFIG[DB][USER] 			= "glory";
$_CONFIG[DB][PASSWORD] 		= "lands";

### Global game information
$_CONFIG[GAME][LANG] 		= "en";
$_CONFIG[GAME][TITLE] 		= "Glory Lands";
$_CONFIG[GAME][CHARSET] 	= "iso-8859-7";
$_CONFIG[GAME][THEME] 		= "default";
$_CONFIG[GAME][BASE] 		= "C:/xampp/htdocs/gl-sf";
$_CONFIG[GAME][REF_URL] 	= "/gl-sf";
$_CONFIG[GAME][INDX_INTERFACE] = 'interface.entry';

### Tuning variables
$_CONFIG[TUNE][MAP_GRID_W]	= 24;
$_CONFIG[TUNE][MAP_GRID_H]	= 16;
?>