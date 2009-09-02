/**
  * Place/Update dynamic map objects
  *
  * This function places new dynamic objects on the map or
  * removes or updates old ones.
  *
  * The changes are detected using differential check with
  * the data previously passed on the function. 
  * The primary key for the difference check operation is the
  * 'id' fiend of the data entries
  *
  */
function map_updatedata(data) {
	
	// We don't have previous data?
	if (map_dynamic_objects.length == 0) {
		
		// Store the data provided
		map_dynamic_objects = data;
		
		// And place them on map
		$each(data, function(e,k) {
			map_dynamic_objects[k].index = map_addobject(e);
		});
	
	// We DO have previous data?
	} else {
		
		// Perform a differential check and find
		// removed, new and changed objects
		var ignore = []
		var removed = [];
		var changed = [];
				
		// Check removed or changed
		for (var i=0; i<map_dynamic_objects.length; i++) {
			var found = false;
			var idcheck = false;
			for (var j=data.length-1; j>=0; j--) {
				// Do we have an ID? Use ID as difference check key...
				if ($defined(data[j].id) && $defined(map_dynamic_objects[i].id)) {
					if (data[j].id == map_dynamic_objects[i].id) {
						// We found it
						found = true;				
						// So, ignore this item
						ignore.push(j);
						// But hold the object to check differencies
						idcheck=data[j];
						break;
					}
				} else {
					// No ID? Use [x,y,image] as difference check key...
					if ((data[j].x == map_dynamic_objects[i].x) && (data[j].y == map_dynamic_objects[i].y) && (data[j].image == map_dynamic_objects[i].image)) {
						// We found it
						found = true;
						// So, ignore this item
						ignore.push(j);
						break;
					}
				}
			}
			
			if (!found) {
				// Not found on new data? The object is removed
				removed.push(map_dynamic_objects[i].index);
			} else if (idcheck!=false) {
				// Update dynamic objects
				// (We cannot know if an object has actually changed or not, since
				//  not all of the variables are used by the map system)
				changed.push({index:map_dynamic_objects[i].index, cdata:idcheck});
			}
		}
		
		// [ Now that we are out of loops and locked arrays, perform all the required operations ]
				
		// #1) Remove all disposed objects
		for (var i=removed.length-1; i>=0; i--) {
			map_removeobject(removed[i]);
		}

		// Reset map_dynamic_objects and prepare it for new stacking
		map_dynamic_objects = [];

		// #2) Update all altered objects
		for (var i=0; i<changed.length; i++) {
			changed[i].cdata.index = changed[i].index;
			map_dynamic_objects.push(changed[i].cdata);

			map_updateobject(changed[i].index,changed[i].cdata);			
		}

		// #3) Create all new items
		//     Note: All what is left on the data array are new items
		for (var i=0; i<data.length; i++) {
			if (ignore.indexOf(i)<0) {
				var dat = data[i];
				var index = map_addobject(dat);
				dat.index = index;
				map_dynamic_objects.push(dat);
			}
		}

	}
	
}

/**
  * Place a map object
  *
  * This function places a new object on the map grid
  *
  */
function map_addobject(data) {
	// Store default values if something is missing
	if (!$defined(data.cx)) data.cx=0;
	if (!$defined(data.cy)) data.cy=0;
	
	// Callback to alter/edit object data
	callback.call('object_put', data);
	
	// Create and insert image
	//var im = $(document.createElement('img'));
	//im.src = data.image;	
	var im = ImageLoader.get(data.image);
	$('datapane').appendChild(im);	

	//$debug('Inserting object: '+$trace(data));

	// If the image is sprite, convert the image to sprite
	if ($defined(data.sprite)) im = fx_sprite_prepare(im, data.sprite[0],data.sprite[1]);
	var size = im.getSize();

	// Re-map x-y
	var x=data.x*32-data.cx;
	var y=data.y*32-data.cy-size.y;

	// Calculate new Z-Index
	var zindex = (data.y-1)*500+x;
	if (zindex<0) zindex=1;

	// Apply image styles
	im.setStyles({
		'position': 'absolute',
		'left': x,
		'top': y,
		'z-index': zindex
	});
		
	// Cache all the instances
	var uid=map_last_id++;
	var id=map_objects.length;
	map_object_index[id]=uid;
	map_objects[id]={
		'info': data,
		'x': x,
		'y': y,
		'width': size.x,
		'height': size.y,
		'cx': data.cx,
		'cy': data.cy,
		'object': im
	}
	
	// Check if this object is the player, and update UID value
	if ($defined(data.player)) map_playeruid=uid;	

	// If we have automation information, process the automation system now
	if ($defined(data.automate)) {
		auto_setup_object(uid, data);
	}

	// If the object is dynamic, append triggers and allow advanced show effects
	if (data.dynamic) {
		
		// Hide object, if it's out of our visible range
		if (!map_object_is_visible(map_objects[id])) {
			map_objects[id].object.setStyle('display','none');
		}

		// Add event handlers
		im.addEvent('contextmenu', function(e) {
			var e = new Event(e);
			map_objecttrigger(uid, 'contextmenu', e);						
			e.stop();
		});
		im.addEvent('mousemove', function(e) {
			var e = new Event(e);
			im.setStyles({'opacity':0.7});
			map_objecttrigger(uid, 'mousemove', e);
			e.stop();
		});
		im.addEvent('mouseout', function(e) {
			var e = new Event(e);
			im.setStyles({'opacity':1});
			map_objecttrigger(uid, 'mouseout', e);
			e.stop();
		});
		im.addEvent('mousedown', function(e) {
			var e = new Event(e);
			map_objecttrigger(uid, 'mousedown', e);
			e.stop();
		});
		im.addEvent('mouseup', function(e) {
			var e = new Event(e);
			map_objecttrigger(uid, 'mouseup', e);
			e.stop();
		});
		im.addEvent('click', function(e) {
			var e = new Event(e);
			map_objecttrigger(uid, 'click', e);
			e.stop();
		});
		
		// Handle display effect
		if ($defined(data.fx_show)) {						
			switch (data.fx_show) {
				case 'fade':
					var imfx=new Fx.Morph(im, {wait: false, duration: 400, transition: Fx.Transitions.Quad.easeIn});
					im.setStyles({
						'opacity':0
					});
					imfx.start({
						'opacity':1		   
					});
					break;
				
				case 'pop':
					var imfx=new Fx.Morph(im, {wait: false, duration: 400, transition: Fx.Transitions.Back.easeOut});
					im.setStyles({
						'opacity':0,
						'top':y+32
					});
					imfx.start({
						'opacity':1,
						'top':y
					});
					break;

				case 'drop':
					var imfx=new Fx.Morph(im, {wait: false, duration: 400, transition: Fx.Transitions.Bounce.easeOut});
					im.setStyles({
						'opacity':0,
						'top':y-200
					});
					imfx.start({
						'opacity':1,
						'top':y
					});
					break;				

				case 'zoom':
					var imfx=new Fx.Morph(im, {wait: false, duration: 400,transition: Fx.Transitions.Quad.easeIn});
					im.setStyles({
						'opacity':0,
						'width': 5,
						'height': 5,
						'left': (x+(size.x/2)),
						'top': (y+(size.y/2))
					});
					imfx.start({
						'opacity':1,
						'width': size.x,
						'height': size.y,
						'left': x,
						'top': y
					});
					break;				
			}
		}

		// If we must focus on this item, do it now
		if ($defined(data.focus)) {
			map_center(x+Math.ceil(size.x/2),y+Math.ceil(size.y/2),true);
		}
	}
	
	// Return the new structure
	return uid;
}

/**
  * Remove an object from map
  *
  * This function removes an object from map.
  * It also performs disposal animation, based on the information
  * stored in the map_objects array
  *
  */
function map_removeobject(uid, nofx) {
	var id=map_object_index.indexOf(uid);
	var data = map_objects[id];
	if (!data) return;

	callback.call('object_remove', data.info);

	// If we have automation information, process the automation cleanups now
	if ($defined(map_objects[id].info.automate)) {
		auto_remove_object(uid, data);
	}

	// Function that will be chained or directly
	// executed and will remove the object
	var disposer = function() {
		// Remove the object
		data.object.dispose();
		map_objects.splice(id,1);
		map_object_index.splice(id,1);
	}
	
	// Do the dispose effect (if specified)
	if (data.info.fx_hide && !nofx) {
		switch (data.info.fx_hide) {
			case 'fade':
				var imfx=new Fx.Morph(data.object, {wait: false, duration: 400, transition: Fx.Transitions.Quad.easeOut});
				imfx.start({
					'opacity':0
				}).chain(disposer);
				break;
			
			case 'pop':
				var imfx=new Fx.Morph(data.object, {wait: false, duration: 400, transition: Fx.Transitions.Back.easeIn});
				imfx.start({
					'opacity':0,
					'top':data.y+32
				}).chain(disposer);
				break;

			case 'drop':
				var imfx=new Fx.Morph(data.object, {wait: false, duration: 400, transition: Fx.Transitions.Back.easeIn});
				imfx.start({
					'opacity':0,
					'top':data.y-200
				});
				break;

			case 'zoom':
				var imfx=new Fx.Morph(data.object, {wait: false, duration: 400,transition: Fx.Transitions.Quad.easeIn});
				imfx.start({
					'opacity':0,
					'width': 5,
					'height': 5,
					'left': (data.x+(data.width/2)),
					'top': (data.y+(data.height/2))
				});
				break;				

			default:
				disposer();
		}
	} else {
		disposer();	
	}
}

/**
  * Update a map object
  *
  * This function updates a map object. If the object contains
  * animation effects, they will be applied after the local data
  * cache update
  *
  */
function map_updateobject(uid,data) {
	try {
	var id=map_object_index.indexOf(uid);
	var old_data = map_objects[id];
	if (!old_data) {
		//$debug('Data mapping not found for UID '+uid);
		return;
	}
	
	// Callback notification
	callback.call('object_update', {'old':old_data, 'new':data});
	//$debug('Altering '+uid+' using data :'+"\n"+$trace(data));

	// Store default values if something is missing
	if (!$defined(data.cx)) data.cx=old_data.cx;
	if (!$defined(data.cy)) data.cy=old_data.cy;
	if (!$defined(data.x)) data.x=old_data.info.x;
	if (!$defined(data.y)) data.y=old_data.info.y;
		
	// If image is changed, perform update
	if ($defined(data.image)) {
		if (old_data.info.image != data.image) {		
			// If the old image is sprite...
			if ($defined(old_data.info.sprite)) {
				// But is no more.. Remove the sprite and update image
				if (!$defined(data.sprite)) {
					old_data.object = fx_sprite_undo(old_data.object);
					old_data.object.src = data.image;
	
				// Elseways, update the sprite image and dimensions
				} else {
					fx_sprite_update(old_data.object, ImageLoader.get(data.image), data.sprite[0],data.sprite[1]);
				}
			} else {
				// Not sprite? Update image...
				old_data.object.src = data.image;
			}				
		}	
	}

	// If automation is removed, cleanup automation. Elseways, update
	if (!$defined(data.automate) && $defined(old_data.info.automate)) {
		auto_remove_object(uid, old_data.info);		
	} else if ($defined(data.automate) && !$defined(old_data.info.automate)) {
		auto_setup_object(uid, data);		
	} else if ($defined(data.automate)) {
		auto_update_object(uid, data, old_data.info);
	}

	// Copy each missing value from old data, here
	for (i in old_data.info) {
		if (!$defined(data[i])) data[i] = old_data.info[i];
	}

	// Re-map x-y
	var size = old_data.object.getSize();
	var x=data.x*32-data.cx;
	var y=data.y*32-data.cy-size.y;

	// Calculate new Z-Index
	var zindex = (data.y-1)*500+x;
	if (zindex<0) zindex=1;

	// Update cache
	old_data.x = x;
	old_data.y = y;
	old_data.width = size.x;
	old_data.height = size.y;
	old_data.info = data;

	// Check if this object is the player, and update UID value
	if ($defined(data.player)) {
		map_playeruid=uid;	
		cursor_blink(0, data.x*32, data.y*32);
	}

	// Stack the transition, including all the variables used,
	// for chained execution	
	map_stack_movefx(uid, old_data, data, x, y, zindex, size);
	
	// If we must focus on this item, do it now
	if ($defined(data.focus)) {
		map_center(x+Math.ceil(old_data.width/2),y+Math.ceil(old_data.height/2),true);
	} else {
		
		//  Update trim of invisible objects
		// (Since map_center function updates the trim, do not call it twice)
		map_trim();
	}		
	
	
	} catch (e) {
		alert('Error updating UID '+uid+': '+$trace(e));
	}
	
	// Javascript uses byRef for
	// objects. That means the stored
	// object in the array is now updated
}