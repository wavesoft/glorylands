<?php
// ===========================================
//   PROJECT: The Glory Lands MMORPG Game
// ===========================================
//   Version: v0.1 Beta
//      File: Session management & environment
//            variables
//                   _______
// _________________| TO DO |_________________
//  -
// ___________________________________________
//   (C) Copyright 2007, John Haralampidis
// ===========================================


### Sesssion-wide constants
define(PLAYER,"PLAYER");
define(GUID,"GUID");

### Start Session
session_start();

### Initialization
$_SESSION[PLAYER][GUID] = 1292;

?>