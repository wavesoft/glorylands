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
$_CONFIG[DB][HOST] 			= "{#DB_HOST#}";
$_CONFIG[DB][DATABASE] 		= "{#DB_DATABASE#}";
$_CONFIG[DB][USER] 			= "{#DB_USER#}";
$_CONFIG[DB][PASSWORD] 		= "{#DB_PASSWORD#}";

### Global game information
$_CONFIG[GAME][LANG] 		= "{#GAME_LANG#}";
$_CONFIG[GAME][TITLE] 		= "{#GAME_TITLE#}";
$_CONFIG[GAME][CHARSET] 	= "{#GAME_CHARSET#}";
$_CONFIG[GAME][THEME] 		= "default";
$_CONFIG[GAME][BASE] 		= "{#GAME_BASE#}";
$_CONFIG[GAME][REF_URL] 	= "{#GAME_REF_URL#}";
$_CONFIG[GAME][INDX_INTERFACE] = 'interface.entry';

### Tuning variables
$_CONFIG[TUNE][MAP_GRID_W]	= 24;
$_CONFIG[TUNE][MAP_GRID_H]	= 16;
?>