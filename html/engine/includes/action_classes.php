<?php
// ===========================================
//   PROJECT: The Glory Lands MMORPG Game
// ===========================================
//   Version: v0.1 Beta
//      File: All the helper classes required
//			  by the actions. Every class that
//			  is listed here is instanced by 
//		   	  the actionprocess script	
//                   _______
// _________________| TO DO |_________________
//  1) Implement hpr_players.inArea()
// ___________________________________________
//   (C) Copyright 2007, John Haralampidis
// ===========================================

// Player information class
class hpr_players {

	// Return an array with user info keys that
	// represent all the players in the specified area
	// starting from ($x,$y) and having width $w and 
	// height $h on the map $map;
	// The return array is in format:
	//  [x pos][y pos][counter] = <info array>
	//
	function inArea($x,$y,$w,$h,$map) {
		$players = array();
		$players[20][21][0][name] = "John";
		$players[20][21][0][id] = 14;
		$players[20][23][1][name] = "Sofia";
		$players[20][23][1][id] = 462;
		return $players;
	}

}

// Owner player information
class hpr_player {
	
	// Return True if the user's quest is active
	function questActive($questid) {
		return false;
	}
}
?>