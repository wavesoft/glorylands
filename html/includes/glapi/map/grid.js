// ======================================================
//  Theese functions handle the main grid data
// ======================================================

var data_grid=false;		// Data Grid 2D Array
var collision_grid=false;	// Attennuation (Collision) grid 2D Array
var data_dictionary=false;	// Translate dictionary 2D Array
var grid_range=false;		// Grid RECT 1D Array
var overlay_grid = false;	// Overlaied objects 2D array
var nav_grid = false;		// Navigation (hover) grid 2D array, including dictionary
var current_map = "";		// Currently loaded map name
var glob_x_base = 0;		// \ 
var glob_y_base = 0;		// -- Top-left map offset corner
var grid_x=0, grid_y=0;		// [IN] Render Coordinates

// Parameters to send on displayBuffer:
var grid_display = {'rollback': false, 'head_link': false, 'head_image': false, 'title': false, 'background': 'none.gif'};
// Float RECT dimensions, offset point, the request URL to send when user clicks somewere and the flag to dispose rect if clicked:
var rectinfo = {w:3,h:3,bx:1,by:2,url:'',clickdispose:false,silent:false};	
