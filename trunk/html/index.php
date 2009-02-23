<?php
/*
    GloryLands, a Web-Based, Massive Multiplayer Online RPG/Strategy Game
    Copyright (C) 2008  John Haralampidis <johnys2@gmail.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Check for configuration file
if (!file_exists("config/config.php")) {
	echo "<h2>GloryLands Web-Based MMORPG</h2>";
	echo "<p>Configuration file is missing! This probably means that the game is not properly installed!<br />Please use the game installer located under the <b>/install</b> subdirectory to install the game.</p>";
	echo "<p><small>Licenced under the GNU/GPL Licence. Author: John Haralampidis</small></p>";
	die();
}

// Require configuration
require_once "config/config.php";
require_once "config/diralias.php";

// Do we have a clean index request?
if (( (substr($_SERVER['REQUEST_URI'],-9) == 'index.php') || 
	  (substr($_SERVER['REQUEST_URI'],-1) == '/') )
 	   && !isset($_REQUEST['a'])) {
	// Load default index interface
	$_REQUEST['a'] = $_CONFIG[GAME][INDX_INTERFACE];
}

// Process the event
require_once "engine/eventprocess.php";

?>