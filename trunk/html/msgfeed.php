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

include "config/config.php";
include "engine/includes/base.php";

// We are currently in message feed system
define('IN_MSGFEED', true);

// Make sure session is valid
if (!isset($_SESSION[PLAYER][GUID])) die();

// User is active. Poll database to keep user online
gl_user_action();

// Inform any code that must send data now
callEvent('system.clientpoll');

// Send messages
echo json_encode(array('messages'=>gl_translate(jsonPopMessages(MSG_INTERFACE))));

?>