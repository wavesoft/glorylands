/**
  * Javascript port of the PHP Click2Go Script
  *
  * This script contains all the calculations required to
  * find a walking path inside the current map's collision grid.
  *
  */

var c2g_path = [];
var c2g_log = '';

function c2g_distance(x1,y1,x2,y2) {
	return Math.sqrt(Math.pow((y2-y1),2)+Math.pow((x2-x1),2));
}

function c2g_sort_function(a, b) {
    if (a[2] == b[2]) return 0;
    return (a[2] < b[2]) ? -1 : 1;
}

function c2g_sort_directions(from_x, from_y, to_x, to_y, directions) {	
	// Store the distances for all the directions
	for (i=0; i<directions.length; i++) {
		directions[i][2] = c2g_distance(from_x+directions[i][0], from_y+directions[i][1], to_x, to_y);
	}
	// Sort the directions, based on the distance
	directions.sort(c2g_sort_function);
}

function c2g_pathwalk(from_x, from_y, to_x, to_y, speed, parent_x, parent_y) {
	if (parent_x===false) parent_x = from_x;
	if (parent_y===false) parent_y = from_y;
	
	c2g_log += "Entering "+from_x+","+from_y+" with speed "+speed+"\n";
	
	////// Check if we have enough speed left //////
	grid = map_info.grid;
	if (!grid[from_y]) return false; // Cannot enter
	if (!grid[from_y][from_x]) return false; // Cannot enter
	attennuation = grid[from_y][from_x];
	speed -= attennuation;
	
	// If the speed is excausted, consider it as the final position
	// Prepare the return stack and quit
	if (speed <= 0) {
		c2g_log += "Speed exhausted. Returning ("+from_x+","+from_y+")\n";
		return [{x:from_x,y:from_y}];
	} else {
		c2g_log += "Still here with speed "+speed+"\n";	
	}
	
	////// Initialize Directions //////
	directions = [
		              [0,-1],
		   [-1,0] ,              [1,0],
		              [0,1] 
	];
	direction_count = directions.length;
	c2g_sort_directions(from_x, from_y, to_x, to_y, directions);		
	c2g_log += "Directions sorted:"+$trace(directions)+"\n";
	
	////// Start walking towards (to_x,to_y), using the best directions //////
	for (i=0; i<direction_count; i++) {
		test_x = from_x+directions[i][0];
		test_y = from_y+directions[i][1];
		
		// If the next step is the target, we are done!
		if ((test_x == to_x) && (test_y == to_y)) {
			c2g_log += "Reached the end. Returning ("+to_x+","+to_y+"), ("+from_x+", "+from_y+")\n";
			return [{x:to_x, y:to_y}, {x:from_x, y:from_y}];
		}
		
		// If we are about to enter the place we came from, proceed to the next		
		if ((test_x == parent_x) && (test_y == parent_y)) continue;
		
		// Try to walk this direction
		result = c2g_pathwalk(test_x, test_y, to_x, to_y, speed, from_x, from_y);
		
		// If the next step successfully reaches the target, stack
		// our position on the return stack and quit
		if (result !== false) {
			c2g_log += "Found completion. Returning ("+from_x+","+from_y+")\n";
			result.push({x:from_x, y:from_y});
			return result;
		}
	}
	
	// If we reach this point.. something was not successfull...
	return false;
}

/**
  * Pathwalk thread function
  */
function pw_walk_to(uid, to_x, to_y, range, mark) {
	var id=map_object_index.indexOf(uid);
	var object = map_objects[id];
	
	if (!range) range=5;
	
	//$debug('Path-walking object '+uid+' ('+object.info.x+','+object.info.y+') => ('+to_x+','+to_y+')');
	
	c2g_path = [];
	c2g_log = '';
	var path = c2g_pathwalk(
		Number(object.info.x), Number(object.info.y), 
		to_x, to_y, 
		range, 
		false, false
	);
	
	if (!path) {
		showStatus('<span style="color: red">Cannot access that point!</span>',1000);
		return;
	}
	
	// Mark the area we will enter
	/*
	if (mark) {		
		var z=$('datapane').getStyle('z-index');
		for (var i=0; i<path.length; i++) {
			marker_set('path', path[i].x*32, path[i].y*32, 'images/UI/area/green-path.png', z);
		}
	}
	*/
	
	object.info.x = path[0].x;
	object.info.y = path[0].y;
	object.info.fx_move = 'path';
	object.info.fx_path = path.reverse();	

	map_updateobject(uid, object.info);
}
