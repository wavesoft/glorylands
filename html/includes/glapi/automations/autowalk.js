/**
  * Automation - Walking
  *
  * Theese functions provide automated object walking with predefined path points.
  * They are called from the automations/core.js
  * 
  * See also:
  *   # automations/core.js
  *   # helpers/pathwalk.js
  */

/**
  * Autowalk object paths
  * Variable stricture:
  *
  * aw_paths[] = {
  *  	uid: <number>,				// Object UID (From the map system)
  *		path: <array> = [			// The object waypoints
  *				{x: <number>,		 // | The object
  *				 y: <number>,        // | coordinates
  *				 delay: <number>     // The time the object will wait before it moves to next waypoint
  *             },
  *				...
  *			  ],
  * 	current: <number>			// The current path index
  *		delay: <number>				// The delay counter (increments every second)
  *		speed:	<number>			// The object speed (used by the sprite animation functions)
  *  };
  *
  */  
var aw_paths = [];

/**
  * Register an object on the walk automation system
  *
  * This function creates a new entry on the aw_paths[] array.
  * The information inside this array are evaluated every second
  * by the auto_path_process() function.
  */
function auto_path_register(uid, path) {

	// Check for previous existance
	for (var i=0; i<aw_paths.length; i++) {
		if (aw_paths[i].uid = uid) {
			aw_paths[i].path = path;
			return true;
		}
	}
	
	// Not found, create new
	aw_paths.push({
		'path': path,
		'current': 0,
		'delay': 0,
		'speed': 3,
		'uid': uid
	});
	return true;
}

/**
  * Remove an object from the walk automation system
  */
function auto_path_unregister(uid) {
	for (var i=0; i<aw_paths.length; i++) {
		if (aw_paths[i].uid = uid) {
			aw_paths.splice(i,1);
			return true;
		}
	}
	return false;
}

/**
  * Process the paths for every registered object
  */
function auto_path_process() {
	for (var i=0; i<aw_paths.length; i++) {
		// Make sure object is not busy
		var id=map_object_index.indexOf(aw_paths[i].uid);
		var object = map_objects[id];
		if (object.busy) continue;
		
		// Increment step delay
		aw_paths[i].delay++;
		
		// If we reached the required delay for the next step, take it
		if (aw_paths[i].delay >= aw_paths[i].path[aw_paths[i].current].delay) {
			
			// Reset delay and move forward...
			aw_paths[i].delay = 0;
			
			// Forward to the next step
			aw_paths[i].current++;
			if (aw_paths[i].current >= aw_paths[i].path.length) aw_paths[i].current = 0;
			
			// Path-walk the object to the new position, based on
			// the collision information of the map

			//$debug('Walking to step '+aw_paths[i].current);
			pw_walk_to(
				aw_paths[i].uid, 
				aw_paths[i].path[aw_paths[i].current].x, 
				aw_paths[i].path[aw_paths[i].current].y,
				20 /* Calculate at least 20 tiles away, no matter what the speed is */
			);
		}
	}
}

/**
  * Register auto_path_process() to be executed every second
  */
setInterval(auto_path_process, 1000);
