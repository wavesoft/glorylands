/**
  * Path animation (updateobject Helper)
  *
  * This function moves a map object object on the
  * continuous position given in the grid
  *
  * The directional variable can be 0,1 or 2 and it means:
  *  0 - The object is not directional
  *  1 - The object is 4-side directional (Front,Back,Left,Right)
  *  2 - The object is 8-side directional (The previous four plus the diagonial four)
  *
  */
var map_fx_pathmove_stack_id = [];
var map_fx_pathmove_stack = [];

function map_fx_pathmove(object, path, completed_callback) {
	// This function is used to prohibit multiple requests for
	// animation on the same object.
	// This function just chains the concurrent requests and handles
	// it, only when the previouse ones are completed
	
	var i = map_fx_pathmove_stack_id.indexOf(object.info.id);	
	if (i < 0) {
		map_fx_pathmove_stack_id.push(object.info.id);
		map_fx_pathmove_stack.push([ [object,path] ]);
		map_fx_pathmove_next(object.info.id, completed_callback);
	} else {
		map_fx_pathmove_stack[i].push([object,path]);
	}
}

function map_fx_pathmove_single(object, path, completed_callback) {
	// Same as map_fx_pathmove, but does not stack the events.
	// If more than one exists, it cancels the previous

	var i = map_fx_pathmove_stack_id.indexOf(object.info.id);	
	if (i < 0) {
		map_fx_pathmove_stack_id.push(object.info.id);
		map_fx_pathmove_stack.push([ [object,path] ]);
		map_fx_pathmove_next(object.info.id, completed_callback);
	} else {
		map_fx_pathmove_stack[i].push([object,path]);
	}
}


function map_fx_pathmove_next(id, completed_callback) {
	//$debug('[path] ('+id+') Next called!');
	var i = map_fx_pathmove_stack_id.indexOf(id);
	if (i>-1) {
		if (map_fx_pathmove_stack[i].length == 0) {
			//$debug('[path] ('+id+') No more. Erasing...!');
			map_fx_pathmove_stack.splice(i,1);
			map_fx_pathmove_stack_id.splice(i,1);
			if (completed_callback) completed_callback();
		} else {
			//$debug('[path] ('+id+') We have '+map_fx_pathmove_stack[i].length+' to do');
			var f = map_fx_pathmove_stack[i].shift();
			//$debug('[path] ('+id+') Running '+f[0]+' with '+$trace(f[1]));
			map_fx_pathmove_thread(f[0], f[1], completed_callback);
		}
	}
}
function map_fx_pathmove_build_spritepath(object_info, facing_side) {
	// Default direction grid
	// This one maps side-ID to row number
	var dirgrid = {
		'rd': 3,
		'ru': 3,
		'r': 3,
		'ld': 1,
		'lu': 1,
		'l': 1,
		'u': 2,
		'd': 0
	};
	// Default animation columns
	var ani = {
		'walk': [1,2,3,4,5],
		'stay': 0
	};
	
	/** 
	  * Sample animation sprite, as defined from the default configuration:
	  *
	  *       0     1     2     3     4
	  *    +-----+-----+-----+-----+-----+-----+
	  *  0 |stand|walk1|walk2|walk3|walk4|walk5|  - Facing down
	  *    +-----+-----+-----+-----+-----+-----+
	  *  1 |stand|walk1|walk2|walk3|walk4|walk5|  - Facing up
	  *    +-----+-----+-----+-----+-----+-----+
	  *  2 |stand|walk1|walk2|walk3|walk4|walk5|  - Facing left
	  *    +-----+-----+-----+-----+-----+-----+
	  *  3 |stand|walk1|walk2|walk3|walk4|walk5|  - Facing right
	  *    +-----+-----+-----+-----+-----+-----+
	  *
	  */
	
	// Check if the object contains custom coordinates
	if ($defined(object_info.info.sprite_direction_grid)) dirgrid=object_info.info.sprite_direction_grid;
	if ($defined(object_info.info.sprite_direction_ani)) ani=object_info.info.sprite_direction_ani;
	
	// Detect the row ID, based on the facing side
	var row=0;
	if (!$defined(dirgrid[facing_side])) {
		if (facing_side.length == 2) {
			// For example: If not found 'rb' => Check for 'r' only
			if ($defined(dirgrid[facing_side[0]])) {
				row = dirgrid[facing_side[0]];
			}
		}
	} else {
		row = dirgrid[facing_side];
	}

	// Create the frames based on the information above
	var frames=[];
	for (var i=0; i<ani.walk.length; i++) {
		frames.push([ani.walk[i], row]);
	}
	
	// Find the standing frame
	var stand=[ani.stay, row];
	
	// Return the animation frames and the standing frame
	return [frames, stand];
}
function map_fx_pathmove_thread(object_info, path, completed_callback) {
	var i=0;
	var spath;
	var last_stand_dir = [0,0];
	var id = object_info.info.id;
	var object = object_info.object;
	var last_ani_dir = '';
	var ani_dir = '';
	var directional = 0;
	if ($defined(object_info.info.directional)) directional=object_info.info.directional;

	// Speed is the tiles the player can enter per move
	var speed = 5;
	if ($defined(object_info.info.speed)) speed=object_info.info.speed;	
	var enter_interval = 1000-(speed*100); if (enter_interval<10) enter_interval=10;
	var animation_fps = speed*2; if (animation_fps<1) animation_fps=1;
	
	var px_transition=new Fx.Morph(object, {duration: enter_interval, unit: 'px', transition: Fx.Transitions.linear});
	var walk_step = function() {
		//$debug('[path] ('+id+') Thread');
				
		// Check if we have more steps to go
		if (!$defined(path[i])) {
			fx_sprite_stop(object,last_stand_dir);	
			map_fx_pathmove_next(id,completed_callback);
			object_info.busy = false;
			return;
		}
		var j=i;
		i++;

		// Calculate previous and next position
		if (j<1) {
			var info = $(object).getStyles('left','top');
			var from_x = Math.round(info.left.toInt()/32);
			var from_y = Math.round(info.top.toInt()/32);
		} else {
			var from_x = path[j-1].x;
			var from_y = path[j-1].y;
		}
		var to_x = path[j].x;
		var to_y = path[j].y;						
		var dir_x = to_x - from_x;
		var dir_y = to_y - from_y;

		//$debug('Waking to '+to_x+','+to_y+' from '+from_x+','+from_y);
		if ((dir_x == 0) && (dir_y == 0)) {
			walk_step();
			return;
		}

		// If this object is directional, calculate it's direction
		// and update the image
		if (directional) {
			
			var dir = false; // Defaults
			last_stand_dir = [0,0];

			if (dir_x>0) {
				if (dir_y>0) {
					// Right-Down
					spath = map_fx_pathmove_build_spritepath(object_info, 'rd');
					dir = spath[0]; last_stand_dir = spath[1];
					ani_dir = 'rd';
				} else if (dir_y<0) {
					// Right-Up
					spath = map_fx_pathmove_build_spritepath(object_info, 'ru');
					dir = spath[0]; last_stand_dir = spath[1];
					ani_dir = 'ru';
				} else {
					// Right
					spath = map_fx_pathmove_build_spritepath(object_info, 'r');
					dir = spath[0]; last_stand_dir = spath[1];
					ani_dir = 'r';
				}
			} else if (dir_x<0) {
				if (dir_y>0) {
					// Left-Down
					spath = map_fx_pathmove_build_spritepath(object_info, 'ld');
					dir = spath[0]; last_stand_dir = spath[1];
					ani_dir = 'ld';
				} else if (dir_y<0) {
					// Left-Up
					spath = map_fx_pathmove_build_spritepath(object_info, 'lu');
					dir = spath[0]; last_stand_dir = spath[1];
					ani_dir = 'lu';
				} else {
					// Left
					spath = map_fx_pathmove_build_spritepath(object_info, 'l');
					dir = spath[0]; last_stand_dir = spath[1];
					ani_dir = 'l';
				}
			} else {
				if (dir_y>0) {
					// Down
					spath = map_fx_pathmove_build_spritepath(object_info, 'd');
					dir = spath[0]; last_stand_dir = spath[1];
					ani_dir = 'd';
				} else if (dir_y<0) {
					// Up
					spath = map_fx_pathmove_build_spritepath(object_info, 'u');
					dir = spath[0]; last_stand_dir = spath[1];
					ani_dir = 'u';
				} else {
				}
			}
			
			// Animate the object only if the animation is changed
			if (dir != false) {
				if (last_ani_dir != ani_dir) {
					fx_sprite_animate(object, animation_fps, dir);
					last_ani_dir = ani_dir;
				}
			}
		}
		
		// Calculate new Z-Index
		var dim = object.getSize();
		var zYp = Math.round(dim.y/32);
		var zindex = (Number(path[j].y)+zYp)*500+Number(path[j].x);
		if (zindex<0) zindex=1;
		
		// Update z index
		object.setStyle('z-index',zindex);
		
		// Move object
		var obj_x = path[j].x*32;
		var obj_y = path[j].y*32-dim.y+32;
		px_transition.start({
			'left': obj_x,
			'top': obj_y
		}).chain(walk_step);
		
		//marker_remove('path', path[j].x*32, path[j].y*32);
	}

	// Wheck if we are repeating the same path
	// (Checking if the last position is the current position)
	var info = $(object).getStyles('left','top');
	var dim = $(object).getSize();
	var from_x = Math.round(info.left.toInt()/32);
	var from_y = Math.round(info.top.toInt()/32);
	var to_x = path[path.length-1].x;
	var to_y = path[path.length-1].y-1;
	if ((from_x == to_x) && (from_y == to_y)) {
		return;
	}
	
	// Start walking
	object_info.busy = true;
	walk_step();
}
