<?php
global $_CONFIG;

## Constants
define('DB','DB');
define('MCACHE','MCACHE');
define('HOST','HOST');
define('PORT','PORT');
define('ENABLE','ENABLE');
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
$_CONFIG[DB][DATABASE] 		= "glorylands";
$_CONFIG[DB][USER] 			= "glory";
$_CONFIG[DB][PASSWORD] 		= "lands";

### Memory cache configuration
$_CONFIG[MCACHE][ENABLE] 	= false;
$_CONFIG[MCACHE][HOST] 		= "10.110.17.67";
$_CONFIG[MCACHE][PORT] 		= 11211;

### Global game information
$_CONFIG[GAME][LANG] 		= "en";
$_CONFIG[GAME][TITLE] 		= "Glory Lands";
$_CONFIG[GAME][CHARSET] 	= "utf-8";
$_CONFIG[GAME][THEME] 		= "default";
$_CONFIG[GAME][BASE] 		= "C:/xampp/htdocs/gl-sf";
$_CONFIG[GAME][REF_URL] 	= "http://localhost/gl-sf";
$_CONFIG[GAME][INDX_INTERFACE] = 'interface.entry';

### Tuning variables
$_CONFIG[TUNE][MAP_GRID_W]	= 24;
$_CONFIG[TUNE][MAP_GRID_H]	= 16;
?>
