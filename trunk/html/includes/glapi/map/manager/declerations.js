/***********************************
   Map Rendering System
************************************/

var map_dynamic_objects = [];	// Delay-load objects
var map_objects = [];			// All the objects
var map_object_index = [];		// Holds the unique IDs for the previous array
var map_info = [];				// The map information
var map_back = [];				// Background objects
var map_curtain_status = false;	// The last status of the map curtain
var map_curtain_fx = null;		// This holds the last instance of the curtain Fx class - Used to stop animation
var map_scroll_pos = {x:0,y:0};	// The current scroll position
var map_last_id = 1;			// Used to provide unique IDs while storing objects
var map_viewpoint = {x:0,y:0};	// The center of the current view
var map_center_fx = null;		// This holds the last instance of the center Fx class - Used to stop animation
var map_current = '';			// The currently active MAP
var map_playeruid = 0;			// The player's object UniqueID
